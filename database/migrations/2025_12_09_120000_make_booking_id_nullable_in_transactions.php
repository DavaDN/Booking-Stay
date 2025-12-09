<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Drop existing foreign key then make booking_id nullable and recreate FK with SET NULL
        Schema::table('transactions', function (Blueprint $table) {
            // drop foreign key if exists (uses default naming convention)
            try {
                $table->dropForeign(['booking_id']);
            } catch (\Exception $e) {
                // ignore if not exists
            }
        });

        // modify column to allow null (raw statement to avoid requiring doctrine/dbal)
        DB::statement('ALTER TABLE `transactions` MODIFY `booking_id` BIGINT UNSIGNED NULL');

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            try {
                $table->dropForeign(['booking_id']);
            } catch (\Exception $e) {
            }
        });

        // make booking_id NOT NULL again (if you want reverse, ensure default exists)
        DB::statement('ALTER TABLE `transactions` MODIFY `booking_id` BIGINT UNSIGNED NOT NULL');

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
        });
    }
};
