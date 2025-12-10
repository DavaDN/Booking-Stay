<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Add 'paid' to bookings.status enum
        DB::statement("ALTER TABLE `bookings` MODIFY `status` ENUM('pending','confirmed','paid','checked_in','checked_out','cancelled') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Revert to previous enum without 'paid'
        DB::statement("ALTER TABLE `bookings` MODIFY `status` ENUM('pending','confirmed','checked_in','checked_out','cancelled') NOT NULL DEFAULT 'pending'");
    }
};
