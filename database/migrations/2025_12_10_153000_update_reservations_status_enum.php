<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Add 'pending' and 'paid' to reservations.status enum
        // NOTE: This uses raw SQL and targets MySQL. Adjust if using other DB.
        DB::statement("ALTER TABLE `reservations` MODIFY `status` ENUM('booked','pending','paid','check_in','check_out','cancelled') NOT NULL DEFAULT 'booked'");
    }

    public function down(): void
    {
        // Revert to original enum values (may fail if rows contain values not in list)
        DB::statement("ALTER TABLE `reservations` MODIFY `status` ENUM('booked','check_in','check_out','cancelled') NOT NULL DEFAULT 'booked'");
    }
};
