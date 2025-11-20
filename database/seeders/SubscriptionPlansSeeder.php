<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;
use App\Models\PlanLimitation;

class SubscriptionPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Basic Plan
        $basic = SubscriptionPlan::create([
            'name_ar' => 'الخطة الأساسية',
            'name_en' => 'Basic Plan',
            'slug' => 'basic',
            'description_ar' => 'خطة مثالية للمشاهدة الفردية بجودة عالية',
            'description_en' => 'Perfect for individual viewing in high quality',
            'price' => 9.99,
            'currency' => 'USD',
            'billing_period' => 'monthly',
            'trial_days' => 7,
            'max_profiles' => 1,
            'max_devices' => 1,
            'video_quality' => 'hd',
            'download_enabled' => false,
            'ads_enabled' => true,
            'live_tv_enabled' => false,
            'features' => [
                'Watch on phone, tablet, or computer',
                'HD quality streaming',
                '1 simultaneous stream',
            ],
            'sort_order' => 1,
            'is_popular' => false,
            'is_active' => true,
        ]);

        PlanLimitation::create([
            'plan_id' => $basic->id,
            'limitation_type' => 'streaming',
            'limitation_key' => 'max_concurrent_streams',
            'limitation_value' => '1',
            'limitation_unit' => 'streams',
            'description_ar' => 'مشاهدة واحدة في نفس الوقت',
            'description_en' => 'One stream at a time',
            'is_active' => true,
        ]);

        PlanLimitation::create([
            'plan_id' => $basic->id,
            'limitation_type' => 'quality',
            'limitation_key' => 'allowed_qualities',
            'limitation_value' => 'sd,hd',
            'limitation_unit' => null,
            'description_ar' => 'جودة حتى HD',
            'description_en' => 'Quality up to HD',
            'is_active' => true,
        ]);

        // Standard Plan
        $standard = SubscriptionPlan::create([
            'name_ar' => 'الخطة القياسية',
            'name_en' => 'Standard Plan',
            'slug' => 'standard',
            'description_ar' => 'خطة مثالية للعائلات الصغيرة مع إمكانية المشاهدة على جهازين',
            'description_en' => 'Perfect for small families with 2 simultaneous streams',
            'price' => 14.99,
            'currency' => 'USD',
            'billing_period' => 'monthly',
            'trial_days' => 7,
            'max_profiles' => 2,
            'max_devices' => 2,
            'video_quality' => 'hd',
            'download_enabled' => true,
            'ads_enabled' => false,
            'live_tv_enabled' => true,
            'features' => [
                'Watch on 2 devices simultaneously',
                'HD quality streaming',
                'Download content for offline viewing',
                'Ad-free experience',
                'Access to Live TV',
            ],
            'sort_order' => 2,
            'is_popular' => true,
            'is_active' => true,
        ]);

        PlanLimitation::create([
            'plan_id' => $standard->id,
            'limitation_type' => 'streaming',
            'limitation_key' => 'max_concurrent_streams',
            'limitation_value' => '2',
            'limitation_unit' => 'streams',
            'description_ar' => 'مشاهدتان في نفس الوقت',
            'description_en' => 'Two streams at a time',
            'is_active' => true,
        ]);

        PlanLimitation::create([
            'plan_id' => $standard->id,
            'limitation_type' => 'quality',
            'limitation_key' => 'allowed_qualities',
            'limitation_value' => 'sd,hd',
            'limitation_unit' => null,
            'description_ar' => 'جودة حتى HD',
            'description_en' => 'Quality up to HD',
            'is_active' => true,
        ]);

        PlanLimitation::create([
            'plan_id' => $standard->id,
            'limitation_type' => 'downloads',
            'limitation_key' => 'max_daily_downloads',
            'limitation_value' => '5',
            'limitation_unit' => 'downloads',
            'description_ar' => '5 تنزيلات يومياً',
            'description_en' => '5 downloads per day',
            'is_active' => true,
        ]);

        // Premium Plan
        $premium = SubscriptionPlan::create([
            'name_ar' => 'الخطة المميزة',
            'name_en' => 'Premium Plan',
            'slug' => 'premium',
            'description_ar' => 'أفضل تجربة مشاهدة بجودة 4K وبدون إعلانات',
            'description_en' => 'Best viewing experience with 4K quality and no ads',
            'price' => 19.99,
            'currency' => 'USD',
            'billing_period' => 'monthly',
            'trial_days' => 14,
            'max_profiles' => 5,
            'max_devices' => 4,
            'video_quality' => 'uhd',
            'download_enabled' => true,
            'ads_enabled' => false,
            'live_tv_enabled' => true,
            'features' => [
                'Watch on 4 devices simultaneously',
                'Ultra HD (4K) quality streaming',
                'Unlimited downloads',
                'Ad-free experience',
                'Access to Live TV',
                'Early access to new content',
                'Priority customer support',
            ],
            'sort_order' => 3,
            'is_popular' => false,
            'is_active' => true,
        ]);

        PlanLimitation::create([
            'plan_id' => $premium->id,
            'limitation_type' => 'streaming',
            'limitation_key' => 'max_concurrent_streams',
            'limitation_value' => '4',
            'limitation_unit' => 'streams',
            'description_ar' => '4 مشاهدات في نفس الوقت',
            'description_en' => 'Four streams at a time',
            'is_active' => true,
        ]);

        PlanLimitation::create([
            'plan_id' => $premium->id,
            'limitation_type' => 'quality',
            'limitation_key' => 'allowed_qualities',
            'limitation_value' => 'sd,hd,uhd',
            'limitation_unit' => null,
            'description_ar' => 'جودة حتى 4K UHD',
            'description_en' => 'Quality up to 4K UHD',
            'is_active' => true,
        ]);

        // Yearly Basic Plan (with discount)
        $yearlyBasic = SubscriptionPlan::create([
            'name_ar' => 'الخطة الأساسية - سنوية',
            'name_en' => 'Basic Plan - Yearly',
            'slug' => 'basic-yearly',
            'description_ar' => 'وفر المال مع الخطة السنوية',
            'description_en' => 'Save money with yearly subscription',
            'price' => 99.99,
            'currency' => 'USD',
            'billing_period' => 'yearly',
            'trial_days' => 14,
            'max_profiles' => 1,
            'max_devices' => 1,
            'video_quality' => 'hd',
            'download_enabled' => false,
            'ads_enabled' => true,
            'live_tv_enabled' => false,
            'features' => [
                'Watch on phone, tablet, or computer',
                'HD quality streaming',
                '1 simultaneous stream',
                'Save 17% compared to monthly',
            ],
            'sort_order' => 4,
            'is_popular' => false,
            'is_active' => true,
        ]);

        PlanLimitation::create([
            'plan_id' => $yearlyBasic->id,
            'limitation_type' => 'streaming',
            'limitation_key' => 'max_concurrent_streams',
            'limitation_value' => '1',
            'limitation_unit' => 'streams',
            'is_active' => true,
        ]);

        PlanLimitation::create([
            'plan_id' => $yearlyBasic->id,
            'limitation_type' => 'quality',
            'limitation_key' => 'allowed_qualities',
            'limitation_value' => 'sd,hd',
            'limitation_unit' => null,
            'is_active' => true,
        ]);
    }
}

