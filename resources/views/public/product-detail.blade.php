@extends('layouts.store')

@section('title', $product->name)

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | PRODUCT DATA
    |--------------------------------------------------------------------------
    */

    $primaryImage = $product->images->first();

    /*
    |--------------------------------------------------------------------------
    | ACTIVE VARIANTS
    |--------------------------------------------------------------------------
    |
    | Hanya variant active yang boleh dipilih customer.
    |
    */

    $activeVariants = $product->variants
        ->where('status', 'active')
        ->values();

    $minPrice = $activeVariants->min('price');

    $colors = $activeVariants
        ->pluck('color')
        ->filter()
        ->unique()
        ->values();

    $sizes = $activeVariants
        ->pluck('size')
        ->filter()
        ->unique()
        ->values();

    $totalStock = $activeVariants->sum('stock');

    /*
    |--------------------------------------------------------------------------
    | VARIANT DATA FOR JAVASCRIPT
    |--------------------------------------------------------------------------
    */

    $variantData = $activeVariants
        ->map(function ($variant) {
            return [
                'id' => $variant->id,
                'color' => $variant->color,
                'size' => $variant->size,
                'price' => (float) $variant->price,
                'stock' => (int) $variant->stock,
                'status' => $variant->status,
            ];
        })
        ->values();
@endphp


{{-- =========================
    BREADCRUMB
========================= --}}

<div class="product-breadcrumb">

    <a href="{{ route('home.preview') }}">
        HOME
    </a>

    <span>/</span>

    <a href="{{ route('catalog.preview') }}">
        PRODUCTS
    </a>

    @if($product->category)

        <span>/</span>

        <a
            href="{{ route('catalog.preview', [
                'category' => $product->category->slug
            ]) }}"
        >
            {{ strtoupper($product->category->name) }}
        </a>

    @endif

</div>


{{-- =========================
    PRODUCT MAIN
========================= --}}

