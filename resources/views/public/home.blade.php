@extends('layouts.store')

@section('title', 'Home')

@section('content')


{{-- =========================
    HERO
========================= --}}

<section class="hero">

    <div class="hero-overlay"></div>

    <div class="hero-content">

        <span class="hero-label">
            NEW COLLECTION
        </span>

        <h1>
            STYLE FOR
            <br>
            EVERYDAY LIFE
        </h1>

        <p>
            Koleksi lifestyle modern untuk aktivitas sehari-hari.
            Nyaman, sederhana, dan mudah dipadukan.
        </p>

        <div class="hero-actions">

            <a
                href="{{ route('catalog.preview', ['segment' => 'men']) }}"
                class="btn-primary-store">
                SHOP MEN
                <i class="bi bi-arrow-right"></i>
            </a>

            <a
                href="{{ route('catalog.preview', ['segment' => 'women']) }}"
                class="btn-secondary-store">
                SHOP WOMEN
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

        <div>
            <span class="section-small-title">
                DISCOVER
            </span>

            <h2>
                NEW ARRIVALS
            </h2>
        </div>

        <a href="{{ route('catalog.preview') }}" class="section-link">
            VIEW ALL
            <i class="bi bi-arrow-right"></i>
        </a>
    </div>


    <div class="product-grid">

        @forelse($products as $product)

        @include('components.product-card', [
        'product' => $product
        ])

        @empty

        <div class="empty-product">
            Belum ada produk tersedia.
        </div>

        @endforelse

    </div>

</section>



{{-- =========================
    SHOP BY CATEGORY
========================= --}}

<section class="category-wrapper">

    <div class="category-heading">

        <span class="section-small-title">
            EXPLORE
        </span>

        <h2>
            SHOP BY CATEGORY
        </h2>

    </div>


    <div class="category-grid">


        {{-- MEN --}}

        <article
            class="category-card category-men">

            <div class="category-overlay"></div>

            <div class="category-content">

                <h3>
                    MEN
                </h3>

                <a href="{{ route('catalog.preview', ['segment' => 'men']) }}">
                    SHOP NOW
                    <i class="bi bi-arrow-right"></i>
                </a>

            </div>

        </article>


        {{-- WOMEN --}}

        <article
            class="category-card category-women">

            <div class="category-overlay"></div>

            <div class="category-content">

                <h3>
                    WOMEN
                </h3>

                <a href="{{ route('catalog.preview', ['segment' => 'women']) }}">
                    SHOP NOW
                    <i class="bi bi-arrow-right"></i>
                </a>

            </div>

        </article>


        {{-- UNISEX --}}

        <article
            class="category-card category-unisex">

            <div class="category-overlay"></div>

            <div class="category-content">

                <h3>
                    UNISEX
                </h3>

                <a href="{{ route('catalog.preview', ['segment' => 'unisex']) }}">
                    SHOP NOW
                    <i class="bi bi-arrow-right"></i>
                </a>

            </div>

        </article>

    </div>

</section>



{{-- =========================
    COLLECTION
========================= --}}

<section class="store-section collection-section">

    <div class="section-header">

        <div>

            <span class="section-small-title">
                CURATED FOR YOU
            </span>

            <h2>
                SHOP COLLECTIONS
            </h2>

        </div>

    </div>


    <div class="collection-grid">

        <a href="#" class="collection-item">

            <span>
                01
            </span>

            <div>
                <h3>
                    NEW ARRIVALS
                </h3>

                <p>
                    Discover our latest products.
                </p>
            </div>

            <i class="bi bi-arrow-up-right"></i>

        </a>


        <a href="#" class="collection-item">

            <span>
                02
            </span>

            <div>
                <h3>
                    ESSENTIALS
                </h3>

                <p>
                    Everyday wardrobe essentials.
                </p>
            </div>

            <i class="bi bi-arrow-up-right"></i>

        </a>


        <a href="#" class="collection-item">

            <span>
                03
            </span>

            <div>
                <h3>
                    BEST SELLER
                </h3>

                <p>
                    Customer favourite products.
                </p>
            </div>

            <i class="bi bi-arrow-up-right"></i>

        </a>


        <a href="#" class="collection-item">

            <span>
                04
            </span>

            <div>
                <h3>
                    DAILY WEAR
                </h3>

                <p>
                    Designed for everyday movement.
                </p>
            </div>

            <i class="bi bi-arrow-up-right"></i>

        </a>

    </div>

</section>



{{-- =========================
    CAMPAIGN
========================= --}}

<section class="campaign-section">

    <div class="campaign-overlay"></div>

    <div class="campaign-content">

        <span>
            DAILY ESSENTIALS
        </span>

        <h2>
            MADE FOR
            <br>
            EVERYDAY MOVEMENT
        </h2>

        <p>
            Simple pieces designed to keep you comfortable
            throughout your everyday activities.
        </p>

        <a href="#" class="btn-primary-store">

            EXPLORE COLLECTION

            <i class="bi bi-arrow-right"></i>

        </a>

    </div>

</section>


@endsection