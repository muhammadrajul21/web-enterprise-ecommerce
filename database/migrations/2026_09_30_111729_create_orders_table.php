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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('voucher_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('order_code')->unique();

            $table->enum('status', [
                'pending_payment',
                'waiting_verification',
                'processing',
                'packing',
                'shipped',
                'completed',
                'cancelled'
            ])->default('pending_payment');

            // Snapshot alamat pengiriman
            $table->string('recipient_name');
            $table->string('recipient_phone', 20);
            $table->text('shipping_address');
            $table->string('shipping_city', 100);
            $table->string('shipping_province', 100);
            $table->string('shipping_postal_code', 10);

            // Nilai transaksi
            $table->decimal('subtotal', 15, 2);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);

            // Pengiriman
            $table->string('courier', 100)->nullable();
            $table->string('shipping_service', 100)->nullable();
            $table->string('tracking_number', 100)->nullable();

            // Waktu status penting
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->text('cancel_reason')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('order_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
