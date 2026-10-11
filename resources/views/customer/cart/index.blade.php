@extends('layouts.store')

@section('title', 'Shopping Cart')

@section('content')

<section class="cart-page">

    <div class="cart-container">

        {{-- PAGE HEADER --}}
        <div class="cart-header">

            <div>

                <span class="cart-eyebrow">
                    YOUR BAG
                </span>

                <h1>
                    Shopping Cart
                </h1>

                <p>
                    {{ $cart->items->count() }}
                    {{ $cart->items->count() === 1 ? 'item' : 'items' }}
                    in your cart
                </p>

            </div>

        </div>


        {{-- FLASH MESSAGE --}}
        @if(session('success'))

            <div class="cart-alert cart-alert-success">

                <i class="bi bi-check-circle"></i>

                {{ session('success') }}

            </div>

        @endif


        @if(session('error'))

            <div class="cart-alert cart-alert-error">

                <i class="bi bi-exclamation-circle"></i>

                {{ session('error') }}

            </div>

        @endif


        @if($errors->any())

            <div class="cart-alert cart-alert-error">

                <i class="bi bi-exclamation-circle"></i>

                {{ $errors->first() }}

            </div>

        @endif


        @if($cart->items->isEmpty())

            {{-- EMPTY CART --}}
            <div class="empty-cart">

                <div class="empty-cart-icon">

                    <i class="bi bi-bag"></i>

                </div>

                <h2>
                    Your cart is empty
                </h2>

                <p>
                    Discover our latest products and add your favorites
                    to the cart.
                </p>

                <a
                    href="{{ route('catalog.preview') }}"
                    class="continue-shopping"
                >

                    SHOP PRODUCTS

                    <i class="bi bi-arrow-right"></i>

                </a>

            </div>

        @else

            <div class="cart-layout">


                {{-- ======================
                    CART ITEMS
                ======================= --}}

                <div class="cart-items">

                    @foreach($cart->items as $item)

                        @php

                            $variant = $item->variant;

                            $product = $variant?->product;

                            $image = $variant?->images?->first()
                                ?? $product?->images?->first();

                            $itemSubtotal =
                                $item->price * $item->quantity;

                        @endphp


                        <article class="cart-item">

                            {{-- IMAGE --}}
                            <div class="cart-item-image">

                                @if(
                                    $image
                                    && file_exists(
                                        public_path($image->image_path)
                                    )
                                )

                                    <img
                                        src="{{ asset($image->image_path) }}"
                                        alt="{{ $product?->name }}"
                                    >

                                @else

                                    <div class="cart-image-placeholder">

                                        <i class="bi bi-image"></i>

                                    </div>

                                @endif

                            </div>


                            {{-- INFORMATION --}}
                            <div class="cart-item-info">

                                <div class="cart-item-top">

                                    <div>

                                        <span class="cart-category">

                                            {{ strtoupper(
                                                $product?->category?->name
                                                ?? 'PRODUCT'
                                            ) }}

                                        </span>

                                        <h2>

                                            {{ $product?->name
                                                ?? 'Product' }}

                                        </h2>

                                    </div>


                                    {{-- REMOVE --}}
                                    <form
                                        action="{{ route(
                                            'cart.destroy',
                                            $item
                                        ) }}"
                                        method="POST"
                                        onsubmit="return confirm(
                                            'Remove this product from cart?'
                                        );"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="cart-remove"
                                            title="Remove item"
                                        >

                                            <i class="bi bi-trash"></i>

                                        </button>

                                    </form>

                                </div>


                                {{-- VARIANT --}}
                                <div class="cart-variant">

                                    @if($variant?->color)

                                        <span>

                                            COLOR:
                                            <strong>
                                                {{ strtoupper($variant->color) }}
                                            </strong>

                                        </span>

                                    @endif


                                    @if($variant?->size)

                                        <span>

                                            SIZE:
                                            <strong>
                                                {{ strtoupper($variant->size) }}
                                            </strong>

                                        </span>

                                    @endif


                                    <span>

                                        SKU:
                                        <strong>
                                            {{ $variant?->sku }}
                                        </strong>

                                    </span>

                                </div>


                                {{-- PRICE --}}
                                <div class="cart-unit-price">

                                    Rp {{ number_format(
                                        $item->price,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </div>


                                <div class="cart-item-bottom">


                                    {{-- QUANTITY --}}
                                    <form
                                        action="{{ route(
                                            'cart.update',
                                            $item
                                        ) }}"
                                        method="POST"
                                        class="quantity-form"
                                    >

                                        @csrf
                                        @method('PUT')

                                        <label
                                            for="quantity-{{ $item->id }}"
                                        >
                                            QTY
                                        </label>

                                        <input
                                            id="quantity-{{ $item->id }}"
                                            type="number"
                                            name="quantity"
                                            min="1"
                                            max="{{ $variant?->stock ?? 1 }}"
                                            value="{{ $item->quantity }}"
                                        >

                                        <button
                                            type="submit"
                                        >
                                            UPDATE
                                        </button>

                                    </form>


                                    {{-- SUBTOTAL --}}
                                    <div class="cart-item-subtotal">

                                        <span>
                                            SUBTOTAL
                                        </span>

                                        <strong>

                                            Rp {{ number_format(
                                                $itemSubtotal,
                                                0,
                                                ',',
                                                '.'
                                            ) }}

                                        </strong>

                                    </div>

                                </div>


                                @if($variant)

                                    <small class="stock-information">

                                        {{ $variant->stock }}
                                        items available

                                    </small>

                                @endif

                            </div>

                        </article>

                    @endforeach

                </div>


                {{-- ======================
                    ORDER SUMMARY
                ======================= --}}

                <aside class="cart-summary">

                    <div class="cart-summary-card">

                        <h2>
                            Order Summary
                        </h2>


                        <div class="summary-row">

                            <span>
                                Subtotal
                            </span>

                            <strong>

                                Rp {{ number_format(
                                    $subtotal,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </strong>

                        </div>


                        <div class="summary-row">

                            <span>
                                Shipping
                            </span>

                            <span>
                                Calculated at checkout
                            </span>

                        </div>


                        <div class="summary-divider"></div>


                        <div class="summary-total">

                            <span>
                                TOTAL
                            </span>

                            <strong>

                                Rp {{ number_format(
                                    $subtotal,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </strong>

                        </div>


                        {{-- CHECKOUT BELUM DIAKTIFKAN --}}
                        <button
                            type="button"
                            class="checkout-button"
                            disabled
                        >

                            PROCEED TO CHECKOUT

                            <i class="bi bi-arrow-right"></i>

                        </button>


                        <p class="checkout-note">

                            Checkout will be available in the next step.

                        </p>


                        <a
                            href="{{ route('catalog.preview') }}"
                            class="continue-link"
                        >

                            <i class="bi bi-arrow-left"></i>

                            Continue Shopping

                        </a>

                    </div>

                </aside>

            </div>

        @endif

    </div>

</section>

@endsection