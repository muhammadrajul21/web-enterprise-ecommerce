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

            /*
            |--------------------------------------------------------------------------
            | USERS
            |--------------------------------------------------------------------------
            */

            $admin = User::where(
                'email',
                'admin@lifestyle.test'
            )->firstOrFail();

            $staff = User::where(
                'email',
                'staff@lifestyle.test'
            )->firstOrFail();

            $rizky = User::where(
                'email',
                'rizky@lifestyle.test'
            )->firstOrFail();

            $nabila = User::where(
                'email',
                'nabila@lifestyle.test'
            )->firstOrFail();

            $dimas = User::where(
                'email',
                'dimas@lifestyle.test'
            )->firstOrFail();


            /*
            |--------------------------------------------------------------------------
            | VOUCHER
            |--------------------------------------------------------------------------
            */

            $welcomeVoucher = DB::table('vouchers')
                ->where('code', 'WELCOME10')
                ->first();


            /*
            |--------------------------------------------------------------------------
            | DEMO ORDER DEFINITIONS
            |--------------------------------------------------------------------------
            */

            $demoOrders = [
                [
                    'code' => 'ORD-20261018-K7M2QP',
                    'customer' => $rizky,
                    'sku' => 'OTS-BLK-M',
                    'status' => 'pending_payment',
                    'days_ago' => 0,
                    'quantity' => 1,
                    'address' => 'Jl. Merdeka No. 12',
                    'city' => 'Banda Aceh',
                    'postal_code' => '23116',
                ],

                [
                    'code' => 'ORD-20261017-N4B8TX',
                    'customer' => $nabila,
                    'sku' => 'COS-BLU-M',
                    'status' => 'waiting_verification',
                    'days_ago' => 1,
                    'quantity' => 1,
                    'address' => 'Jl. Malikussaleh No. 8',
                    'city' => 'Lhokseumawe',
                    'postal_code' => '24351',
                ],

                [
                    'code' => 'ORD-20261016-D6R3PL',
                    'customer' => $dimas,
                    'sku' => 'RP-BLK-M',
                    'status' => 'processing',
                    'days_ago' => 2,
                    'quantity' => 1,
                    'address' => 'Jl. Medan Banda Aceh No. 21',
                    'city' => 'Bireuen',
                    'postal_code' => '24251',
                ],

                [
                    'code' => 'ORD-20261015-R8W5KM',
                    'customer' => $rizky,
                    'sku' => 'LJ-BLK-M',
                    'status' => 'packing',
                    'days_ago' => 3,
                    'quantity' => 1,
                    'address' => 'Jl. Merdeka No. 12',
                    'city' => 'Banda Aceh',
                    'postal_code' => '23116',
                ],

                [
                    'code' => 'ORD-20261014-H3T9QX',
                    'customer' => $nabila,
                    'sku' => 'ES-WHT-40',
                    'status' => 'shipped',
                    'days_ago' => 4,
                    'quantity' => 1,
                    'address' => 'Jl. Malikussaleh No. 8',
                    'city' => 'Lhokseumawe',
                    'postal_code' => '24351',
                ],

                [
                    'code' => 'ORD-20261011-M5C7ZA',
                    'customer' => $dimas,
                    'sku' => 'MTB-BLK-OS',
                    'status' => 'completed',
                    'days_ago' => 7,
                    'quantity' => 2,
                    'address' => 'Jl. Medan Banda Aceh No. 21',
                    'city' => 'Bireuen',
                    'postal_code' => '24251',
                    'voucher' => true,
                ],

                [
                    'code' => 'ORD-20261010-P2V6BN',
                    'customer' => $rizky,
                    'sku' => 'CBC-BLK-OS',
                    'status' => 'cancelled',
                    'days_ago' => 8,
                    'quantity' => 1,
                    'address' => 'Jl. Merdeka No. 12',
                    'city' => 'Banda Aceh',
                    'postal_code' => '23116',
                ],
            ];


            foreach ($demoOrders as $demo) {

                $variant = ProductVariant::with('product')
                    ->where(
                        'sku',
                        $demo['sku']
                    )
                    ->firstOrFail();


                $customer = $demo['customer'];

                $quantity = $demo['quantity'];

                $price = (float) $variant->price;

                $subtotal =
                    $price * $quantity;

                $shippingCost = 20000;

                $discountAmount = 0;

                $voucherId = null;


                /*
                |--------------------------------------------------------------------------
                | VOUCHER
                |--------------------------------------------------------------------------
                */

                if (!empty($demo['voucher'])) {
                    $voucherId =
                        $welcomeVoucher->id;

                    $discountAmount =
                        min(
                            $subtotal * 0.10,
                            50000
                        );
                }


                $totalAmount =
                    $subtotal
                    - $discountAmount
                    + $shippingCost;


                /*
                |--------------------------------------------------------------------------
                | DATE
                |--------------------------------------------------------------------------
                */

                $createdAt = now()
                    ->subDays(
                        $demo['days_ago']
                    );


                /*
                |--------------------------------------------------------------------------
                | ORDER
                |--------------------------------------------------------------------------
                */

                $orderData = [
                    'user_id' =>
                        $customer->id,

                    'voucher_id' =>
                        $voucherId,

                    'order_code' =>
                        $demo['code'],

                    'status' =>
                        $demo['status'],

                    'recipient_name' =>
                        $customer->name,

                    'recipient_phone' =>
                        $customer->phone,

                    'shipping_address' =>
                        $demo['address'],

                    'shipping_city' =>
                        $demo['city'],

                    'shipping_province' =>
                        'Aceh',

                    'shipping_postal_code' =>
                        $demo['postal_code'],

                    'subtotal' =>
                        $subtotal,

                    'discount_amount' =>
                        $discountAmount,

                    'shipping_cost' =>
                        $shippingCost,

                    'total_amount' =>
                        $totalAmount,

                    'courier' =>
                        null,

                    'shipping_service' =>
                        null,

                    'tracking_number' =>
                        null,

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


                /*
                |--------------------------------------------------------------------------
                | PAID ORDERS
                |--------------------------------------------------------------------------
                */

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


                /*
                |--------------------------------------------------------------------------
                | SHIPPED
                |--------------------------------------------------------------------------
                */

                if (
                    $demo['status']
                    === 'shipped'
                ) {
                    $orderData['courier'] =
                        'JNE';

                    $orderData['shipping_service'] =
                        'REG';

                    $orderData['tracking_number'] =
                        'JNE261018005';

                    $orderData['shipped_at'] =
                        $createdAt->copy()
                            ->addDay();
                }


                /*
                |--------------------------------------------------------------------------
                | COMPLETED
                |--------------------------------------------------------------------------
                */

                if (
                    $demo['status']
                    === 'completed'
                ) {
                    $orderData['courier'] =
                        'J&T Express';

                    $orderData['shipping_service'] =
                        'EZ';

                    $orderData['tracking_number'] =
                        'JNT261018006';

                    $orderData['shipped_at'] =
                        $createdAt->copy()
                            ->addDay();

                    $orderData['completed_at'] =
                        $createdAt->copy()
                            ->addDays(3);
                }


                /*
                |--------------------------------------------------------------------------
                | CANCELLED
                |--------------------------------------------------------------------------
                */

                if (
                    $demo['status']
                    === 'cancelled'
                ) {
                    $orderData['cancelled_at'] =
                        $createdAt->copy()
                            ->addHours(5);

                    $orderData['cancel_reason'] =
                        'Pembayaran tidak dapat diverifikasi.';
                }


                $order = Order::create(
                    $orderData
                );


                $order->created_at =
                    $createdAt;

                $order->updated_at =
                    $createdAt;

                $order->save();


                /*
                |--------------------------------------------------------------------------
                | ORDER ITEM
                |--------------------------------------------------------------------------
                */

                $orderItem =
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


                /*
                |--------------------------------------------------------------------------
                | PENDING PAYMENT
                |--------------------------------------------------------------------------
                */

                if (
                    $demo['status']
                    === 'pending_payment'
                ) {
                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | WAITING VERIFICATION
                |--------------------------------------------------------------------------
                */

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
                            'uploads/payments/payment-1-a40549c1-f898-47f5-b661-2d1d65e28857.png',

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


                /*
                |--------------------------------------------------------------------------
                | CANCELLED / REJECTED PAYMENT
                |--------------------------------------------------------------------------
                */

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
                            'uploads/payments/payment-2-e9f20bb0-3eaf-4254-9c5b-82d4482ebeba.jpg',

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


                /*
                |--------------------------------------------------------------------------
                | VERIFIED PAYMENT
                |--------------------------------------------------------------------------
                */

                $paymentImages = [
                    'processing' =>
                        'uploads/payments/payment-3-5bf19c1e-f956-420b-8b0c-2dfb90cf4142.jpg',

                    'packing' =>
                        'uploads/payments/payment-4-0459b319-e5d0-4017-a10b-c1de34ba31f2.png',

                    'shipped' =>
                        'uploads/payments/payment-5-ec0d7ab5-f196-484b-a5f4-57b1dc58e760.png',

                    'completed' =>
                        'uploads/payments/payment-3-5bf19c1e-f956-420b-8b0c-2dfb90cf4142.jpg',
                ];


                $order->payments()->create([
                    'payment_method' =>
                        'bank_transfer',

                    'amount' =>
                        $totalAmount,

                    'proof_image' =>
                        $paymentImages[
                            $demo['status']
                        ],

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


                /*
                |--------------------------------------------------------------------------
                | POD + REVIEW FOR COMPLETED ORDER
                |--------------------------------------------------------------------------
                */

                if (
                    $demo['status']
                    === 'completed'
                ) {
                    DB::table(
                        'shipment_updates'
                    )->insert([
                        'order_id' =>
                            $order->id,

                        'updated_by' =>
                            $staff->id,

                        'status' =>
                            'delivered',

                        'location_name' =>
                            'Bireuen, Aceh',

                        'latitude' =>
                            5.2030,

                        'longitude' =>
                            96.7009,

                        'receiver_name' =>
                            $customer->name,

                        'note' =>
                            'Barang diterima langsung oleh customer dalam kondisi baik.',

                        'proof_image' =>
                            'delivery-proofs/demo-proof.jpg',

                        'occurred_at' =>
                            $orderData[
                                'completed_at'
                            ],

                        'created_at' =>
                            $orderData[
                                'completed_at'
                            ],

                        'updated_at' =>
                            $orderData[
                                'completed_at'
                            ],
                    ]);


                    DB::table('reviews')
                        ->insert([
                            'user_id' =>
                                $customer->id,

                            'order_item_id' =>
                                $orderItem->id,

                            'rating' =>
                                5,

                            'review' =>
                                'Produk sesuai deskripsi, kualitas bagus, dan pengiriman berjalan dengan baik.',

                            'is_visible' =>
                                true,

                            'created_at' =>
                                $orderData[
                                    'completed_at'
                                ],

                            'updated_at' =>
                                $orderData[
                                    'completed_at'
                                ],
                        ]);
                }
            }
        });
    }
}