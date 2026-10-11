<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Menampilkan cart customer.
     */
    public function index()
    {
        $cart = Cart::firstOrCreate([
            'user_id' => auth()->id(),
        ]);

        $cart->load([
            'items.variant.product',
            'items.variant.images',
        ]);

        $subtotal = $cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        return view(
            'customer.cart.index',
            compact('cart', 'subtotal')
        );
    }


    /**
     * Menambahkan produk ke cart.
     */
    public function store(Request $request)
    {
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

        $variant = ProductVariant::with('product')
            ->findOrFail($validated['variant_id']);

        // Variant harus aktif
        if ($variant->status !== 'active') {
            return back()->with(
                'error',
                'Variant produk sedang tidak tersedia.'
            );
        }

        // Quantity tidak boleh melebihi stock
        if ($validated['quantity'] > $variant->stock) {
            return back()->with(
                'error',
                'Stock produk tidak mencukupi.'
            );
        }

        $cart = Cart::firstOrCreate([
            'user_id' => auth()->id(),
        ]);

        // Cek apakah variant yang sama sudah ada di cart
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('variant_id', $variant->id)
            ->first();

        if ($cartItem) {

            $newQuantity =
                $cartItem->quantity
                + $validated['quantity'];

            if ($newQuantity > $variant->stock) {
                return back()->with(
                    'error',
                    'Jumlah produk melebihi stock yang tersedia.'
                );
            }

            $cartItem->update([
                'quantity' => $newQuantity,
                'price' => $variant->price,
            ]);

        } else {

            CartItem::create([
                'cart_id' => $cart->id,
                'variant_id' => $variant->id,
                'quantity' => $validated['quantity'],
                'price' => $variant->price,
            ]);
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
        CartItem $cartItem
    ) {
        $this->ensureOwner($cartItem);

        $validated = $request->validate([
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $cartItem->load('variant');

        if (
            $validated['quantity']
            > $cartItem->variant->stock
        ) {
            return back()->with(
                'error',
                'Jumlah produk melebihi stock yang tersedia.'
            );
        }

        $cartItem->update([
            'quantity' => $validated['quantity'],

            // Harga mengikuti harga variant terbaru
            'price' => $cartItem->variant->price,
        ]);

        return redirect()
            ->route('cart.index')
            ->with(
                'success',
                'Jumlah produk berhasil diperbarui.'
            );
    }


    /**
     * Menghapus item dari cart.
     */
    public function destroy(CartItem $cartItem)
    {
        $this->ensureOwner($cartItem);

        $cartItem->delete();

        return redirect()
            ->route('cart.index')
            ->with(
                'success',
                'Produk berhasil dihapus dari keranjang.'
            );
    }


    /**
     * Memastikan CartItem milik customer
     * yang sedang login.
     */
    private function ensureOwner(
        CartItem $cartItem
    ): void {
        $cartItem->loadMissing('cart');

        abort_unless(
            $cartItem->cart->user_id === auth()->id(),
            403
        );
    }
}