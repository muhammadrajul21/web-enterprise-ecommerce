<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Voucher;
use App\Services\ShippingEstimateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    /**
     * Menampilkan halaman checkout.
     *
     * Mode cart    : seluruh isi keranjang.
     * Mode buy_now : hanya produk yang dipilih.
     */
    public function index(
        Request $request,
        ShippingEstimateService $shippingEstimateService
    ) {

        /*
        |--------------------------------------------------------------------------
        | DETERMINE CHECKOUT MODE
        |--------------------------------------------------------------------------
        */

        if ($request->query('mode') === 'cart') {

            $request->session()->forget([
                'buy_now',
                'checkout_mode',
            ]);
        } elseif (
            $request->query('mode') === 'buy_now'
            && $request->session()->has('buy_now')
        ) {

            $request->session()->put(
                'checkout_mode',
                'buy_now'
            );
        }

        $isBuyNow =
            $request->session()->get('checkout_mode') === 'buy_now'
            && $request->session()->has('buy_now');


        /*
        |--------------------------------------------------------------------------
        | BUY NOW
        |--------------------------------------------------------------------------
        */

        if ($isBuyNow) {

            $buyNow = $request->session()->get('buy_now', []);

            $variantId = (int) ($buyNow['variant_id'] ?? 0);

            $quantity = (int) ($buyNow['quantity'] ?? 0);

            $variant = ProductVariant::with([
                'product.category',
                'product.images',
                'images',
            ])->find($variantId);

            if (
                !$variant
                || $variant->status !== 'active'
                || $quantity < 1
                || $quantity > $variant->stock
            ) {

                $request->session()->forget([
                    'buy_now',
                    'checkout_mode',
                ]);

                return redirect()
                    ->route('catalog.preview')
                    ->with(
                        'error',
                        'Produk tidak tersedia atau stok sudah berubah. Silakan pilih kembali.'
                    );
            }

            /*
             * Struktur sementara agar halaman Checkout
             * tetap menggunakan format cart->items.
             *
             * Cart database tidak disentuh.
             */

            $cart = (object) [
                'items' => collect([
                    (object) [
                        'variant_id' => $variant->id,
                        'variant' => $variant,
                        'quantity' => $quantity,
                        'price' => $variant->price,
                    ],
                ]),
            ];
        } else {

            /*
             |--------------------------------------------------------------------------
             | NORMAL CART CHECKOUT
             |--------------------------------------------------------------------------
             */

            $cart = Cart::with([
                'items.variant.product.category',
                'items.variant.product.images',
                'items.variant.images',
            ])
                ->where('user_id', auth()->id())
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                return redirect()
                    ->route('cart.index')
                    ->with('error', 'Keranjang masih kosong.');
            }

            // Ambil hanya varian yang dipilih dari halaman Cart.
            $selectedVariantIds = array_values(array_unique(
                array_map(
                    'intval',
                    (array) $request->session()->get(
                        'checkout_variant_ids',
                        []
                    )
                )
            ));

            if (empty($selectedVariantIds)) {
                return redirect()
                    ->route('cart.index')
                    ->with(
                        'error',
                        'Pilih produk terlebih dahulu sebelum Checkout.'
                    );
            }

            // Pastikan semua pilihan masih tersedia di Cart akun.
            $selectedItems = $cart->items
                ->whereIn('variant_id', $selectedVariantIds)
                ->values();

            if ($selectedItems->count() !== count($selectedVariantIds)) {
                $request->session()->forget('checkout_variant_ids');

                return redirect()
                    ->route('cart.index')
                    ->with(
                        'error',
                        'Pilihan produk tidak valid. Silakan pilih kembali.'
                    );
            }

            // Halaman Checkout hanya menerima produk terpilih.
            $cart->setRelation('items', $selectedItems);
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
                    ->route($isBuyNow ? 'catalog.preview' : 'cart.index')
                    ->with(
                        'error',
                        'Salah satu varian produk sudah tidak tersedia.'
                    );
            }

            if ($variant->status !== 'active') {

                return redirect()
                    ->route($isBuyNow ? 'catalog.preview' : 'cart.index')
                    ->with(
                        'error',
                        'Salah satu produk sedang tidak aktif.'
                    );
            }

            if ($item->quantity > $variant->stock) {

                return redirect()
                    ->route($isBuyNow ? 'catalog.preview' : 'cart.index')
                    ->with(
                        'error',
                        'Jumlah produk melebihi stok yang tersedia.'
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | CURRENT SUBTOTAL
        |--------------------------------------------------------------------------
        */

        $subtotal = $cart->items->sum(function ($item) {

            return $item->variant->price * $item->quantity;
        });


        /*
        |--------------------------------------------------------------------------
        | AVAILABLE VOUCHERS
        |--------------------------------------------------------------------------
        */

        $availableVouchers = Voucher::query()
            ->where('is_active', true)
            ->where(function ($query) {

                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {

                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->where(function ($query) {

                $query->whereNull('usage_limit')
                    ->orWhereColumn('used_count', '<', 'usage_limit');
            })
            ->orderBy('expires_at')
            ->get();


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
        | PROVINCES AND REGENCIES
        |--------------------------------------------------------------------------
        */

        $provinces = Province::with([
            'regencies' => function ($query) {
                $query->orderBy('name');
            },
        ])
            ->orderBy('name')
            ->get(['id', 'name']);


        /*
        |--------------------------------------------------------------------------
        | SHIPPING OPTIONS
        |--------------------------------------------------------------------------
        */

        $provinceOptions = $shippingEstimateService->supportedProvinces();

        $defaultAddress = $addresses->firstWhere('is_default', true)
            ?? $addresses->first();

        $selectedProvince = old(
            'shipping_province',
            $defaultAddress?->province ?? 'Aceh'
        );

        if (!in_array($selectedProvince, $provinceOptions, true)) {
            $selectedProvince = 'Aceh';
        }

        $selectedShipping = old('shipping_method', 'regular');

        if (!in_array($selectedShipping, ['regular', 'express'], true)) {
            $selectedShipping = 'regular';
        }


        /*
        |--------------------------------------------------------------------------
        | SHIPPING QUOTES
        |--------------------------------------------------------------------------
        */

        $shippingOptions = [];

        foreach (['regular', 'express'] as $method) {

            $shippingOptions[$method] =
                $shippingEstimateService->quote(
                    $selectedProvince,
                    $method
                );
        }


        /*
        |--------------------------------------------------------------------------
        | SHIPPING QUOTES FOR ALL PROVINCES
        |--------------------------------------------------------------------------
        */

        $shippingQuotes = [];

        foreach ($provinceOptions as $province) {

            foreach (['regular', 'express'] as $method) {

                $shippingQuotes[$province][$method] =
                    $shippingEstimateService->quote(
                        $province,
                        $method
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | CHECKOUT VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'customer.checkout.index',
            compact(
                'cart',
                'isBuyNow',
                'subtotal',
                'addresses',
                'shippingOptions',
                'provinceOptions',
                'provinces',
                'selectedProvince',
                'shippingQuotes',
                'selectedShipping',
                'availableVouchers'
            )
        );
    }


    /**
     * Membuat order dari Cart atau Buy Now.
     */
    public function store(
        Request $request,
        ShippingEstimateService $shippingEstimateService
    ) {

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
        | VALIDATE REGENCY AND PROVINCE
        |--------------------------------------------------------------------------
        */

        $isValidCity = Regency::query()
            ->where('name', $validated['shipping_city'])
            ->whereHas('province', function ($query) use ($validated) {

                $query->where(
                    'name',
                    $validated['shipping_province']
                );
            })
            ->exists();

        if (!$isValidCity) {

            throw ValidationException::withMessages([

                'shipping_city' =>
                'Kabupaten/kota tidak sesuai dengan provinsi yang dipilih.',

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | SHIPPING QUOTE
        |--------------------------------------------------------------------------
        */

        $shipping = $shippingEstimateService->quote(
            $validated['shipping_province'],
            $validated['shipping_method']
        );


        /*
        |--------------------------------------------------------------------------
        | DETERMINE CHECKOUT ITEMS
        |--------------------------------------------------------------------------
        */

        $isBuyNow =
            $request->session()->get('checkout_mode') === 'buy_now'
            && $request->session()->has('buy_now');

        $cart = null;

        $checkoutItems = collect();
        $checkoutVariantIds = [];


        if ($isBuyNow) {

            /*
             * BUY NOW:
             * Ambil hanya produk dari session.
             */

            $buyNow = $request->session()->get('buy_now', []);

            $variantId = (int) ($buyNow['variant_id'] ?? 0);

            $quantity = (int) ($buyNow['quantity'] ?? 0);

            $variant = ProductVariant::find($variantId);

            if (
                !$variant
                || $variant->status !== 'active'
                || $quantity < 1
                || $quantity > $variant->stock
            ) {

                $request->session()->forget([
                    'buy_now',
                    'checkout_mode',
                ]);

                return redirect()
                    ->route('catalog.preview')
                    ->with(
                        'error',
                        'Produk tidak tersedia atau stok sudah berubah.'
                    );
            }

            $checkoutItems = collect([
                (object) [
                    'variant_id' => $variant->id,
                    'quantity' => $quantity,
                ],
            ]);
        } else {

            /*
             |--------------------------------------------------------------------------
             | CART:
             | Ambil hanya item yang dipilih Customer.
             |--------------------------------------------------------------------------
             */

            $cart = Cart::with([
                'items.variant.product',
            ])
                ->where('user_id', auth()->id())
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                return redirect()
                    ->route('cart.index')
                    ->with('error', 'Keranjang masih kosong.');
            }

            $checkoutVariantIds = array_values(array_unique(
                array_map(
                    'intval',
                    (array) $request->session()->get(
                        'checkout_variant_ids',
                        []
                    )
                )
            ));

            if (empty($checkoutVariantIds)) {
                return redirect()
                    ->route('cart.index')
                    ->with(
                        'error',
                        'Pilih produk terlebih dahulu sebelum Checkout.'
                    );
            }

            $checkoutItems = $cart->items
                ->whereIn('variant_id', $checkoutVariantIds)
                ->values();

            if ($checkoutItems->count() !== count($checkoutVariantIds)) {
                $request->session()->forget('checkout_variant_ids');

                return redirect()
                    ->route('cart.index')
                    ->with(
                        'error',
                        'Pilihan produk tidak valid. Silakan pilih kembali.'
                    );
            }
        }



        /*
        |--------------------------------------------------------------------------
        | CREATE ORDER
        |--------------------------------------------------------------------------
        */

        try {

            $order = DB::transaction(
                function () use (
                    $validated,
                    $shipping,
                    $cart,
                    $checkoutItems,
                    $checkoutVariantIds,
                    $isBuyNow
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | LOCK AND VALIDATE VARIANTS
                    |--------------------------------------------------------------------------
                    */

                    $subtotal = 0;

                    $lockedVariants = [];

                    foreach ($checkoutItems as $item) {

                        $variant = ProductVariant::with('product')
                            ->where('id', $item->variant_id)
                            ->lockForUpdate()
                            ->first();

                        if (!$variant || !$variant->product) {

                            throw new \RuntimeException(
                                'Salah satu produk sudah tidak tersedia.'
                            );
                        }

                        if ($variant->status !== 'active') {

                            throw new \RuntimeException(
                                'Salah satu produk sedang tidak aktif.'
                            );
                        }

                        if ($item->quantity > $variant->stock) {

                            throw new \RuntimeException(
                                'Stok salah satu produk tidak mencukupi.'
                            );
                        }

                        $lockedVariants[$variant->id] = $variant;

                        $subtotal +=
                            $variant->price * $item->quantity;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | VOUCHER
                    |--------------------------------------------------------------------------
                    */

                    $voucher = null;

                    $discountAmount = 0;

                    if (!empty($validated['voucher_code'])) {

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
                            && now()->lt($voucher->starts_at)
                        ) {

                            throw new \RuntimeException(
                                'Voucher belum dapat digunakan.'
                            );
                        }

                        if (
                            $voucher->expires_at
                            && now()->gt($voucher->expires_at)
                        ) {

                            throw new \RuntimeException(
                                'Voucher sudah kedaluwarsa.'
                            );
                        }

                        if (
                            $voucher->usage_limit !== null
                            && $voucher->used_count >= $voucher->usage_limit
                        ) {

                            throw new \RuntimeException(
                                'Batas penggunaan voucher telah habis.'
                            );
                        }

                        if ($subtotal < $voucher->min_order_amount) {

                            throw new \RuntimeException(
                                'Subtotal belum memenuhi minimum penggunaan voucher.'
                            );
                        }

                        if ($voucher->discount_type === 'percentage') {

                            $discountAmount = $subtotal
                                * ($voucher->discount_value / 100);

                            if ($voucher->max_discount_amount !== null) {

                                $discountAmount = min(
                                    $discountAmount,
                                    $voucher->max_discount_amount
                                );
                            }
                        } else {

                            $discountAmount = $voucher->discount_value;
                        }

                        $discountAmount = min(
                            $discountAmount,
                            $subtotal
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CALCULATE TOTAL
                    |--------------------------------------------------------------------------
                    */

                    $shippingCost = $shipping['cost'];

                    $totalAmount = $subtotal
                        - $discountAmount
                        + $shippingCost;


                    /*
                    |--------------------------------------------------------------------------
                    | GENERATE ORDER CODE
                    |--------------------------------------------------------------------------
                    */

                    do {

                        $orderCode = 'ORD-'
                            . now()->format('Ymd')
                            . '-'
                            . strtoupper(Str::random(6));
                    } while (
                        Order::where('order_code', $orderCode)->exists()
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | CREATE ORDER
                    |--------------------------------------------------------------------------
                    */

                    $order = Order::create([

                        'user_id' => auth()->id(),

                        'voucher_id' => $voucher?->id,

                        'order_code' => $orderCode,

                        'status' => 'pending_payment',

                        'recipient_name' => $validated['recipient_name'],

                        'recipient_phone' => $validated['recipient_phone'],

                        'shipping_address' => $validated['shipping_address'],

                        'shipping_city' => $validated['shipping_city'],

                        'shipping_province' => $validated['shipping_province'],

                        'shipping_postal_code' => $validated['shipping_postal_code'],

                        'subtotal' => $subtotal,

                        'discount_amount' => $discountAmount,

                        'shipping_cost' => $shippingCost,

                        'total_amount' => $totalAmount,

                        'courier' => null,

                        'shipping_service' => $shipping['label'],

                        'shipping_method' => $validated['shipping_method'],

                        'estimated_delivery_start' =>
                        $shipping['estimated_delivery_start'],

                        'estimated_delivery_end' =>
                        $shipping['estimated_delivery_end'],

                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | CREATE ORDER ITEMS
                    |--------------------------------------------------------------------------
                    */

                    foreach ($checkoutItems as $item) {

                        $variant = $lockedVariants[$item->variant_id];

                        $price = $variant->price;

                        OrderItem::create([

                            'order_id' => $order->id,

                            'variant_id' => $variant->id,

                            'product_name' => $variant->product->name,

                            'sku' => $variant->sku,

                            'color' => $variant->color,

                            'size' => $variant->size,

                            'price' => $price,

                            'quantity' => $item->quantity,

                            'subtotal' => $price * $item->quantity,

                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CLEAR CART ONLY FOR NORMAL CART CHECKOUT
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !$isBuyNow
                        && $cart
                        && !empty($checkoutVariantIds)
                    ) {
                        $cart->items()
                            ->whereIn('variant_id', $checkoutVariantIds)
                            ->delete();
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | STOCK NOTE
                    |--------------------------------------------------------------------------
                    |
                    | Stok tidak dikurangi saat checkout.
                    | Pengurangan stok tetap dilakukan setelah
                    | pembayaran diverifikasi.
                    |
                    */

                    return $order;
                }
            );


            /*
            |--------------------------------------------------------------------------
            | CLEAR TEMPORARY CHECKOUT SESSION
            |--------------------------------------------------------------------------
            */

            $request->session()->forget([
                'buy_now',
                'checkout_mode',
                'checkout_variant_ids',
            ]);

            /*
            |--------------------------------------------------------------------------
            | REDIRECT TO PAYMENT
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route('payment.show', $order)
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
