<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_updates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('status', 50);

            $table->string('location_name')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->string('receiver_name')->nullable();

            $table->text('note')->nullable();

            $table->string('proof_image')->nullable();

            $table->timestamp('occurred_at')->nullable();

            $table->timestamps();

            $table->index([
                'order_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_updates');
    }
};