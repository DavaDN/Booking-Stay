<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVouchersTable extends Migration
{
    public function up()
    {
        Schema::create('voucher', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->integer('discount');
            $table->timestamp('expired_at')->nullable()->before('timestamp');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('voucher');
    }
}
