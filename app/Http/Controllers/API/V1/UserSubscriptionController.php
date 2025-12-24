<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Payments;
use App\Models\SubscriptionPlan;
use App\Models\SystemSetting;
use App\Models\UserSubscription;
use App\Services\Billing\PaylinkGateway;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Stevebauman\Location\Facades\Location;

class UserSubscriptionController extends Controller
{
    use ApiResponse;
    protected $setting;
    public function __construct()
    {
        $this->setting = SystemSetting::where('key', 'site_tax')->first();
    }

    // ==============================
    // عرض الاشتراك الحالي
    // GET /api/v1/user_subscription/me
    // ==============================
    public function index()
    {
        $user = Auth('sanctum')->user();
        $items = $user->subscriptions()->with('plan')->get();
        if ($items->isEmpty()) {
            return $this->error("No  Subscription Found", 404);
        }

        return $this->success([
            "subscription" => $items,
        ], "Get  Subscription Successfully");
    }

    // ==============================
    // إضافة / ترقية اشتراك
    // POST /api/v1/user_subscription
    // ==============================
    public function store(Request $request)
    {
        $user = Auth('sanctum')->user();
        if (!$user) return $this->error("Unauthenticated user", 401);

        $plan = SubscriptionPlan::find($request->plan_id);
        if (!$plan) return $this->error("Subscription Plan Not Found", 404);

        $now = now();

        DB::beginTransaction();
        try {
            // ===== تحقق من وجود اشتراك pending سابق =====
            $existingPending = UserSubscription::where('user_id', $user->id)
                ->where('status', 'pending')
                ->first();
            if ($existingPending) {
                DB::rollBack();
                return $this->error("You already have a pending subscription. Please complete the payment first.", 422);
            }

            $current = UserSubscription::where('user_id', $user->id)
                ->whereIn('status', ['active', 'trial', 'pending', 'suspended'])
                ->latest('starts_at')
                ->first();

            // ===== تحديد السعر حسب الدولة =====
            $ip = $request->ip() === '127.0.0.1' || $request->ip() === '::1' ? '8.8.8.8' : $request->ip();
            $position = Location::get($ip);
            $userCountryCode = $position ? $position->countryCode : 'EG';

            $amountToCharge = $plan->price;
            $countryPrice = $plan->countryPrices()->whereHas('country', function ($q) use ($userCountryCode) {
                $q->where('code', $userCountryCode);
            })->first();
            if ($countryPrice) $amountToCharge = $countryPrice->price_sar;

            // ===== نفس الخطة =====
            if ($current && in_array($current->status, ['active', 'trial', 'pending', 'suspended']) && $current->plan_id == $plan->id) {
                DB::rollBack();
                return $this->success([
                    'subscription' => $current,
                    'amount_to_charge' => 0
                ], "You already have a subscription for this plan");
            }

            // ===== ترقية لخطة أخرى =====
            $upgradeAmount = $amountToCharge;
            if ($current && in_array($current->status, ['trial', 'pending', 'suspended']) && $current->plan_id != $plan->id) {
                $upgradeAmount = $amountToCharge;
            }

            if ($current && $current->status == 'active') {
                $startsAt = Carbon::parse($current->starts_at);
                $endsAt = Carbon::parse($current->ends_at);

                $remainingDays = $now->diffInDays($endsAt, false);
                $totalDays = max(1, $startsAt->diffInDays($endsAt));

                $diffPrice = $amountToCharge - $current->plan->price;
                $upgradeAmount = ($diffPrice > 0 && $remainingDays > 0) ? round($diffPrice * ($remainingDays / $totalDays), 2) : 0;
            }

            // ===== حساب الضريبة =====
            $taxPercent = $this->setting->value ?? 0;
            $taxAmount = round($upgradeAmount * $taxPercent / 100, 2);
            $totalAmount = $upgradeAmount + $taxAmount;

            // ===== إنشاء الاشتراك الجديد بالـ pending =====
            $subscription = UserSubscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => $plan->trial_days > 0 ? 'trial' : 'pending',
                'starts_at' => now(),
                'trial_ends_at' => $plan->trial_days > 0 ? $now->addDays($plan->trial_days) : null,
                'ends_at' => $now->addMonth(),
                'amount' => $upgradeAmount,
                'tax_amount' => $taxAmount,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
                'currency' => $plan->currency,
                'payment_method' => 'telr',
                'auto_renew' => true
            ]);

            DB::commit();

            return $this->success([
                'subscription' => $subscription->load('plan'),
                'amount_to_charge' => $totalAmount
            ], "Subscription processed successfully. Payment pending.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error("Something went wrong: " . $e->getMessage(), 500);
        }
    }

    // ==============================
    // إلغاء الاشتراك
    // POST /api/v1/user_subscription/cancel
    // ==============================
    public function cancel_user_subscription(Request $request, $user_sub_id)
    {
        $user = Auth('sanctum')->user();
        if (!$user) return $this->error("Unauthenticated user", 401);

        $current = UserSubscription::where('id', $user_sub_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$current) {
            return $this->error("No subscription found", 404);
        }

        // ===== منع إلغاء الاشتراك المنتهي =====
        if ($current->ends_at && $current->ends_at < now()) {
            return $this->error("Cannot cancel a subscription that has already ended.", 422);
        }

        $current->update([
            'status' => 'canceled',
            'canceled_at' => now(),
            'cancellation_reason' => $request->reason ?? null
        ]);

        return $this->success([
            'subscription' => $current
        ], "Subscription canceled successfully");
    }

   // /api/subscriptions/check-quality
    public function checkQuality(Request $request)
    {
        $request->validate([
            'requested_quality' => 'required|in:sd,hd,uhd',
        ]);

        $user = Auth('sanctum')->user();
        if (!$user) return $this->error("Unauthenticated user", 401);

        // جلب اشتراك المستخدم
        $subscription = UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->first();

        if (!$subscription) {
            return response()->json([
                'status' => false,
                'message' => 'User has no active subscription.',
                'allowed' => false
            ]);
        }

        $plan = $subscription->plan; // العلاقة Subscription belongsTo SubscriptionPlan

        // الجودة المسموحة بالخطة
        $allowedQuality = $plan->video_quality;

        // ترتيب الجودة من الأضعف للأعلى
        $qualityOrder = [
            'sd' => 1,
            'hd' => 2,
            'uhd' => 3
        ];

        $requested = $request->requested_quality;

        $isAllowed = $qualityOrder[$requested] <= $qualityOrder[$allowedQuality];

        return response()->json([
            'status' => true,
            'message' => $isAllowed ? 'Quality allowed.' : 'Quality not allowed for this plan.',
            'data' => [
                'requested_quality' => $requested,
                'plan_quality' => $allowedQuality,
                'allowed' => $isAllowed,
            ]
        ]);
    }
}
