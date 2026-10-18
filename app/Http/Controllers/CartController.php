<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Menampilkan cart.
     *
     * Guest  -> session
     * Login  -> database
     */
    public function index()
    {
        $this->ensureCustomerOrGuest();

        /*
         * CUSTOMER LOGIN
         */
        if (auth()->check()) {

            $cart = Cart::firstOrCreate([
                'user_id' => auth()->id(),
            ]);

            $cart->load([
                'items.variant.product.category',
                'items.variant.product.images',
                'items.variant.images',
            ]);
        }

        /*
         * GUEST
         */
        else {

            $guestCart = session(
                'guest_cart',
                []
            );

            $variantIds = array_map(
                'intval',
                array_keys($guestCart)
            );

            $variants = ProductVariant::with([
                'product.category',
                'product.images',
                'images',
            ])
                ->whereIn(
                    'id',
                    $variantIds
                )
                ->get()
                ->keyBy('id');


            $items = collect();


            foreach ($guestCart as $variantId => $quantity) {

                $variant = $variants->get(
                    (int) $variantId
                );

                if (!$variant) {
                    continue;
                }


                $items->push(
                    (object) [
                        'variant_id' =>
                            $variant->id,

                        'quantity' =>
                            (int) $quantity,

                        'price' =>
                            $variant->price,

                        'variant' =>
                            $variant,
                    ]
                );
            }


            $cart = (object) [
                'items' => $items,
            ];
        }


        $subtotal = $cart->items
            ->sum(function ($item) {

                return
                    $item->price
                    * $item->quantity;
            });


        return view(
            'customer.cart.index',
            compact(
                'cart',
                'subtotal'
            )
        );
    }


    /**
     * Menambahkan produk ke cart.
     */
    public function store(Request $request)
    {
        $this->ensureCustomerOrGuest();


        $validated = $request->validate([

            'variant_id' => [
                'required',
                'exists:product_variants,id',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],

        ]);


        $variant = ProductVariant::with(
            'product'
        )
            ->findOrFail(
                $validated['variant_id']
            );


        if ($variant->status !== 'active') {

            return back()->with(
                'error',
                'Variant produk sedang tidak tersedia.'
            );
        }


        if (
            $validated['quantity']
            > $variant->stock
        ) {

            return back()->with(
                'error',
                'Stock produk tidak mencukupi.'
            );
        }


        /*
         * CUSTOMER LOGIN
         */
        if (auth()->check()) {

            $cart = Cart::firstOrCreate([
                'user_id' => auth()->id(),
            ]);


            $cartItem = CartItem::where(
                'cart_id',
                $cart->id
            )
                ->where(
                    'variant_id',
                    $variant->id
                )
                ->first();


            if ($cartItem) {

                $newQuantity =
                    $cartItem->quantity
                    + $validated['quantity'];


                if (
                    $newQuantity
                    > $variant->stock
                ) {

                    return back()->with(
                        'error',
                        'Jumlah produk melebihi stock yang tersedia.'
                    );
                }


                $cartItem->update([

                    'quantity' =>
                        $newQuantity,

                    'price' =>
                        $variant->price,

                ]);
            } else {

                CartItem::create([

                    'cart_id' =>
                        $cart->id,

                    'variant_id' =>
                        $variant->id,

                    'quantity' =>
                        $validated['quantity'],

                    'price' =>
                        $variant->price,

                ]);
            }
        }

        /*
         * GUEST
         */
        else {

            $guestCart = session(
                'guest_cart',
                []
            );


            $currentQuantity =
                (int) (
                    $guestCart[
                        $variant->id
                    ] ?? 0
                );


            $newQuantity =
                $currentQuantity
                + $validated['quantity'];


            if (
                $newQuantity
                > $variant->stock
            ) {

                return back()->with(
                    'error',
                    'Jumlah produk melebihi stock yang tersedia.'
                );
            }


            $guestCart[
                $variant->id
            ] = $newQuantity;


            session()->put(
                'guest_cart',
                $guestCart
            );
        }


        return redirect()
            ->route('cart.index')
            ->with(
                'success',
                'Produk berhasil ditambahkan ke keranjang.'
            );
    }


    /**
     * Mengubah quantity item.
     */
    public function update(
        Request $request,
        int $variantId
    ) {
        $this->ensureCustomerOrGuest();


        $validated = $request->validate([

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],

        ]);


        $variant = ProductVariant::findOrFail(
            $variantId
        );


        if ($variant->status !== 'active') {

            return back()->with(
                'error',
                'Variant produk sedang tidak tersedia.'
            );
        }


        if (
            $validated['quantity']
            > $variant->stock
        ) {

            return back()->with(
                'error',
                'Jumlah produk melebihi stock yang tersedia.'
            );
        }


        /*
         * CUSTOMER LOGIN
         */
        if (auth()->check()) {

            $cart = Cart::where(
                'user_id',
                auth()->id()
            )
                ->firstOrFail();


            $cartItem = CartItem::where(
                'cart_id',
                $cart->id
            )
                ->where(
                    'variant_id',
                    $variant->id
                )
                ->firstOrFail();


            $cartItem->update([

                'quantity' =>
                    $validated['quantity'],

                'price' =>
                    $variant->price,

            ]);
        }

        /*
         * GUEST
         */
        else {

            $guestCart = session(
                'guest_cart',
                []
            );


            if (
                !array_key_exists(
                    $variant->id,
                    $guestCart
                )
            ) {

                abort(404);
            }


            $guestCart[
                $variant->id
            ] = $validated['quantity'];


            session()->put(
                'guest_cart',
                $guestCart
            );
        }


        return redirect()
            ->route('cart.index')
            ->with(
                'success',
                'Jumlah produk berhasil diperbarui.'
            );
    }


    /**
     * Menghapus produk dari cart.
     */
    public function destroy(
        int $variantId
    ) {
        $this->ensureCustomerOrGuest();


        /*
         * CUSTOMER LOGIN
         */
        if (auth()->check()) {

            $cart = Cart::where(
                'user_id',
                auth()->id()
            )
                ->firstOrFail();


            $cartItem = CartItem::where(
                'cart_id',
                $cart->id
            )
                ->where(
                    'variant_id',
                    $variantId
                )
                ->firstOrFail();


            $cartItem->delete();
        }

        /*
         * GUEST
         */
        else {

            $guestCart = session(
                'guest_cart',
                []
            );


            unset(
                $guestCart[
                    $variantId
                ]
            );


            session()->put(
                'guest_cart',
                $guestCart
            );
        }


        return redirect()
            ->route('cart.index')
            ->with(
                'success',
                'Produk berhasil dihapus dari keranjang.'
            );
    }


    /**
     * Cart hanya boleh digunakan oleh:
     *
     * - Guest
     * - Customer
     *
     * Admin / Staff / Owner tidak boleh
     * menggunakan customer cart.
     */
    private function ensureCustomerOrGuest(): void
    {
        if (!auth()->check()) {
            return;
        }


        abort_unless(
            auth()->user()->role?->name
                === 'customer',
            403
        );
    }
}