@extends('layouts.store')

@section('title', 'Checkout')

@section('content')

@php
    $defaultAddress = $addresses->firstWhere('is_default', true)
        ?? $addresses->first();

    $selectedShipping = old(
        'shipping_method',
        $selectedShipping ?? 'regular'
    );

    if (!array_key_exists($selectedShipping, $shippingOptions)) {
        $selectedShipping = 'regular';
    }

    $selectedProvince = old(
        'shipping_province',
        $selectedProvince ?? $defaultAddress?->province ?? 'Aceh'
    );

    if (!in_array($selectedProvince, $provinceOptions, true)) {
        $selectedProvince = 'Aceh';
    }

    $selectedVoucherCode = old('voucher_code', '');
@endphp

<style>
    .available-vouchers {
        display: grid;
        gap: 14px;
        margin-top: 22px;
    }

    .available-vouchers > h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
    }

    .available-voucher-card {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        padding: 18px;
        border: 1px solid #e3e5e8;
        border-left: 4px solid #111;
        background: #fff;
        border-radius: 4px;
    }

    .available-voucher-info {
        flex: 1;
        min-width: 0;
    }

    .available-voucher-info > strong {
        display: block;
        margin-bottom: 8px;
        font-size: 15px;
        line-height: 1.5;
    }

    .available-voucher-code {
        display: inline-block;
        margin-bottom: 8px;
        padding: 5px 9px;
        background: #f2f3f5;
        border: 1px dashed #b8bdc5;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.7px;
    }

    .available-voucher-info p {
        margin: 0 0 7px;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.6;
    }

    .available-voucher-info small {
        display: block;
        margin-top: 5px;
        color: #68717d;
        font-size: 12px;
        line-height: 1.6;
    }

    .voucher-apply-button {
        flex-shrink: 0;
        padding: 10px 13px;
        border: 1px solid #111;
        background: #111;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
    }

    .voucher-apply-button:hover {
        background: #fff;
        color: #111;
    }

    .voucher-apply-button.is-applied {
        background: #e8f5eb;
        color: #176b39;
        border-color: #b9dec3;
    }

    .voucher-note {
        color: #68717d;
        font-size: 12px;
        line-height: 1.7;
    }

    .voucher-empty {
        padding: 18px;
        background: #f7f8fa;
        border: 1px solid #e5e7eb;
        color: #68717d;
        font-size: 13px;
        line-height: 1.7;
    }

    @media (max-width: 600px) {
        .available-voucher-card {
            flex-direction: column;
        }

        .voucher-apply-button {
            width: 100%;
        }
    }
