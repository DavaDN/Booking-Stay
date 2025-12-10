<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGuestFieldsToBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('customer_id');
            }
            if (!Schema::hasColumn('bookings', 'customer_email')) {
                $table->string('customer_email')->nullable()->after('customer_name');
            }
            if (Schema::hasColumn('bookings', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'customer_name')) {
                $table->dropColumn('customer_name');
            }
            if (Schema::hasColumn('bookings', 'customer_email')) {
                $table->dropColumn('customer_email');
            }
            // leaving customer_id nullable to avoid accidental data loss on revert
        });
    }
}
