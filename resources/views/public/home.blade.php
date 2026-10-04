@extends('layouts.store')

@section('title', 'Home')

@section('content')

<section class="hero">
    <div class="hero-content">
        <p class="hero-label">NEW COLLECTION</p>

        <h1>
            STYLE FOR
            <br>
            EVERYDAY LIFE
        </h1>

        <p>
            Temukan koleksi lifestyle untuk aktivitas harian
            dengan desain modern dan nyaman.
        </p>

        <div class="hero-actions">
            <a href="#" class="btn-dark">SHOP MEN</a>
            <a href="#" class="btn-light">SHOP WOMEN</a>
        </div>
    </div>
</section>


<section class="section">

    <div class="section-header">
        <h2>NEW ARRIVALS</h2>
        <a href="#">View All →</a>
    </div>

    <div class="product-grid">

        <div class="product-card">
            <div class="product-image">
                <span>Product Image</span>
            </div>

            <div class="product-info">
                <p class="product-badge">NEW</p>
                <h3>Oversized T-Shirt</h3>
                <p>Unisex Lifestyle</p>
                <strong>Rp 199.000</strong>
            </div>
        </div>

        <div class="product-card">
            <div class="product-image">
                <span>Product Image</span>
            </div>

            <div class="product-info">
                <p class="product-badge">NEW</p>
                <h3>Casual Oxford Shirt</h3>
                <p>Men Lifestyle</p>
                <strong>Rp 299.000</strong>
            </div>
        </div>

        <div class="product-card">
            <div class="product-image">
                <span>Product Image</span>
            </div>

            <div class="product-info">
                <p class="product-badge">BEST SELLER</p>
                <h3>Relaxed Pants</h3>
                <p>Unisex Lifestyle</p>
                <strong>Rp 349.000</strong>
            </div>
        </div>

        <div class="product-card">
            <div class="product-image">
                <span>Product Image</span>
            </div>

            <div class="product-info">
                <p class="product-badge">NEW</p>
                <h3>Everyday Sneakers</h3>
                <p>Unisex Footwear</p>
                <strong>Rp 599.000</strong>
            </div>
        </div>

    </div>

</section>


<section class="category-section">

    <div class="category-card">
        <h2>MEN</h2>
        <a href="#">SHOP NOW →</a>
    </div>

    <div class="category-card">
        <h2>WOMEN</h2>
        <a href="#">SHOP NOW →</a>
    </div>

    <div class="category-card">
        <h2>UNISEX</h2>
        <a href="#">SHOP NOW →</a>
    </div>

</section>


<section class="campaign-section">

    <div class="campaign-content">
        <p>DAILY ESSENTIALS</p>

        <h2>
            MADE FOR
            <br>
            EVERYDAY MOVEMENT
        </h2>

        <a href="#" class="btn-dark">
            EXPLORE COLLECTION
        </a>
    </div>

</section>

@endsection