<section class="product-detail">


    {{-- =========================
        PRODUCT GALLERY
    ========================= --}}

    <div class="product-gallery">

        @forelse($product->images as $image)

            <div class="product-detail-image">

                @if(file_exists(public_path($image->image_path)))

                    <img
                        src="{{ asset($image->image_path) }}"
                        alt="{{ $product->name }}"
                    >

                @else

                    <div class="detail-image-placeholder">

                        <i class="bi bi-image"></i>

                        <span>
                            {{ $product->name }}
                        </span>

                    </div>

                @endif

            </div>

        @empty

            <div class="product-detail-image">

                <div class="detail-image-placeholder">

                    <i class="bi bi-image"></i>

                    <span>
                        {{ $product->name }}
                    </span>

                </div>

            </div>

        @endforelse

    </div>


    {{-- =========================
        PRODUCT INFORMATION
    ========================= --}}

    <aside class="product-detail-info">

        <div class="product-detail-sticky">


            {{-- CATEGORY --}}

            <p class="product-detail-category">

                {{ strtoupper($product->segment?->name ?? '') }}

                @if($product->category)

                    · {{ strtoupper($product->category->name) }}

                @endif

            </p>


            {{-- PRODUCT NAME --}}

            <h1>
                {{ $product->name }}
            </h1>


            {{-- PRICE --}}

            @if($minPrice !== null)

                <p
                    class="product-detail-price"
                    id="product-current-price"
                >

                    Rp {{ number_format(
                        $minPrice,
                        0,
                        ',',
                        '.'
                    ) }}

                </p>

            @endif


            {{-- COLLECTION --}}

            @if($product->collections->count())

                <div class="product-collections">

                    @foreach($product->collections as $collection)

                        <span>
                            {{ $collection->name }}
                        </span>

                    @endforeach

                </div>

            @endif


            {{-- =========================
                FLASH MESSAGE
            ========================= --}}

            @if(session('error'))

                <div class="product-cart-message error">

                    <i class="bi bi-exclamation-circle"></i>

                    <span>
                        {{ session('error') }}
                    </span>

                </div>

            @endif


            @if(session('success'))

                <div class="product-cart-message success">

                    <i class="bi bi-check-circle"></i>

                    <span>
                        {{ session('success') }}
                    </span>

                </div>

            @endif


            @if($errors->any())

                <div class="product-cart-message error">

                    <i class="bi bi-exclamation-circle"></i>

                    <span>
                        {{ $errors->first() }}
                    </span>

                </div>

            @endif


            {{-- =========================
                COLOR
            ========================= --}}

            @if($colors->count())

                <div class="product-option">

                    <div class="product-option-title">

                        <span>
                            COLOR
                        </span>

                        <span>
                            {{ $colors->count() }} options
                        </span>

                    </div>


                    <div class="option-buttons">

                        @foreach($colors as $color)

                            <button
                                type="button"
                                class="option-button color-option"
                                data-color="{{ $color }}"
                            >

                                {{ $color }}

                            </button>

                        @endforeach

                    </div>

                </div>

            @endif


            {{-- =========================
                SIZE
            ========================= --}}

            @if($sizes->count())

                <div class="product-option">

                    <div class="product-option-title">

                        <span>
                            SELECT SIZE
                        </span>

                        <span>
                            Size Guide
                        </span>

                    </div>


                    <div class="size-grid">

                        @foreach($sizes as $size)

                            <button
                                type="button"
                                class="size-button size-option"
                                data-size="{{ $size }}"
                            >

                                {{ $size }}

                            </button>

                        @endforeach

                    </div>

                </div>

            @endif


            {{-- =========================
                STOCK
            ========================= --}}

            <div class="product-stock">

                @if($totalStock > 0)

                    <i class="bi bi-check-circle"></i>

                    <span>
                        In stock
                    </span>

                    <small>
                        {{ $totalStock }} items available
                    </small>

                @else

                    <i class="bi bi-x-circle"></i>

                    <span>
                        Out of stock
                    </span>

                    <small>
                        Product currently unavailable
                    </small>

                @endif

            </div>


            {{-- =========================
                ACTION
            ========================= --}}

            <div class="product-actions">


                {{-- ADD TO CART --}}

                <form
                    action="{{ route('cart.store') }}"
                    method="POST"
                    id="add-to-cart-form"
                    class="product-add-cart-form"
                >

                    @csrf


                    {{-- SELECTED VARIANT --}}

                    <input
                        type="hidden"
                        name="variant_id"
                        id="selected-variant-id"
                        value=""
                    >


                    {{-- DEFAULT QUANTITY --}}

                    <input
                        type="hidden"
                        name="quantity"
                        value="1"
                    >


                    <button
                        type="submit"
                        class="add-cart-button"
                        id="add-cart-button"
                        disabled
                    >

                        ADD TO CART

                        <i class="bi bi-bag"></i>

                    </button>

                </form>


                {{-- WISHLIST --}}

                <button
                    type="button"
                    class="detail-wishlist-button"
                    aria-label="Add to wishlist"
                >

                    <i class="bi bi-heart"></i>

                </button>

            </div>


            {{-- VARIANT STATUS MESSAGE --}}

            <p
                id="variant-message"
                class="variant-message"
            >

                @if($totalStock > 0)

                    Select available options

                @else

                    Product is out of stock

                @endif

            </p>


            {{-- =========================
                DESCRIPTION
            ========================= --}}

            <div class="product-description">

                <h2>
                    PRODUCT DETAILS
                </h2>

                <p>

                    {{ $product->description
                        ?: 'Everyday lifestyle product designed for comfort, simplicity, and modern daily use.' }}

                </p>

            </div>


            {{-- =========================
                AVAILABLE VARIANTS
            ========================= --}}

            <div class="variant-section">

                <h2>
                    AVAILABLE VARIANTS
                </h2>


                @forelse($activeVariants as $variant)

                    <div class="variant-row">

                        <div>

                            <strong>
                                {{ $variant->color ?: '-' }}
                            </strong>

                            <span>
                                Size {{ $variant->size ?: '-' }}
                            </span>

                        </div>


                        <div>

                            <strong>

                                Rp {{ number_format(
                                    $variant->price,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </strong>

                            <span>
                                Stock: {{ $variant->stock }}
                            </span>

                        </div>

                    </div>

                @empty

                    <p class="no-variant-message">
                        No available variants.
                    </p>

                @endforelse

            </div>

        </div>

    </aside>

</section>


{{-- =========================
    RELATED PRODUCTS
========================= --}}

@if($relatedProducts->count())

<section class="store-section related-products">

    <div class="section-header">

        <div>

            <span class="section-small-title">
                YOU MAY ALSO LIKE
            </span>

            <h2>
                RELATED PRODUCTS
            </h2>

        </div>


        <a
            href="{{ route('catalog.preview', [
                'category' => $product->category?->slug
            ]) }}"
            class="section-link"
        >

            VIEW ALL

            <i class="bi bi-arrow-right"></i>

        </a>

    </div>


    <div class="product-grid">

        @foreach($relatedProducts as $relatedProduct)

            @include('components.product-card', [
                'product' => $relatedProduct
            ])

        @endforeach

    </div>

</section>

@endif


{{-- =========================
    VARIANT DATA
========================= --}}
{{--
    Data JSON sengaja diletakkan pada HTML,
    bukan langsung di dalam JavaScript.
    Ini membuat Blade dan JavaScript lebih aman
    dan tidak membingungkan syntax checker VS Code.
--}}

<div
    id="product-variant-data"
    data-variants="{{ $variantData->toJson() }}"
    hidden
></div>

@endsection


