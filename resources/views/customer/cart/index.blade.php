@extends('layouts.store')

@section('title', 'Shopping Cart')

@section('content')

@php
$displaySubtotal = $requiresSelection ? 0 : $subtotal;
@endphp

<section class="cart-page">
    <div class="cart-container">

        <div class="cart-header">
            <div>
                <span class="cart-eyebrow">YOUR BAG</span>
                <h1>Shopping Cart</h1>
                <p>
                    {{ $cart->items->count() }}
                    {{ $cart->items->count() === 1 ? 'item' : 'items' }}
                    in your cart
                </p>
            </div>
        </div>

        @if(session('success'))
        <div class="cart-alert cart-alert-success">
            <i class="bi bi-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="cart-alert cart-alert-error">
            <i class="bi bi-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="cart-alert cart-alert-error">
            <i class="bi bi-exclamation-circle"></i>
            <span>{{ $errors->first() }}</span>
        </div>
        @endif

        @if($cart->items->isEmpty())

        <div class="empty-cart">
            <div class="empty-cart-icon">
                <i class="bi bi-bag"></i>
            </div>

            <h2>Your cart is empty</h2>

            <p>
                Discover our latest products and add
                your favorites to the cart.
            </p>

            <a
                href="{{ route('catalog.preview') }}"
                class="continue-shopping">
                SHOP PRODUCTS
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        @else

        @if($requiresSelection)
        <div class="cart-selection-notice">
            <i class="bi bi-info-circle"></i>
            <span>
                Select the products you want to purchase.
                Unselected products will remain in your cart.
            </span>
        </div>
        @endif

        <div class="cart-layout">

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

                <article
                    class="cart-item"
                    data-item-subtotal="{{ $itemSubtotal }}">

                    <div class="cart-item-image">

                        @if(
                        $image
                        && file_exists(public_path($image->image_path))
                        )
                        <img
                            src="{{ asset($image->image_path) }}"
                            alt="{{ $product?->name }}">
                        @else
                        <div class="cart-image-placeholder">
                            <i class="bi bi-image"></i>
                        </div>
                        @endif

                    </div>

                    <div class="cart-item-info">

                        @if($requiresSelection)
                        <div class="cart-item-select">
                            <label>
                                <input
                                    type="checkbox"
                                    class="cart-select-checkbox"
                                    name="selected_variants[]"
                                    value="{{ $item->variant_id }}"
                                    @checked(in_array(
                                    (int) $item->variant_id,
                                (array) session('checkout_variant_ids', []),
                                true
                                ))
                                data-subtotal="{{ $itemSubtotal }}"
                                form="cart-checkout-form"
                                >
                                <span>Select this product</span>
                            </label>
                        </div>
                        @endif

                        <div class="cart-item-top">

                            <div>
                                <span class="cart-category">
                                    {{ strtoupper(
                                                $product?->category?->name ?? 'PRODUCT'
                                            ) }}
                                </span>

                                <h2>
                                    {{ $product?->name ?? 'Product' }}
                                </h2>
                            </div>

                            <form
                                action="{{ route('cart.destroy', $item->variant_id) }}"
                                method="POST"
                                onsubmit="return confirm('Remove this product from cart?');">
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="cart-remove"
                                    title="Remove item">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>

                        </div>

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
                                    {{ $variant?->sku ?? '-' }}
                                </strong>
                            </span>

                        </div>

                        <div class="cart-unit-price">
                            Rp {{ number_format(
                                        $item->price,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                        </div>

                        <div class="cart-item-bottom">

                            <form
                                action="{{ route('cart.update', $item->variant_id) }}"
                                method="POST"
                                class="quantity-form">
                                @csrf
                                @method('PUT')

                                <label for="quantity-{{ $item->variant_id }}">
                                    QTY
                                </label>

                                <input
                                    id="quantity-{{ $item->variant_id }}"
                                    type="number"
                                    name="quantity"
                                    min="1"
                                    max="{{ $variant?->stock ?? 1 }}"
                                    value="{{ $item->quantity }}"
                                    required>

                                <button type="submit">
                                    UPDATE
                                </button>
                            </form>

                            <div class="cart-item-subtotal">
                                <span>SUBTOTAL</span>

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
                            {{ $variant->stock }} items available
                        </small>
                        @endif

                    </div>

                </article>

                @endforeach

            </div>

            <aside class="cart-summary">

                <div class="cart-summary-card">

                    <h2>Order Summary</h2>

                    @if($requiresSelection)
                    <p class="cart-selected-count" id="cart-selected-count">
                        0 products selected
                    </p>
                    @endif

                    <div class="summary-row">
                        <span>Subtotal</span>

                        <strong id="cart-selected-subtotal">
                            Rp {{ number_format(
                                    $displaySubtotal,
                                    0,
                                    ',',
                                    '.'
                                ) }}
                        </strong>
                    </div>

                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>Calculated at checkout</span>
                    </div>

                    <div class="summary-divider"></div>

                    <div class="summary-total">
                        <span>TOTAL</span>

                        <strong id="cart-selected-total">
                            Rp {{ number_format(
                                    $displaySubtotal,
                                    0,
                                    ',',
                                    '.'
                                ) }}
                        </strong>
                    </div>

                    <form
                        action="{{ route('cart.checkout') }}"
                        method="POST"
                        id="cart-checkout-form">
                        @csrf

                        <button
                            type="submit"
                            class="checkout-button"
                            id="cart-checkout-button"
                            @if($requiresSelection) disabled @endif>
                            PROCEED TO CHECKOUT
                            <i class="bi bi-arrow-right"></i>
                        </button>

                    </form>

                    @if($requiresSelection)
                    <p class="checkout-note">
                        Select at least one product to continue.
                    </p>
                    @endif

                    <a
                        href="{{ route('catalog.preview') }}"
                        class="continue-link">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const checkboxes = document.querySelectorAll(
            '.cart-select-checkbox'
        );

        const subtotalElement = document.getElementById(
            'cart-selected-subtotal'
        );

        const totalElement = document.getElementById(
            'cart-selected-total'
        );

        const selectedCountElement = document.getElementById(
            'cart-selected-count'
        );

        const checkoutButton = document.getElementById(
            'cart-checkout-button'
        );

        if (
            !checkboxes.length ||
            !subtotalElement ||
            !totalElement ||
            !checkoutButton
        ) {
            return;
        }

        const formatter = new Intl.NumberFormat('id-ID', {
            maximumFractionDigits: 0
        });

        function updateSelectedProducts() {

            let subtotal = 0;
            let selectedCount = 0;

            checkboxes.forEach(function(checkbox) {

                if (checkbox.checked) {

                    subtotal += Number(
                        checkbox.dataset.subtotal || 0
                    );

                    selectedCount++;
                }
            });

            const formattedTotal = 'Rp ' + formatter.format(subtotal);

            subtotalElement.textContent = formattedTotal;
            totalElement.textContent = formattedTotal;

            selectedCountElement.textContent =
                selectedCount + ' products selected';

            checkoutButton.disabled = selectedCount === 0;
        }

        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener(
                'change',
                updateSelectedProducts
            );
        });

        updateSelectedProducts();

    });
</script>
@endpush