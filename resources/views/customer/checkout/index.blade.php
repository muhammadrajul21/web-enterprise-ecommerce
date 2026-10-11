@extends('layouts.store')

@section('title', 'Checkout')

@section('content')

@php
    $defaultAddress = $addresses
        ->firstWhere('is_default', true)
        ?? $addresses->first();

    $selectedShipping = old(
        'shipping_method',
        'regular'
    );
@endphp


<section class="checkout-page">

    <div class="checkout-container">

        {{-- HEADER --}}

        <div class="checkout-header">

            <a
                href="{{ route('cart.index') }}"
                class="checkout-back"
            >
                <i class="bi bi-arrow-left"></i>

                BACK TO CART
            </a>

            <span class="checkout-eyebrow">
                SECURE CHECKOUT
            </span>

            <h1>
                Checkout
            </h1>

            <p>
                Complete your shipping information
                and review your order.
            </p>

        </div>


        {{-- MESSAGE --}}

        @if(session('error'))

            <div class="checkout-alert checkout-alert-error">

                <i class="bi bi-exclamation-circle"></i>

                <span>
                    {{ session('error') }}
                </span>

            </div>

        @endif


        @if($errors->any())

            <div class="checkout-alert checkout-alert-error">

                <i class="bi bi-exclamation-circle"></i>

                <div>

                    <strong>
                        Please check your checkout information.
                    </strong>

                    <p>
                        {{ $errors->first() }}
                    </p>

                </div>

            </div>

        @endif


        <form
            action="{{ route('checkout.store') }}"
            method="POST"
            class="checkout-layout"
            id="checkout-form"
        >

            @csrf


            {{-- =========================
                LEFT CONTENT
            ========================= --}}

            <div class="checkout-main">


                {{-- SHIPPING ADDRESS --}}

                <section class="checkout-card">

                    <div class="checkout-card-header">

                        <div class="checkout-step">
                            1
                        </div>

                        <div>

                            <h2>
                                Shipping Address
                            </h2>

                            <p>
                                Enter the address where your
                                order should be delivered.
                            </p>

                        </div>

                    </div>


                    @if($addresses->count())

                        <div class="saved-addresses">

                            <span class="field-label">
                                SAVED ADDRESS
                            </span>

                            <div class="saved-address-grid">

                                @foreach($addresses as $address)

                                    <button
                                        type="button"
                                        class="saved-address-button"
                                        data-recipient-name="{{ $address->recipient_name }}"
                                        data-phone="{{ $address->phone }}"
                                        data-address="{{ $address->address }}"
                                        data-city="{{ $address->city }}"
                                        data-province="{{ $address->province }}"
                                        data-postal-code="{{ $address->postal_code }}"
                                    >

                                        <strong>
                                            {{ $address->label ?: 'Address' }}
                                        </strong>

                                        @if($address->is_default)

                                            <small>
                                                DEFAULT
                                            </small>

                                        @endif

                                        <span>
                                            {{ $address->recipient_name }}
                                        </span>

                                        <span>
                                            {{ $address->city }},
                                            {{ $address->province }}
                                        </span>

                                    </button>

                                @endforeach

                            </div>

                        </div>

                    @endif


                    <div class="checkout-form-grid">

                        <div class="checkout-field">

                            <label for="recipient_name">
                                RECIPIENT NAME
                            </label>

                            <input
                                type="text"
                                id="recipient_name"
                                name="recipient_name"
                                value="{{ old(
                                    'recipient_name',
                                    $defaultAddress?->recipient_name
                                        ?? auth()->user()->name
                                ) }}"
                                required
                            >

                        </div>


                        <div class="checkout-field">

                            <label for="recipient_phone">
                                PHONE NUMBER
                            </label>

                            <input
                                type="text"
                                id="recipient_phone"
                                name="recipient_phone"
                                value="{{ old(
                                    'recipient_phone',
                                    $defaultAddress?->phone
                                        ?? auth()->user()->phone
                                ) }}"
                                required
                            >

                        </div>


                        <div class="checkout-field checkout-field-full">

                            <label for="shipping_address">
                                FULL ADDRESS
                            </label>

                            <textarea
                                id="shipping_address"
                                name="shipping_address"
                                rows="4"
                                required
                            >{{ old(
                                'shipping_address',
                                $defaultAddress?->address
                            ) }}</textarea>

                        </div>


                        <div class="checkout-field">

                            <label for="shipping_city">
                                CITY
                            </label>

                            <input
                                type="text"
                                id="shipping_city"
                                name="shipping_city"
                                value="{{ old(
                                    'shipping_city',
                                    $defaultAddress?->city
                                ) }}"
                                required
                            >

                        </div>


                        <div class="checkout-field">

                            <label for="shipping_province">
                                PROVINCE
                            </label>

                            <input
                                type="text"
                                id="shipping_province"
                                name="shipping_province"
                                value="{{ old(
                                    'shipping_province',
                                    $defaultAddress?->province
                                ) }}"
                                required
                            >

                        </div>


                        <div class="checkout-field">

                            <label for="shipping_postal_code">
                                POSTAL CODE
                            </label>

                            <input
                                type="text"
                                id="shipping_postal_code"
                                name="shipping_postal_code"
                                value="{{ old(
                                    'shipping_postal_code',
                                    $defaultAddress?->postal_code
                                ) }}"
                                required
                            >

                        </div>

                    </div>

                </section>


                {{-- SHIPPING METHOD --}}

                <section class="checkout-card">

                    <div class="checkout-card-header">

                        <div class="checkout-step">
                            2
                        </div>

                        <div>

                            <h2>
                                Shipping Method
                            </h2>

                            <p>
                                Select your preferred
                                delivery option.
                            </p>

                        </div>

                    </div>


                    <div class="shipping-options">

                        @foreach(
                            $shippingOptions
                            as $key => $option
                        )

                            <label
                                class="shipping-option"
                                data-shipping-cost="{{ $option['cost'] }}"
                            >

                                <input
                                    type="radio"
                                    name="shipping_method"
                                    value="{{ $key }}"
                                    {{ $selectedShipping === $key
                                        ? 'checked'
                                        : '' }}
                                >

                                <span class="shipping-radio"></span>

                                <span class="shipping-info">

                                    <strong>
                                        {{ $option['label'] }}
                                    </strong>

                                    <small>

                                        @if($key === 'regular')

                                            Standard delivery

                                        @else

                                            Faster delivery

                                        @endif

                                    </small>

                                </span>

                                <strong class="shipping-price">

                                    Rp {{ number_format(
                                        $option['cost'],
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </strong>

                            </label>

                        @endforeach

                    </div>

                </section>


                {{-- VOUCHER --}}

                <section class="checkout-card">

                    <div class="checkout-card-header">

                        <div class="checkout-step">
                            3
                        </div>

                        <div>

                            <h2>
                                Voucher
                            </h2>

                            <p>
                                Enter your voucher code
                                if you have one.
                            </p>

                        </div>

                    </div>


                    <div class="voucher-input">

                        <input
                            type="text"
                            name="voucher_code"
                            value="{{ old('voucher_code') }}"
                            placeholder="Enter voucher code"
                            autocomplete="off"
                        >

                        <span>
                            OPTIONAL
                        </span>

                    </div>

                    <p class="voucher-note">
                        Voucher eligibility will be checked
                        when you place the order.
                    </p>

                </section>

            </div>


            {{-- =========================
                ORDER SUMMARY
            ========================= --}}

            <aside class="checkout-summary">

                <div class="checkout-summary-card">

                    <h2>
                        Order Summary
                    </h2>


                    {{-- PRODUCT LIST --}}

                    <div class="checkout-products">

                        @foreach($cart->items as $item)

                            @php
                                $variant = $item->variant;
                                $product = $variant?->product;

                                $image = $variant?->images?->first()
                                    ?? $product?->images?->first();

                                $itemSubtotal =
                                    $variant->price
                                    * $item->quantity;
                            @endphp


                            <div class="checkout-product">

                                <div class="checkout-product-image">

                                    @if(
                                        $image
                                        && file_exists(
                                            public_path(
                                                $image->image_path
                                            )
                                        )
                                    )

                                        <img
                                            src="{{ asset(
                                                $image->image_path
                                            ) }}"
                                            alt="{{ $product?->name }}"
                                        >

                                    @else

                                        <div class="checkout-image-placeholder">

                                            <i class="bi bi-image"></i>

                                        </div>

                                    @endif

                                </div>


                                <div class="checkout-product-info">

                                    <strong>
                                        {{ $product?->name }}
                                    </strong>

                                    <span>

                                        @if($variant?->color)

                                            {{ $variant->color }}

                                        @endif

                                        @if(
                                            $variant?->color
                                            && $variant?->size
                                        )

                                            /

                                        @endif

                                        @if($variant?->size)

                                            {{ $variant->size }}

                                        @endif

                                    </span>

                                    <small>
                                        Qty {{ $item->quantity }}
                                    </small>

                                </div>


                                <strong class="checkout-product-price">

                                    Rp {{ number_format(
                                        $itemSubtotal,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </strong>

                            </div>

                        @endforeach

                    </div>


                    <div class="summary-divider"></div>


                    {{-- SUBTOTAL --}}

                    <div class="checkout-summary-row">

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


                    {{-- SHIPPING --}}

                    <div class="checkout-summary-row">

                        <span>
                            Shipping
                        </span>

                        <strong id="checkout-shipping-price">

                            Rp {{ number_format(
                                $shippingOptions[
                                    $selectedShipping
                                ]['cost'],
                                0,
                                ',',
                                '.'
                            ) }}

                        </strong>

                    </div>


                    {{-- VOUCHER --}}

                    <div class="checkout-summary-row">

                        <span>
                            Voucher
                        </span>

                        <span>
                            Applied after validation
                        </span>

                    </div>


                    <div class="summary-divider"></div>


                    {{-- TOTAL --}}

                    <div class="checkout-total">

                        <span>
                            ESTIMATED TOTAL
                        </span>

                        <strong
                            id="checkout-total-price"
                            data-subtotal="{{ $subtotal }}"
                        >

                            Rp {{ number_format(
                                $subtotal
                                + $shippingOptions[
                                    $selectedShipping
                                ]['cost'],
                                0,
                                ',',
                                '.'
                            ) }}

                        </strong>

                    </div>


                    {{-- PLACE ORDER --}}

                    <button
                        type="submit"
                        class="place-order-button"
                    >

                        PLACE ORDER

                        <i class="bi bi-arrow-right"></i>

                    </button>


                    <p class="checkout-summary-note">

                        By placing your order, you confirm
                        that the shipping information above
                        is correct.

                    </p>


                    <a
                        href="{{ route('cart.index') }}"
                        class="return-cart-link"
                    >

                        <i class="bi bi-arrow-left"></i>

                        Return to Cart

                    </a>

                </div>

            </aside>

        </form>

    </div>

</section>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | SHIPPING PRICE
    |--------------------------------------------------------------------------
    */

    const shippingInputs =
        document.querySelectorAll(
            'input[name="shipping_method"]'
        );

    const totalElement =
        document.getElementById(
            'checkout-total-price'
        );

    const shippingPriceElement =
        document.getElementById(
            'checkout-shipping-price'
        );


    if (
        totalElement
        && shippingPriceElement
    ) {

        const subtotal =
            Number(
                totalElement.dataset.subtotal
            );


        const formatRupiah = function (value) {

            return 'Rp '
                + new Intl.NumberFormat(
                    'id-ID',
                    {
                        maximumFractionDigits: 0
                    }
                ).format(value);

        };


        shippingInputs.forEach(
            function (input) {

                input.addEventListener(
                    'change',
                    function () {

                        const label =
                            input.closest(
                                '.shipping-option'
                            );

                        if (!label) {
                            return;
                        }


                        const shippingCost =
                            Number(
                                label.dataset.shippingCost
                            );


                        shippingPriceElement.textContent =
                            formatRupiah(
                                shippingCost
                            );


                        totalElement.textContent =
                            formatRupiah(
                                subtotal
                                + shippingCost
                            );

                    }
                );

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | SAVED ADDRESS
    |--------------------------------------------------------------------------
    */

    const savedAddresses =
        document.querySelectorAll(
            '.saved-address-button'
        );


    savedAddresses.forEach(
        function (button) {

            button.addEventListener(
                'click',
                function () {

                    savedAddresses.forEach(
                        function (item) {

                            item.classList.remove(
                                'active'
                            );

                        }
                    );


                    button.classList.add(
                        'active'
                    );


                    document.getElementById(
                        'recipient_name'
                    ).value =
                        button.dataset.recipientName
                        || '';


                    document.getElementById(
                        'recipient_phone'
                    ).value =
                        button.dataset.phone
                        || '';


                    document.getElementById(
                        'shipping_address'
                    ).value =
                        button.dataset.address
                        || '';


                    document.getElementById(
                        'shipping_city'
                    ).value =
                        button.dataset.city
                        || '';


                    document.getElementById(
                        'shipping_province'
                    ).value =
                        button.dataset.province
                        || '';


                    document.getElementById(
                        'shipping_postal_code'
                    ).value =
                        button.dataset.postalCode
                        || '';

                }
            );

        }
    );

});
</script>

@endpush