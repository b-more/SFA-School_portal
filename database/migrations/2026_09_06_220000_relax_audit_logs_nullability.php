<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // auth events (login / failed_login / lockout) have no target model
        // to point at, so both polymorphic columns need to accept NULL.
        DB::statement('ALTER TABLE audit_logs MODIFY COLUMN auditable_type VARCHAR(255) NULL');
        DB::statement('ALTER TABLE audit_logs MODIFY COLUMN auditable_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE audit_logs MODIFY COLUMN auditable_type VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE audit_logs MODIFY COLUMN auditable_id BIGINT UNSIGNED NOT NULL');
    }
};
