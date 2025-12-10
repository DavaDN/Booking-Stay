<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerNameEmailToReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('customer_id');
            }
            if (!Schema::hasColumn('reservations', 'customer_email')) {
                $table->string('customer_email')->nullable()->after('customer_name');
            }
            // make customer_id nullable
            if (Schema::hasColumn('reservations', 'customer_id')) {
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
        Schema::table('reservations', function (Blueprint $table) {
            if (Schema::hasColumn('reservations', 'customer_name')) {
                $table->dropColumn('customer_name');
            }
            if (Schema::hasColumn('reservations', 'customer_email')) {
                $table->dropColumn('customer_email');
            }
            // make customer_id not nullable — be conservative: leave as nullable to avoid data loss
            // If you want to revert to not-nullable, handle any nulls first before running revert.
        });
    }
}