</style>

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

            <h1>Checkout</h1>

            <p>
                Complete your shipping information
                and review your order.
            </p>

        </div>

        {{-- FLASH MESSAGE --}}
        @if(session('error'))
            <div class="checkout-alert checkout-alert-error">
                <i class="bi bi-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="checkout-alert">
                <i class="bi bi-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="checkout-alert checkout-alert-error">
                <i class="bi bi-exclamation-circle"></i>

                <div>
                    <strong>
                        Please check your checkout information.
                    </strong>

                    <p>{{ $errors->first() }}</p>
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

            {{-- ================================================= --}}
            {{-- LEFT CONTENT --}}
            {{-- ================================================= --}}

            <div class="checkout-main">

                {{-- SHIPPING ADDRESS --}}
                <section class="checkout-card">

                    <div class="checkout-card-header">

                        <div class="checkout-step">1</div>

                        <div>
                            <h2>Shipping Address</h2>

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
                                            <small>DEFAULT</small>
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

                            <select
                                id="shipping_province"
                                name="shipping_province"
                                required
                            >
                                @foreach($provinceOptions as $province)
                                    <option
                                        value="{{ $province }}"
                                        @selected($selectedProvince === $province)
                                    >
                                        {{ $province }}
                                    </option>
                                @endforeach
                            </select>
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

                        <div class="checkout-step">2</div>

                        <div>
                            <h2>Shipping Method</h2>

                            <p>
                                Select your preferred delivery option.
                            </p>
                        </div>

                    </div>

                    <div class="shipping-options">

                        @foreach($shippingOptions as $key => $option)
                            <label
                                class="shipping-option"
                                data-shipping-cost="{{ $option['cost'] }}"
                            >
                                <input
                                    type="radio"
                                    name="shipping_method"
                                    value="{{ $key }}"
                                    @checked($selectedShipping === $key)
                                    required
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

                    <div class="shipping-estimate">

                        <strong>
                            <i class="bi bi-calendar-check"></i>
                            Estimated Delivery
                        </strong>

                        <p id="checkout-delivery-date">
                            {{ date(
                                'd M Y',
                                strtotime(
                                    $shippingOptions[$selectedShipping]['estimated_delivery_start']
                                )
                            ) }}

                            –

                            {{ date(
                                'd M Y',
                                strtotime(
                                    $shippingOptions[$selectedShipping]['estimated_delivery_end']
                                )
                            ) }}
                        </p>

                        <small>
                            Estimasi simulasi berdasarkan provinsi
                            dan layanan pengiriman.
                        </small>

                    </div>

                </section>

                {{-- VOUCHER & PROMO: HANYA SATU KALI --}}
                <section class="checkout-card">

                    <div class="checkout-card-header">

                        <div class="checkout-step">3</div>

                        <div>
                            <h2>Voucher & Promo</h2>

                            <p>
                                Pilih voucher yang tersedia untuk
                                mendapatkan diskon.
                            </p>
                        </div>

                    </div>

                    <div class="voucher-input">

                        <input
                            type="text"
                            id="voucher_code"
                            name="voucher_code"
                            value="{{ $selectedVoucherCode }}"
                            placeholder="Masukkan kode voucher"
                            autocomplete="off"
                        >

                        <span>OPSIONAL</span>

                    </div>

                    <p class="voucher-note">
                        Diskon akan divalidasi saat kamu membuat pesanan.
                        Pastikan subtotal memenuhi minimum belanja voucher.
                    </p>

                    <div class="available-vouchers">

                        <h3>Voucher Tersedia</h3>

                        @forelse($availableVouchers as $voucher)

                            <div class="available-voucher-card">

                                <div class="available-voucher-info">

                                    <strong>
                                        {{ $voucher->name ?: $voucher->code }}
                                    </strong>

                                    <div class="available-voucher-code">
                                        {{ $voucher->code }}
                                    </div>

                                    <p>
                                        @if($voucher->discount_type === 'percentage')

                                            Diskon
                                            {{ number_format(
                                                (float) $voucher->discount_value,
                                                0,
                                                ',',
                                                '.'
                                            ) }}%

                                            @if($voucher->max_discount_amount !== null)
                                                — maksimal Rp
                                                {{ number_format(
                                                    (float) $voucher->max_discount_amount,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }}
                                            @endif

                                        @else

                                            Diskon Rp
                                            {{ number_format(
                                                (float) $voucher->discount_value,
                                                0,
                                                ',',
                                                '.'
                                            ) }}

                                        @endif
                                    </p>

                                    @if((float) $voucher->min_order_amount > 0)
                                        <small>
                                            Minimum belanja Rp
                                            {{ number_format(
                                                (float) $voucher->min_order_amount,
                                                0,
                                                ',',
                                                '.'
                                            ) }}
                                        </small>
                                    @else
                                        <small>
                                            Tidak ada minimum belanja.
                                        </small>
                                    @endif

                                    <small>
                                        Berlaku sampai:
                                        {{ $voucher->expires_at
                                            ? $voucher->expires_at->format('d M Y')
                                            : 'Tidak dibatasi tanggal' }}
                                    </small>

                                </div>

                                <button
                                    type="button"
                                    class="voucher-apply-button"
                                    data-voucher-code="{{ $voucher->code }}"
                                >
                                    Gunakan Voucher
                                </button>

                            </div>

                        @empty

                            <div class="voucher-empty">
                                <i class="bi bi-ticket-perforated"></i>

                                Belum ada voucher aktif yang tersedia
                                saat ini.
                            </div>

                        @endforelse

                    </div>

                </section>

            </div>

            {{-- ================================================= --}}
            {{-- RIGHT CONTENT: ORDER SUMMARY --}}
            {{-- ================================================= --}}

            <aside class="checkout-summary">

                <div class="checkout-summary-card">

                    <h2>Order Summary</h2>

                    {{-- PRODUCT LIST --}}
                    <div class="checkout-products">

                        @foreach($cart->items as $item)

                            @php
                                $variant = $item->variant;
                                $product = $variant?->product;

                                $image = $variant?->images?->first()
                                    ?? $product?->images?->first();

                                $itemSubtotal = $variant->price
                                    * $item->quantity;
                            @endphp

                            <div class="checkout-product">

                                <div class="checkout-product-image">

                                    @if(
                                        $image
                                        && file_exists(public_path($image->image_path))
                                    )
                                        <img
                                            src="{{ asset($image->image_path) }}"
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

                                        @if($variant?->color && $variant?->size)
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

                        <span>Subtotal</span>

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

                        <span>Shipping</span>

                        <strong id="checkout-shipping-price">
                            Rp {{ number_format(
                                $shippingOptions[$selectedShipping]['cost'],
                                0,
                                ',',
                                '.'
                            ) }}
                        </strong>

                    </div>

                    <div class="checkout-summary-row">
                        <span>Voucher</span>

                        <span id="checkout-voucher-summary">
                            Divalidasi saat checkout
                        </span>
                    </div>

                    <div class="detail-summary-divider"></div>

                    {{-- ESTIMATED TOTAL --}}
                    <div class="checkout-total">

                        <span>ESTIMATED TOTAL</span>

                        <strong
                            id="checkout-total-price"
                            data-subtotal="{{ $subtotal }}"
                        >
                            Rp {{ number_format(
                                $subtotal
                                + $shippingOptions[$selectedShipping]['cost'],
                                0,
                                ',',
                                '.'
                            ) }}
                        </strong>

                    </div>

                    <p class="checkout-summary-note">
                        Total di atas adalah estimasi sebelum diskon voucher.
                        Diskon akan dihitung dan divalidasi saat pesanan dibuat.
                    </p>

                    {{-- PLACE ORDER --}}
                    <button
                        type="submit"
                        class="place-order-button"
                    >
                        PLACE ORDER
                        <i class="bi bi-arrow-right"></i>
                    </button>

                    <p class="checkout-summary-note">
                        By placing your order, you confirm that the
                        shipping information above is correct.
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
    | SHIPPING PRICE AND DELIVERY ESTIMATE
    |--------------------------------------------------------------------------
    */

    const shippingInputs = document.querySelectorAll(
        'input[name="shipping_method"]'
    );

    const totalElement = document.getElementById(
        'checkout-total-price'
    );

    const shippingPriceElement = document.getElementById(
        'checkout-shipping-price'
    );

    const provinceInput = document.getElementById(
        'shipping_province'
    );

    const deliveryDateElement = document.getElementById(
        'checkout-delivery-date'
    );

    const shippingQuotes = @json($shippingQuotes);

    const subtotal = Number(
        totalElement.dataset.subtotal
    );

    function formatRupiah(value) {
        return 'Rp ' + new Intl.NumberFormat('id-ID', {
            maximumFractionDigits: 0
        }).format(value);
    }

    function formatDeliveryDate(value) {
        const datePart = String(value).slice(0, 10);
        const parts = datePart.split('-').map(Number);

        const date = new Date(
            parts[0],
            parts[1] - 1,
            parts[2]
        );

        return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        });
    }

    function updateShipping() {
        const province = provinceInput.value;
        const provinceQuotes = shippingQuotes[province];

        if (!provinceQuotes) {
            return;
        }

        shippingInputs.forEach(function (input) {
            const option = input.closest('.shipping-option');
            const quote = provinceQuotes[input.value];

            if (!option || !quote) {
                return;
            }

            option.dataset.shippingCost = quote.cost;

            const priceElement = option.querySelector(
                '.shipping-price'
            );

            if (priceElement) {
                priceElement.textContent = formatRupiah(
                    quote.cost
                );
            }

            const description = option.querySelector(
                '.shipping-info small'
            );

            if (description) {
                description.textContent =
                    'Estimasi ' +
                    quote.min_days +
                    '–' +
                    quote.max_days +
                    ' hari';
            }
        });

        const selectedInput = document.querySelector(
            'input[name="shipping_method"]:checked'
        );

        if (!selectedInput) {
            return;
        }

        const selectedQuote = provinceQuotes[selectedInput.value];

        if (!selectedQuote) {
            return;
        }

        shippingPriceElement.textContent = formatRupiah(
            selectedQuote.cost
        );

        totalElement.textContent = formatRupiah(
            subtotal + selectedQuote.cost
        );

        deliveryDateElement.textContent =
            formatDeliveryDate(
                selectedQuote.estimated_delivery_start
            ) +
            ' – ' +
            formatDeliveryDate(
                selectedQuote.estimated_delivery_end
            );
    }

    shippingInputs.forEach(function (input) {
        input.addEventListener('change', updateShipping);
    });

    provinceInput.addEventListener('change', updateShipping);

    updateShipping();


    /*
    |--------------------------------------------------------------------------
    | SAVED ADDRESS
    |--------------------------------------------------------------------------
    */

    const savedAddresses = document.querySelectorAll(
        '.saved-address-button'
    );

    savedAddresses.forEach(function (button) {

        button.addEventListener('click', function () {

            savedAddresses.forEach(function (item) {
                item.classList.remove('active');
            });

            button.classList.add('active');

            document.getElementById('recipient_name').value =
                button.dataset.recipientName || '';

            document.getElementById('recipient_phone').value =
                button.dataset.phone || '';

            document.getElementById('shipping_address').value =
                button.dataset.address || '';

            document.getElementById('shipping_city').value =
                button.dataset.city || '';

            document.getElementById('shipping_province').value =
                button.dataset.province || 'Aceh';

            document.getElementById('shipping_province')
                .dispatchEvent(new Event('change'));

            document.getElementById('shipping_postal_code').value =
                button.dataset.postalCode || '';

        });

    });


    /*
    |--------------------------------------------------------------------------
    | APPLY VOUCHER CODE TO INPUT
    |--------------------------------------------------------------------------
    */

    const voucherInput = document.getElementById('voucher_code');

    const voucherSummary = document.getElementById(
        'checkout-voucher-summary'
    );

    const voucherButtons = document.querySelectorAll(
        '.voucher-apply-button'
    );

    voucherButtons.forEach(function (button) {

        button.addEventListener('click', function () {

            const code = button.dataset.voucherCode;

            voucherInput.value = code;
            voucherInput.focus();

            voucherButtons.forEach(function (otherButton) {
                otherButton.classList.remove('is-applied');
                otherButton.textContent = 'Gunakan Voucher';
            });

            button.classList.add('is-applied');
            button.textContent = 'Kode Dipilih';

            if (voucherSummary) {
                voucherSummary.textContent = code;
            }

        });

    });

    voucherInput.addEventListener('input', function () {

        const currentCode = voucherInput.value.trim().toUpperCase();

        let matchedButton = null;

        voucherButtons.forEach(function (button) {

            const buttonCode = button.dataset.voucherCode.toUpperCase();

            button.classList.remove('is-applied');
            button.textContent = 'Gunakan Voucher';

            if (buttonCode === currentCode && currentCode !== '') {
                matchedButton = button;
            }

        });

        if (matchedButton) {
            matchedButton.classList.add('is-applied');
            matchedButton.textContent = 'Kode Dipilih';
        }

        if (voucherSummary) {
            voucherSummary.textContent = currentCode !== ''
                ? currentCode
                : 'Divalidasi saat checkout';
        }

    });

});
</script>

@endpush