<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // ========================
            // USER DEMO
            // ========================

            $customer = User::where(
                'email',
                'customer@example.com'
            )->firstOrFail();

            $admin = User::where(
                'email',
                'admin@example.com'
            )->firstOrFail();


            // ========================
            // PRODUCT VARIANTS
            // ========================

            $variants = ProductVariant::with('product')
                ->where('status', 'active')
                ->take(7)
                ->get();

            if ($variants->isEmpty()) {
                return;
            }


            // ========================
            // DEMO ORDERS
            // ========================

            $demoOrders = [

                [
                    'code' => 'ORD-DEMO-001',
                    'status' => 'pending_payment',
                    'days_ago' => 0,
                    'quantity' => 1,
                ],

                [
                    'code' => 'ORD-DEMO-002',
                    'status' => 'waiting_verification',
                    'days_ago' => 1,
                    'quantity' => 1,
                ],

                [
                    'code' => 'ORD-DEMO-003',
                    'status' => 'processing',
                    'days_ago' => 2,
                    'quantity' => 2,
                ],

                [
                    'code' => 'ORD-DEMO-004',
                    'status' => 'packing',
                    'days_ago' => 3,
                    'quantity' => 1,
                ],

                [
                    'code' => 'ORD-DEMO-005',
                    'status' => 'shipped',
                    'days_ago' => 4,
                    'quantity' => 1,
                ],

                [
                    'code' => 'ORD-DEMO-006',
                    'status' => 'completed',
                    'days_ago' => 7,
                    'quantity' => 2,
                ],

                [
                    'code' => 'ORD-DEMO-007',
                    'status' => 'cancelled',
                    'days_ago' => 8,
                    'quantity' => 1,
                ],
            ];


            foreach ($demoOrders as $index => $demo) {

                $variant = $variants[
                    $index % $variants->count()
                ];

                $quantity = $demo['quantity'];

                $price = (float) $variant->price;

                $subtotal = $price * $quantity;

                $discountAmount = 0;

                $shippingCost = 20000;

                $totalAmount =
                    $subtotal
                    - $discountAmount
                    + $shippingCost;


                // ========================
                // HAPUS DEMO LAMA
                // ========================

                $oldOrder = Order::where(
                    'order_code',
                    $demo['code']
                )->first();

                if ($oldOrder) {

                    $oldOrder->payments()->delete();

                    $oldOrder->items()->delete();

                    $oldOrder->delete();
                }


                // ========================
                // WAKTU
                // ========================

                $createdAt = now()
                    ->subDays($demo['days_ago']);


                // ========================
                // DATA ORDER
                // ========================

                $orderData = [

                    'user_id' =>
                        $customer->id,

                    'voucher_id' =>
                        null,

                    'order_code' =>
                        $demo['code'],

                    'status' =>
                        $demo['status'],


                    // Snapshot penerima

                    'recipient_name' =>
                        $customer->name,

                    'recipient_phone' =>
                        $customer->phone
                            ?: '081234567890',


                    // Snapshot alamat pengiriman

                    'shipping_address' =>
                        'Jl. Lifestyle No. 10',

                    'shipping_city' =>
                        'Lhokseumawe',

                    'shipping_province' =>
                        'Aceh',

                    'shipping_postal_code' =>
                        '24351',


                    // Nilai transaksi

                    'subtotal' =>
                        $subtotal,

                    'discount_amount' =>
                        $discountAmount,

                    'shipping_cost' =>
                        $shippingCost,

                    'total_amount' =>
                        $totalAmount,


                    // Pengiriman

                    'courier' =>
                        null,

                    'shipping_service' =>
                        null,

                    'tracking_number' =>
                        null,


                    // Status waktu

                    'paid_at' =>
                        null,

                    'shipped_at' =>
                        null,

                    'completed_at' =>
                        null,

                    'cancelled_at' =>
                        null,

                    'cancel_reason' =>
                        null,
                ];


                // ========================
                // STATUS SUDAH DIBAYAR
                // ========================

                if (
                    in_array(
                        $demo['status'],
                        [
                            'processing',
                            'packing',
                            'shipped',
                            'completed',
                        ]
                    )
                ) {

                    $orderData['paid_at'] =
                        $createdAt->copy()
                            ->addHours(2);
                }


                // ========================
                // SHIPPED
                // ========================

                if ($demo['status'] === 'shipped') {

                    $orderData['courier'] =
                        'JNE';

                    $orderData['shipping_service'] =
                        'REG';

                    $orderData['tracking_number'] =
                        'JNE-DEMO-005';

                    $orderData['shipped_at'] =
                        $createdAt->copy()
                            ->addDay();
                }


                // ========================
                // COMPLETED
                // ========================

                if ($demo['status'] === 'completed') {

                    $orderData['courier'] =
                        'J&T';

                    $orderData['shipping_service'] =
                        'EZ';

                    $orderData['tracking_number'] =
                        'JNT-DEMO-006';

                    $orderData['shipped_at'] =
                        $createdAt->copy()
                            ->addDay();

                    $orderData['completed_at'] =
                        $createdAt->copy()
                            ->addDays(3);
                }


                // ========================
                // CANCELLED
                // ========================

                if ($demo['status'] === 'cancelled') {

                    $orderData['cancelled_at'] =
                        $createdAt->copy()
                            ->addHours(5);

                    $orderData['cancel_reason'] =
                        'Pembayaran tidak dapat diverifikasi.';
                }


                // ========================
                // CREATE ORDER
                // ========================

                $order = Order::create(
                    $orderData
                );


                // Set waktu order demo

                $order->created_at =
                    $createdAt;

                $order->updated_at =
                    $createdAt;

                $order->save();


                // ========================
                // ORDER ITEM
                // ========================

                $order->items()->create([

                    'variant_id' =>
                        $variant->id,

                    'product_name' =>
                        $variant->product->name,

                    'sku' =>
                        $variant->sku,

                    'color' =>
                        $variant->color,

                    'size' =>
                        $variant->size,

                    'price' =>
                        $price,

                    'quantity' =>
                        $quantity,

                    'subtotal' =>
                        $subtotal,
                ]);


                // ========================
                // PENDING PAYMENT
                // Belum upload pembayaran
                // ========================

                if (
                    $demo['status']
                    === 'pending_payment'
                ) {
                    continue;
                }


                // ========================
                // WAITING VERIFICATION
                // ========================

                if (
                    $demo['status']
                    === 'waiting_verification'
                ) {

                    $order->payments()->create([

                        'payment_method' =>
                            'bank_transfer',

                        'amount' =>
                            $totalAmount,

                        'proof_image' =>
                            'images/payments/demo-002.jpg',

                        'status' =>
                            'pending',

                        'verified_by' =>
                            null,

                        'paid_at' =>
                            $createdAt->copy()
                                ->addHour(),

                        'verified_at' =>
                            null,

                        'rejection_reason' =>
                            null,
                    ]);

                    continue;
                }


                // ========================
                // CANCELLED
                // PAYMENT REJECTED
                // ========================

                if (
                    $demo['status']
                    === 'cancelled'
                ) {

                    $order->payments()->create([

                        'payment_method' =>
                            'bank_transfer',

                        'amount' =>
                            $totalAmount,

                        'proof_image' =>
                            'images/payments/demo-007.jpg',

                        'status' =>
                            'rejected',

                        'verified_by' =>
                            $admin->id,

                        'paid_at' =>
                            $createdAt->copy()
                                ->addHour(),

                        'verified_at' =>
                            $createdAt->copy()
                                ->addHours(4),

                        'rejection_reason' =>
                            'Bukti pembayaran tidak valid.',
                    ]);

                    continue;
                }


                // ========================
                // VERIFIED PAYMENT
                // ========================

                $order->payments()->create([

                    'payment_method' =>
                        'bank_transfer',

                    'amount' =>
                        $totalAmount,

                    'proof_image' =>
                        'images/payments/' .
                        strtolower($demo['code']) .
                        '.jpg',

                    'status' =>
                        'verified',

                    'verified_by' =>
                        $admin->id,

                    'paid_at' =>
                        $createdAt->copy()
                            ->addHour(),

                    'verified_at' =>
                        $createdAt->copy()
                            ->addHours(2),

                    'rejection_reason' =>
                        null,
                ]);
            }
        });
    }
}