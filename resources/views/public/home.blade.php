@extends('layouts.store')

@section('title', 'Lifestyle Store')

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | HOME IMAGE PATHS
    |--------------------------------------------------------------------------
    */

    $heroPhoto = file_exists(
        public_path('images/home/hero.jpg')
    )
        ? asset('images/home/hero.jpg')
        : null;


    $campaignPhoto = file_exists(
        public_path('images/home/campaign.jpg')
    )
        ? asset('images/home/campaign.jpg')
        : null;
@endphp


{{-- =========================
    HERO
========================= --}}

<section
    class="hero"
    @if ($heroPhoto)
        style="--photo: url('{{ $heroPhoto }}');"
    @endif
>

    <div class="hero-content">

        <h1>
            Built for
            <br>
            everyday life
        </h1>

        <p>
            Clothing, footwear, and accessories
            that are easy to wear and easy to mix.
        </p>

        <div class="hero-actions">

            <a
                href="{{ route('catalog.preview', [
                    'segment' => 'men'
                ]) }}"
                class="btn-secondary-store"
            >
                Shop men

                <i class="bi bi-arrow-right"></i>
            </a>


            <a
                href="{{ route('catalog.preview', [
                    'segment' => 'women'
                ]) }}"
                class="btn-ghost-store"
            >
                Shop women

                <i class="bi bi-arrow-right"></i>
            </a>

        </div>

    </div>

</section>



{{-- =========================
    NEW ARRIVALS
========================= --}}

<section class="store-section">

    <div class="section-header">

        <h2>
            New arrivals
        </h2>

        <a
            href="{{ route('catalog.preview', [
                'sort' => 'latest'
            ]) }}"
            class="section-link"
        >
            View all

            <i class="bi bi-arrow-right"></i>
        </a>

    </div>


    <div class="product-grid">

        @forelse ($products as $product)

            @include(
                'components.product-card',
                [
                    'product' => $product
                ]
            )

        @empty

            <div class="empty-product">

                No products yet.
                Check back soon.

            </div>

        @endforelse

    </div>

</section>



{{-- =========================
    SHOP BY SEGMENT
========================= --}}

@if ($segments->isNotEmpty())

    <section class="segment-section">

        <div class="section-header segment-header">

            <h2>
                Shop by style
            </h2>

        </div>


        <div class="segment-grid">

            @foreach ($segments as $segment)

                @php

                    $segmentPath =
                        'images/home/' .
                        $segment->slug .
                        '.jpg';


                    $segmentPhoto =
                        file_exists(
                            public_path($segmentPath)
                        )
                            ? asset($segmentPath)
                            : null;

                @endphp


                <a
                    href="{{ route('catalog.preview', [
                        'segment' => $segment->slug
                    ]) }}"
                    class="segment-card"
                    @if ($segmentPhoto)
                        style="--photo: url('{{ $segmentPhoto }}');"
                    @endif
                >

                    <span class="segment-name">

                        {{ $segment->name }}

                    </span>


                    <span class="segment-cta">

                        Shop {{ strtolower($segment->name) }}

                        <i class="bi bi-arrow-right"></i>

                    </span>

                </a>

            @endforeach

        </div>

    </section>

@endif



{{-- =========================
    COLLECTIONS
========================= --}}

@if ($collections->isNotEmpty())

    <section class="store-section collection-section">

        <div class="section-header">

            <h2>
                Collections
            </h2>

        </div>


        <div class="collection-list">

            @foreach ($collections as $collection)

                <a
                    href="{{ route('catalog.preview', [
                        'collection' => $collection->slug
                    ]) }}"
                    class="collection-item"
                >

                    <div>

                        <h3>
                            {{ $collection->name }}
                        </h3>


                        @if ($collection->description)

                            <p>
                                {{ $collection->description }}
                            </p>

                        @endif

                    </div>


                    <span class="collection-count">

                        {{ $collection->products_count }}

                        {{ $collection->products_count === 1
                            ? 'product'
                            : 'products'
                        }}

                    </span>


                    <i class="bi bi-arrow-right"></i>

                </a>

            @endforeach

        </div>

    </section>

@endif



{{-- =========================
    CLOSING BANNER
========================= --}}

<section
    class="campaign-section"
    @if ($campaignPhoto)
        style="--photo: url('{{ $campaignPhoto }}');"
    @endif
>

    <div class="campaign-content">

        <h2>
            Wear it on repeat
        </h2>

        <p>
            Simple pieces that work with
            what you already own.
        </p>

        <a
            href="{{ route('catalog.preview') }}"
            class="btn-secondary-store"
        >
            Browse all products

            <i class="bi bi-arrow-right"></i>
        </a>

    </div>

</section>

@endsection