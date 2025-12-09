<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
    CREATE VIEW soon_to_expire AS
    SELECT
        us.id as subscription_id,
        us.user_id,
        u.first_name,
        u.last_name,
        u.email,
        us.plan_id,
        sp.name_ar as plan_name_ar,
        sp.name_en as plan_name_en,
        us.status,
        us.ends_at,
        us.auto_renew,
        DATEDIFF(us.ends_at, NOW()) as days_remaining,
        CASE
            WHEN DATEDIFF(us.ends_at, NOW()) <= 3 THEN 'critical'
            WHEN DATEDIFF(us.ends_at, NOW()) <= 7 THEN 'warning'
            WHEN DATEDIFF(us.ends_at, NOW()) <= 30 THEN 'notice'
            ELSE 'normal'
        END as urgency_level
    FROM user_subscriptions us
    JOIN users u ON us.user_id = u.id
    JOIN subscription_plans sp ON us.plan_id = sp.id
    WHERE us.status IN ('active', 'trial')
    AND us.ends_at <= DATE_ADD(NOW(), INTERVAL 30 DAY)
    AND us.ends_at > NOW()
    ORDER BY us.ends_at ASC
");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soon_to_expires');
    }
};
