<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regencies', function (Blueprint $table) {
            $table->string('id', 10)->primary();

            $table->string('province_id', 10);

            $table->string('name', 150);

            $table->timestamps();

            $table->foreign('province_id')
                ->references('id')
                ->on('provinces')
                ->cascadeOnDelete();

            $table->index('province_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regencies');
    }
};