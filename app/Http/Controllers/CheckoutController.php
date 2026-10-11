<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Menampilkan halaman checkout.
     */
    public function index()
    {
        $cart = Cart::with([
            'items.variant.product',
            'items.variant.images',
        ])
            ->where('user_id', auth()->id())
            ->first();

        if (
            !$cart
            || $cart->items->isEmpty()
        ) {
            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'Keranjang masih kosong.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK CART ITEMS
        |--------------------------------------------------------------------------
        */

        foreach ($cart->items as $item) {

            $variant = $item->variant;

            if (!$variant) {
                return redirect()
                    ->route('cart.index')
                    ->with(
                        'error',
                        'Salah satu variant produk sudah tidak tersedia.'
                    );
            }

            if ($variant->status !== 'active') {
                return redirect()
                    ->route('cart.index')
                    ->with(
                        'error',
                        'Salah satu produk di keranjang sedang tidak aktif.'
                    );
            }

            if ($item->quantity > $variant->stock) {
                return redirect()
                    ->route('cart.index')
                    ->with(
                        'error',
                        'Jumlah salah satu produk melebihi stok yang tersedia.'
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CURRENT SUBTOTAL
        |--------------------------------------------------------------------------
        |
        | Harga dihitung ulang dari variant terbaru.
        |
        */

        $subtotal = $cart->items->sum(function ($item) {
            return $item->variant->price * $item->quantity;
        });

        /*
        |--------------------------------------------------------------------------
        | SAVED ADDRESSES
        |--------------------------------------------------------------------------
        */

        $addresses = Address::where(
            'user_id',
            auth()->id()
        )
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | SHIPPING OPTIONS
        |--------------------------------------------------------------------------
        |
        | Sementara menggunakan ongkir statis untuk project.
        | Resi dan kurir aktual tetap diinput staff ketika pengiriman.
        |
        */

        $shippingOptions = [
            'regular' => [
                'label' => 'Regular Shipping',
                'cost' => 15000,
            ],

            'express' => [
                'label' => 'Express Shipping',
                'cost' => 30000,
            ],
        ];

        return view(
            'customer.checkout.index',
            compact(
                'cart',
                'subtotal',
                'addresses',
                'shippingOptions'
            )
        );
    }


    /**
     * Membuat order dari cart.
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'recipient_name' => [
                'required',
                'string',
                'max:255',
            ],

            'recipient_phone' => [
                'required',
                'string',
                'max:20',
            ],

            'shipping_address' => [
                'required',
                'string',
                'max:1000',
            ],

            'shipping_city' => [
                'required',
                'string',
                'max:100',
            ],

            'shipping_province' => [
                'required',
                'string',
                'max:100',
            ],

            'shipping_postal_code' => [
                'required',
                'string',
                'max:10',
            ],

            'shipping_method' => [
                'required',
                'in:regular,express',
            ],

            'voucher_code' => [
                'nullable',
                'string',
                'max:50',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | SHIPPING OPTIONS
        |--------------------------------------------------------------------------
        |
        | Jangan percaya biaya ongkir yang dikirim browser.
        | Server menentukan biayanya.
        |
        */

        $shippingOptions = [
            'regular' => [
                'label' => 'Regular Shipping',
                'cost' => 15000,
            ],

            'express' => [
                'label' => 'Express Shipping',
                'cost' => 30000,
            ],
        ];

        $shipping =
            $shippingOptions[$validated['shipping_method']];


        /*
        |--------------------------------------------------------------------------
        | GET CART
        |--------------------------------------------------------------------------
        */

        $cart = Cart::with([
            'items.variant.product',
        ])
            ->where('user_id', auth()->id())
            ->first();


        if (
            !$cart
            || $cart->items->isEmpty()
        ) {
            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'Keranjang masih kosong.'
                );
        }


        try {

            $order = DB::transaction(
                function () use (
                    $validated,
                    $shipping,
                    $cart
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | CALCULATE SUBTOTAL
                    |--------------------------------------------------------------------------
                    */

                    $subtotal = 0;


                    foreach ($cart->items as $item) {

                        /*
                         * Lock variant supaya stock tidak berubah
                         * ketika checkout sedang diproses.
                         */
                        $variant = ProductVariant::with('product')
                            ->where(
                                'id',
                                $item->variant_id
                            )
                            ->lockForUpdate()
                            ->firstOrFail();


                        if ($variant->status !== 'active') {
                            throw new \RuntimeException(
                                'Salah satu produk sudah tidak tersedia.'
                            );
                        }


                        if (
                            $item->quantity
                            > $variant->stock
                        ) {
                            throw new \RuntimeException(
                                'Stok salah satu produk tidak mencukupi.'
                            );
                        }


                        $subtotal +=
                            $variant->price
                            * $item->quantity;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | VOUCHER
                    |--------------------------------------------------------------------------
                    */

                    $voucher = null;

                    $discountAmount = 0;


                    if (
                        !empty(
                            $validated['voucher_code']
                        )
                    ) {

                        $voucher = Voucher::where(
                            'code',
                            $validated['voucher_code']
                        )
                            ->lockForUpdate()
                            ->first();


                        if (!$voucher) {
                            throw new \RuntimeException(
                                'Voucher tidak ditemukan.'
                            );
                        }


                        if (!$voucher->is_active) {
                            throw new \RuntimeException(
                                'Voucher sedang tidak aktif.'
                            );
                        }


                        if (
                            $voucher->starts_at
                            && now()->lt(
                                $voucher->starts_at
                            )
                        ) {
                            throw new \RuntimeException(
                                'Voucher belum dapat digunakan.'
                            );
                        }


                        if (
                            $voucher->expires_at
                            && now()->gt(
                                $voucher->expires_at
                            )
                        ) {
                            throw new \RuntimeException(
                                'Voucher sudah kedaluwarsa.'
                            );
                        }


                        if (
                            $voucher->usage_limit !== null
                            && $voucher->used_count
                                >= $voucher->usage_limit
                        ) {
                            throw new \RuntimeException(
                                'Batas penggunaan voucher telah habis.'
                            );
                        }


                        if (
                            $subtotal
                            < $voucher->min_order_amount
                        ) {
                            throw new \RuntimeException(
                                'Subtotal belum memenuhi minimum penggunaan voucher.'
                            );
                        }


                        if (
                            $voucher->discount_type
                            === 'percentage'
                        ) {

                            $discountAmount =
                                $subtotal
                                * (
                                    $voucher->discount_value
                                    / 100
                                );


                            if (
                                $voucher->max_discount_amount
                                !== null
                            ) {

                                $discountAmount = min(
                                    $discountAmount,
                                    $voucher->max_discount_amount
                                );

                            }

                        } else {

                            $discountAmount =
                                $voucher->discount_value;

                        }


                        /*
                         * Discount tidak boleh lebih besar
                         * daripada subtotal.
                         */
                        $discountAmount = min(
                            $discountAmount,
                            $subtotal
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | TOTAL
                    |--------------------------------------------------------------------------
                    */

                    $shippingCost =
                        $shipping['cost'];


                    $totalAmount =
                        $subtotal
                        - $discountAmount
                        + $shippingCost;


                    /*
                    |--------------------------------------------------------------------------
                    | ORDER CODE
                    |--------------------------------------------------------------------------
                    */

                    do {

                        $orderCode =
                            'ORD-'
                            . now()->format('Ymd')
                            . '-'
                            . strtoupper(
                                Str::random(6)
                            );

                    } while (
                        Order::where(
                            'order_code',
                            $orderCode
                        )->exists()
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | CREATE ORDER
                    |--------------------------------------------------------------------------
                    */

                    $order = Order::create([
                        'user_id' =>
                            auth()->id(),

                        'voucher_id' =>
                            $voucher?->id,

                        'order_code' =>
                            $orderCode,

                        'status' =>
                            'pending_payment',

                        'recipient_name' =>
                            $validated['recipient_name'],

                        'recipient_phone' =>
                            $validated['recipient_phone'],

                        'shipping_address' =>
                            $validated['shipping_address'],

                        'shipping_city' =>
                            $validated['shipping_city'],

                        'shipping_province' =>
                            $validated['shipping_province'],

                        'shipping_postal_code' =>
                            $validated['shipping_postal_code'],

                        'subtotal' =>
                            $subtotal,

                        'discount_amount' =>
                            $discountAmount,

                        'shipping_cost' =>
                            $shippingCost,

                        'total_amount' =>
                            $totalAmount,

                        /*
                         * Belum menyimpan nama kurir.
                         * Staff akan mengisinya saat shipping.
                         */
                        'courier' =>
                            null,

                        'shipping_service' =>
                            $shipping['label'],
                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | ORDER ITEMS
                    |--------------------------------------------------------------------------
                    */

                    foreach ($cart->items as $item) {

                        $variant = ProductVariant::with('product')
                            ->findOrFail(
                                $item->variant_id
                            );


                        $price =
                            $variant->price;


                        OrderItem::create([
                            'order_id' =>
                                $order->id,

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
                                $item->quantity,

                            'subtotal' =>
                                $price
                                * $item->quantity,
                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CLEAR CART
                    |--------------------------------------------------------------------------
                    */

                    $cart->items()->delete();


                    /*
                    |--------------------------------------------------------------------------
                    | NOTE
                    |--------------------------------------------------------------------------
                    |
                    | Stok TIDAK dikurangi pada tahap ini.
                    | Pengurangan stok akan dilakukan setelah
                    | pembayaran diverifikasi admin.
                    |
                    */


                    return $order;

                }
            );


            /*
            |--------------------------------------------------------------------------
            | TEMPORARY REDIRECT
            |--------------------------------------------------------------------------
            |
            | Screen 9 Payment belum dibuat.
            | Untuk sementara arahkan ke cart dengan order code.
            |
            */

return redirect()
    ->route(
        'payment.show',
        $order
    )
    ->with(
        'success',
        'Order berhasil dibuat. Silakan lakukan pembayaran.'
    );

        } catch (\RuntimeException $exception) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    $exception->getMessage()
                );

        }

    }
}