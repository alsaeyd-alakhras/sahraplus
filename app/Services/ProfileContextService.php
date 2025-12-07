<?php

namespace App\Services;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileContextService
{
    /**
     * استخراج البروفايل من الطلب والتحقق من ملكيته
     * 
     * @param Request $request
     * @return UserProfile|null
     */
    public function resolveProfile(Request $request): ?UserProfile
    {
        // محاولة قراءة profile_id من body أو query أو session
        $profileId = $request->input('profile_id') 
                  ?? $request->query('profile_id')
                  ?? session('active_profile_id');

        if (!$profileId) {
            return null;
        }

        // جلب البروفايل
        $profile = UserProfile::find($profileId);

        // التحقق من وجود البروفايل وملكيته للمستخدم الحالي
        if (!$profile) {
            return null;
        }

        // في المسارات المحمية بـ sanctum أو web
        if (Auth::check() && $profile->user_id !== Auth::id()) {
            return null;
        }

        return $profile;
    }

    /**
     * تحديد ما إذا كان يجب تطبيق فلتر محتوى الأطفال
     * 
     * @param UserProfile|null $profile
     * @return bool
     */
    public function shouldApplyKidsFilter(?UserProfile $profile): bool
    {
        if (!$profile) {
            return false;
        }

        return $profile->is_child_profile == true;
    }

    /**
     * تحديد ما إذا كان يجب تطبيق فلتر محتوى الأطفال عبر التصنيف
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param UserProfile|null $profile
     * @return bool
     */
    public function shouldApplyKidsFilterByCategory($query,?UserProfile $profile): bool
    {
        if (!$profile) {
            return false;
        }

        // إذا لم يكن البروفايل للأطفال، لا نحتاج للفلترة
        if (!$profile->is_child_profile) {
            return false;
        }

        // فحص إذا كان الـ query له علاقة categories وأي منها للأطفال
        // نستخدم whereHas للتحقق من وجود تصنيفات للأطفال
        try {
            // نفحص إذا كان الـ query له علاقة categories
            // ونفحص إذا كان أي من التصنيفات المرتبطة له is_kids = true
            // نستخدم clone لتجنب تعديل الـ query الأصلي
            $hasKidsCategory = (clone $query)->whereHas('categories', function ($q) {
                $q->where('is_kids', true);
            })->exists();

            // إذا كان هناك تصنيف للأطفال، يجب أن يكون المحتوى للأطفال فقط
            return $hasKidsCategory;
        } catch (\Exception $e) {
            // إذا لم تكن العلاقة موجودة، قد يكون الـ query لـ Category نفسه
            // في هذه الحالة، نفحص إذا كان الـ query نفسه يحتوي على تصنيفات للأطفال
            try {
                $hasKidsCategory = (clone $query)->where('is_kids', true)->exists();
                return $hasKidsCategory;
            } catch (\Exception $e2) {
                // إذا فشل كل شيء، نرجع false
                return false;
            }
        }
    }

    /**
     * تطبيق فلتر محتوى الأطفال على استعلام إذا لزم الأمر
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applyKidsFilterIfNeeded($query, Request $request)
    {
        $profile = $this->resolveProfile($request);

        if ($this->shouldApplyKidsFilterByCategory($query, $profile)) {
            return $query->kids();
        }
        
        if ($this->shouldApplyKidsFilter($profile)) {
            return $query->kids();
        }

        return $query;
    }
}