{{-- =========================
    VARIANT SELECTOR SCRIPT
========================= --}}

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | GET VARIANT DATA FROM HTML
    |--------------------------------------------------------------------------
    */

    const variantDataElement =
        document.getElementById('product-variant-data');


    let variants = [];


    if (variantDataElement) {

        try {

            variants = JSON.parse(
                variantDataElement.dataset.variants || '[]'
            );

        } catch (error) {

            console.error(
                'Failed to read product variant data.',
                error
            );

            variants = [];

        }

    }


    /*
    |--------------------------------------------------------------------------
    | ELEMENTS
    |--------------------------------------------------------------------------
    */

    const colorButtons =
        document.querySelectorAll('.color-option');

    const sizeButtons =
        document.querySelectorAll('.size-option');

    const variantInput =
        document.getElementById('selected-variant-id');

    const addButton =
        document.getElementById('add-cart-button');

    const message =
        document.getElementById('variant-message');

    const priceElement =
        document.getElementById('product-current-price');


    /*
    |--------------------------------------------------------------------------
    | SAFETY CHECK
    |--------------------------------------------------------------------------
    */

    if (
        !variantInput
        || !addButton
        || !message
    ) {

        return;

    }


    /*
    |--------------------------------------------------------------------------
    | AVAILABLE OPTIONS
    |--------------------------------------------------------------------------
    */

    const hasColor =
        colorButtons.length > 0;

    const hasSize =
        sizeButtons.length > 0;


    let selectedColor = null;

    let selectedSize = null;


    /*
    |--------------------------------------------------------------------------
    | FORMAT PRICE
    |--------------------------------------------------------------------------
    */

    function formatRupiah(value) {

        const numberValue =
            Number(value);


        if (
            Number.isNaN(numberValue)
        ) {

            return 'Rp 0';

        }


        return 'Rp ' + new Intl.NumberFormat(
            'id-ID',
            {
                maximumFractionDigits: 0
            }
        ).format(numberValue);

    }


    /*
    |--------------------------------------------------------------------------
    | RESET SELECTED VARIANT
    |--------------------------------------------------------------------------
    */

    function resetVariant(messageText) {

        variantInput.value = '';

        addButton.disabled = true;

        message.textContent =
            messageText;

    }


    /*
    |--------------------------------------------------------------------------
    | RESOLVE VARIANT
    |--------------------------------------------------------------------------
    */

    function resolveVariant() {

        /*
         * Customer belum memilih color.
         */
        if (
            hasColor
            && selectedColor === null
        ) {

            resetVariant(
                'Select a color'
            );

            return;

        }


        /*
         * Customer belum memilih size.
         */
        if (
            hasSize
            && selectedSize === null
        ) {

            resetVariant(
                'Select a size'
            );

            return;

        }


        /*
         * Cari kombinasi variant.
         */
        const variant =
            variants.find(function (item) {

                const colorMatches =
                    !hasColor
                    || item.color === selectedColor;

                const sizeMatches =
                    !hasSize
                    || item.size === selectedSize;


                return (
                    colorMatches
                    && sizeMatches
                    && item.status === 'active'
                );

            });


        /*
         * Kombinasi variant tidak ada.
         */
        if (!variant) {

            resetVariant(
                'Selected combination is not available'
            );

            return;

        }


        /*
         * Variant ada tapi stock kosong.
         */
        if (
            Number(variant.stock) <= 0
        ) {

            resetVariant(
                'Selected variant is out of stock'
            );

            return;

        }


        /*
         * Variant valid.
         */
        variantInput.value =
            variant.id;


        addButton.disabled =
            false;


        message.textContent =
            variant.stock
            + ' items available';


        /*
         * Update harga sesuai variant.
         */
        if (priceElement) {

            priceElement.textContent =
                formatRupiah(
                    variant.price
                );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | COLOR SELECTION
    |--------------------------------------------------------------------------
    */

    colorButtons.forEach(function (button) {

        button.addEventListener(
            'click',
            function () {

                colorButtons.forEach(
                    function (item) {

                        item.classList.remove(
                            'active'
                        );

                    }
                );


                button.classList.add(
                    'active'
                );


                selectedColor =
                    button.dataset.color;


                resolveVariant();

            }
        );

    });


    /*
    |--------------------------------------------------------------------------
    | SIZE SELECTION
    |--------------------------------------------------------------------------
    */

    sizeButtons.forEach(function (button) {

        button.addEventListener(
            'click',
            function () {

                sizeButtons.forEach(
                    function (item) {

                        item.classList.remove(
                            'active'
                        );

                    }
                );


                button.classList.add(
                    'active'
                );


                selectedSize =
                    button.dataset.size;


                resolveVariant();

            }
        );

    });


    /*
    |--------------------------------------------------------------------------
    | AUTOMATIC VARIANT SELECTION
    |--------------------------------------------------------------------------
    |
    | Beberapa product mungkin tidak mempunyai color,
    | size, atau hanya mempunyai satu pilihan.
    |
    */


    /*
     * Tidak mempunyai color dan size.
     */
    if (
        !hasColor
        && !hasSize
    ) {

        resolveVariant();

        return;

    }


    /*
     * Hanya mempunyai satu color,
     * tetapi tidak mempunyai size.
     */
    if (
        hasColor
        && !hasSize
        && colorButtons.length === 1
    ) {

        colorButtons[0].click();

        return;

    }


    /*
     * Hanya mempunyai satu size,
     * tetapi tidak mempunyai color.
     */
    if (
        !hasColor
        && hasSize
        && sizeButtons.length === 1
    ) {

        sizeButtons[0].click();

        return;

    }

});
</script>

@endpush