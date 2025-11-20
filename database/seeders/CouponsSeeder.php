<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;
use App\Models\SubscriptionPlan;
use Carbon\Carbon;

class CouponsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // General 20% discount coupon
        Coupon::create([
            'code' => 'WELCOME20',
            'discount_type' => 'percentage',
            'discount_value' => 20.00,
            'starts_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMonths(3),
            'usage_limit' => 100,
            'usage_limit_per_user' => 1,
            'times_used' => 0,
            'is_active' => true,
            'plan_id' => null, // Valid for all plans
            'metadata' => [
                'description' => 'Welcome discount for new users',
                'campaign' => 'new_user_welcome',
            ],
        ]);

        // Fixed $5 discount coupon
        Coupon::create([
            'code' => 'SAVE5',
            'discount_type' => 'fixed',
            'discount_value' => 5.00,
            'starts_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMonth(),
            'usage_limit' => 50,
            'usage_limit_per_user' => 1,
            'times_used' => 0,
            'is_active' => true,
            'plan_id' => null,
            'metadata' => [
                'description' => 'Save $5 on any plan',
            ],
        ]);

        // Premium plan specific 30% discount
        $premiumPlan = SubscriptionPlan::where('slug', 'premium')->first();
        if ($premiumPlan) {
            Coupon::create([
                'code' => 'PREMIUM30',
                'discount_type' => 'percentage',
                'discount_value' => 30.00,
                'starts_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addMonths(2),
                'usage_limit' => 20,
                'usage_limit_per_user' => 1,
                'times_used' => 0,
                'is_active' => true,
                'plan_id' => $premiumPlan->id,
                'metadata' => [
                    'description' => 'Special discount for premium plan',
                    'campaign' => 'premium_promotion',
                ],
            ]);
        }

        // Limited time 50% off
        Coupon::create([
            'code' => 'BLACKFRIDAY50',
            'discount_type' => 'percentage',
            'discount_value' => 50.00,
            'starts_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addDays(7),
            'usage_limit' => 500,
            'usage_limit_per_user' => 1,
            'times_used' => 0,
            'is_active' => true,
            'plan_id' => null,
            'metadata' => [
                'description' => 'Black Friday special offer',
                'campaign' => 'black_friday_2024',
            ],
        ]);

        // Unlimited usage for testing
        Coupon::create([
            'code' => 'TEST10',
            'discount_type' => 'percentage',
            'discount_value' => 10.00,
            'starts_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addYear(),
            'usage_limit' => null, // No limit
            'usage_limit_per_user' => 999,
            'times_used' => 0,
            'is_active' => true,
            'plan_id' => null,
            'metadata' => [
                'description' => 'Test coupon with unlimited usage',
                'test_only' => true,
            ],
        ]);
    }
}

