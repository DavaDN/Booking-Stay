<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'midtrans_order_id')) {
                $table->string('midtrans_order_id')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('transactions', 'payment_type')) {
                $table->string('payment_type')->nullable()->after('midtrans_order_id');
            }
            if (!Schema::hasColumn('transactions', 'midtrans_status')) {
                $table->string('midtrans_status')->nullable()->after('payment_type');
            }
            if (!Schema::hasColumn('transactions', 'midtrans_response')) {
                $table->text('midtrans_response')->nullable()->after('midtrans_status');
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
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'midtrans_response')) {
                $table->dropColumn('midtrans_response');
            }
            if (Schema::hasColumn('transactions', 'midtrans_status')) {
                $table->dropColumn('midtrans_status');
            }
            if (Schema::hasColumn('transactions', 'payment_type')) {
                $table->dropColumn('payment_type');
            }
            if (Schema::hasColumn('transactions', 'midtrans_order_id')) {
                $table->dropColumn('midtrans_order_id');
            }
        });
    }
};
