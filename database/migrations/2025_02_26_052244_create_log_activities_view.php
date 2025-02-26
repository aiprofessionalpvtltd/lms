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
            CREATE VIEW log_activities_view AS
            SELECT
                log_activities.id,
                log_activities.user_id,
                users.name AS user_name,
                log_activities.subject,
                log_activities.url,
                log_activities.method,
                log_activities.ip,
                log_activities.agent,
                log_activities.details,
                log_activities.created_at
            FROM log_activities
            LEFT JOIN users ON log_activities.user_id = users.id
            ORDER BY log_activities.created_at DESC
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS log_activities_view");
    }
};
