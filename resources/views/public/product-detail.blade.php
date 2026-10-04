@extends('layouts.store')

@section('title', $product->name)

@section('content')

@php
    $primaryImage = $product->images->first();

    $minPrice = $product->variants->min('price');

    $colors = $product->variants
        ->pluck('color')
        ->filter()
        ->unique()
        ->values();

    $sizes = $product->variants
        ->pluck('size')
        ->filter()
        ->unique()
        ->values();

    $totalStock = $product->variants->sum('stock');
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

        <a href="{{ route('catalog.preview', [
            'category' => $product->category->slug
        ]) }}">
            {{ strtoupper($product->category->name) }}
        </a>

    @endif

</div>


{{-- =========================
    PRODUCT MAIN
========================= --}}

<section class="product-detail">

    {{-- PRODUCT GALLERY --}}

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


    {{-- PRODUCT INFORMATION --}}

    <aside class="product-detail-info">

        <div class="product-detail-sticky">

            {{-- CATEGORY --}}

            <p class="product-detail-category">

                {{ strtoupper($product->segment?->name ?? '') }}

                @if($product->category)

                    · {{ strtoupper($product->category->name) }}

                @endif

            </p>


            {{-- NAME --}}

            <h1>
                {{ $product->name }}
            </h1>


            {{-- PRICE --}}

            @if($minPrice)

                <p class="product-detail-price">

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


            {{-- COLOR --}}

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
                            >
                                {{ $color }}
                            </button>

                        @endforeach

                    </div>

                </div>

            @endif


            {{-- SIZE --}}

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
                                class="size-button"
                            >
                                {{ $size }}
                            </button>

                        @endforeach

                    </div>

                </div>

            @endif


            {{-- STOCK --}}

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

                @endif

            </div>


            {{-- ACTION --}}

            <div class="product-actions">

                <button
                    type="button"
                    class="add-cart-button"
                    {{ $totalStock <= 0 ? 'disabled' : '' }}
                >

                    ADD TO CART

                    <i class="bi bi-bag"></i>

                </button>


                <button
                    type="button"
                    class="detail-wishlist-button"
                    aria-label="Add to wishlist"
                >

                    <i class="bi bi-heart"></i>

                </button>

            </div>


            {{-- NOTICE --}}

            <p class="product-action-note">
                Cart functionality will be available
                during checkout integration.
            </p>


            {{-- DESCRIPTION --}}

            <div class="product-description">

                <h2>
                    PRODUCT DETAILS
                </h2>

                <p>
                    {{ $product->description
                        ?: 'Everyday lifestyle product designed for comfort, simplicity, and modern daily use.' }}
                </p>

            </div>


            {{-- VARIANT INFORMATION --}}

            <div class="variant-section">

                <h2>
                    AVAILABLE VARIANTS
                </h2>

                @foreach($product->variants as $variant)

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

                @endforeach

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

@endsection