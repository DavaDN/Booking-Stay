<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('facility_room_type', function (Blueprint $table) {
            $table->id();

            // Foreign key ke tabel facilities
            $table->unsignedBigInteger('facility_id');
            $table->foreign('facility_id')
                ->references('id')
                ->on('facilities')
                ->onDelete('cascade');

            // Foreign key ke tabel room_types
            $table->unsignedBigInteger('room_type_id');
            $table->foreign('room_type_id')
                ->references('id')
                ->on('room_types')
                ->onDelete('cascade');

            $table->timestamps();

            // Pastikan kombinasi unik agar tidak ada duplikat
            $table->unique(['facility_id', 'room_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_room_type');
    }
};
