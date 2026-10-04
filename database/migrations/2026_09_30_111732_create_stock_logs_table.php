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
        Schema::create('stock_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('variant_id')
                ->constrained('product_variants')
                ->cascadeOnDelete();

            $table->enum('type', [
                'IN',
                'OUT',
                'RETURN',
                'ADJUSTMENT'
            ]);

            $table->unsignedInteger('quantity');

            $table->integer('stock_before');

            $table->integer('stock_after');

            $table->string('reference_type', 100)
                ->nullable();

            $table->unsignedBigInteger('reference_id')
                ->nullable();

            $table->text('note')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index([
                'reference_type',
                'reference_id'
            ]);

            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_logs');
    }
};
