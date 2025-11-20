<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected function createUser()
    {
        return User::factory()->create();
    }

    protected function createPlan()
    {
        return SubscriptionPlan::create([
            'name_ar' => 'الخطة الأساسية',
            'name_en' => 'Basic Plan',
            'slug' => 'basic',
            'description_ar' => 'خطة أساسية',
            'description_en' => 'Basic plan',
            'price' => 9.99,
            'currency' => 'USD',
            'billing_period' => 'monthly',
            'trial_days' => 7,
            'max_profiles' => 2,
            'max_devices' => 2,
            'video_quality' => 'hd',
            'download_enabled' => true,
            'ads_enabled' => false,
            'live_tv_enabled' => false,
            'is_active' => true,
        ]);
    }

    public function test_can_list_active_plans()
    {
        $plan = $this->createPlan();

        $response = $this->getJson('/api/v1/plans');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name_en',
                        'name_ar',
                        'price',
                        'currency',
                        'billing_period',
                    ],
                ],
            ]);
    }

    public function test_can_view_single_plan()
    {
        $plan = $this->createPlan();

        $response = $this->getJson("/api/v1/plans/{$plan->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $plan->id,
                    'name_en' => 'Basic Plan',
                    'price' => 9.99,
                ],
            ]);
    }

    public function test_authenticated_user_can_create_subscription()
    {
        $user = $this->createUser();
        $plan = $this->createPlan();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/subscriptions', [
                'plan_id' => $plan->id,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'subscription' => [
                    'id',
                    'status',
                    'plan',
                ],
            ]);

        $this->assertDatabaseHas('user_subscriptions', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'pending',
        ]);
    }

    public function test_user_cannot_create_duplicate_active_subscription()
    {
        $user = $this->createUser();
        $plan = $this->createPlan();

        // Create first subscription
        UserSubscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'amount' => $plan->price,
            'currency' => $plan->currency,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => $plan->price,
            'starts_at' => Carbon::now(),
            'ends_at' => Carbon::now()->addMonth(),
        ]);

        // Try to create second subscription
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/subscriptions', [
                'plan_id' => $plan->id,
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'You already have an active subscription',
            ]);
    }

    public function test_can_validate_coupon()
    {
        $user = $this->createUser();
        $plan = $this->createPlan();

        $coupon = Coupon::create([
            'code' => 'TESTCODE',
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'starts_at' => Carbon::now()->subDay(),
            'expires_at' => Carbon::now()->addMonth(),
            'usage_limit' => 100,
            'usage_limit_per_user' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/coupons/validate', [
                'code' => 'TESTCODE',
                'plan_id' => $plan->id,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'valid' => true,
                'discount_calculation' => [
                    'original_amount' => 9.99,
                    'discount_amount' => 1.998,
                ],
            ]);
    }

    public function test_cannot_validate_expired_coupon()
    {
        $user = $this->createUser();
        $plan = $this->createPlan();

        $coupon = Coupon::create([
            'code' => 'EXPIRED',
            'discount_type' => 'fixed',
            'discount_value' => 5,
            'starts_at' => Carbon::now()->subMonth(),
            'expires_at' => Carbon::now()->subDay(),
            'is_active' => true,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/coupons/validate', [
                'code' => 'EXPIRED',
                'plan_id' => $plan->id,
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'valid' => false,
            ]);
    }

    public function test_can_get_active_subscription()
    {
        $user = $this->createUser();
        $plan = $this->createPlan();

        UserSubscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'amount' => $plan->price,
            'currency' => $plan->currency,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => $plan->price,
            'starts_at' => Carbon::now(),
            'ends_at' => Carbon::now()->addMonth(),
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/subscriptions/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'subscription' => [
                    'id',
                    'status',
                    'plan',
                ],
            ]);
    }
}

