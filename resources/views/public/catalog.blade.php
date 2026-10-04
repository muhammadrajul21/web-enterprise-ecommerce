@extends('layouts.store')

@section('title', 'Shop All Products')

@section('content')

{{-- =========================
    PAGE HEADER
========================= --}}

<section class="catalog-header">

    <div>

        <span class="section-small-title">
            SHOP
        </span>

        <h1>
            ALL PRODUCTS
        </h1>

        <p>
            Discover everyday lifestyle pieces designed
            for comfort, simplicity, and modern living.
        </p>

    </div>

</section>


{{-- =========================
    CATALOG TOOLBAR
========================= --}}

<section class="catalog-container">

    <form
        action="{{ route('catalog.preview') }}"
        method="GET"
        class="catalog-toolbar"
    >

        <div class="catalog-filters">


            {{-- SEGMENT --}}

            <div class="filter-group">

                <label for="segment">
                    SEGMENT
                </label>

                <select
                    name="segment"
                    id="segment"
                >

                    <option value="">
                        All
                    </option>

                    @foreach($segments as $segment)

                        <option
                            value="{{ $segment->slug }}"
                            {{ request('segment') === $segment->slug ? 'selected' : '' }}
                        >
                            {{ $segment->name }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- CATEGORY --}}

            <div class="filter-group">

                <label for="category">
                    CATEGORY
                </label>

                <select
                    name="category"
                    id="category"
                >

                    <option value="">
                        All
                    </option>

                    @foreach($categories as $category)

                        <option
                            value="{{ $category->slug }}"
                            {{ request('category') === $category->slug ? 'selected' : '' }}
                        >
                            {{ $category->name }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- SORT --}}

            <div class="filter-group">

                <label for="sort">
                    SORT BY
                </label>

                <select
                    name="sort"
                    id="sort"
                >

                    <option
                        value="latest"
                        {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}
                    >
                        Newest
                    </option>

                    <option
                        value="price_low"
                        {{ request('sort') === 'price_low' ? 'selected' : '' }}
                    >
                        Price: Low to High
                    </option>

                    <option
                        value="price_high"
                        {{ request('sort') === 'price_high' ? 'selected' : '' }}
                    >
                        Price: High to Low
                    </option>

                    <option
                        value="name"
                        {{ request('sort') === 'name' ? 'selected' : '' }}
                    >
                        Name A-Z
                    </option>

                </select>

            </div>


            <button
                type="submit"
                class="catalog-filter-button"
            >
                APPLY
            </button>


            @if(
                request('segment') ||
                request('category') ||
                request('sort')
            )

                <a
                    href="{{ route('catalog.preview') }}"
                    class="catalog-reset"
                >
                    RESET
                </a>

            @endif

        </div>

    </form>


    {{-- =========================
        PRODUCT RESULT
    ========================= --}}

    <div class="catalog-result-header">

        <p>

            <strong>
                {{ $products->total() }}
            </strong>

            products

        </p>

    </div>


    <div class="product-grid catalog-product-grid">

        @forelse($products as $product)

            @include('components.product-card', [
                'product' => $product
            ])

        @empty

            <div class="catalog-empty">

                <i class="bi bi-search"></i>

                <h2>
                    No products found
                </h2>

                <p>
                    Try changing your filters.
                </p>

                <a
                    href="{{ route('catalog.preview') }}"
                    class="btn-primary-store"
                >
                    VIEW ALL PRODUCTS
                </a>

            </div>

        @endforelse

    </div>


    {{-- =========================
        PAGINATION
    ========================= --}}

    @if($products->hasPages())

        <div class="catalog-pagination">

            @if($products->onFirstPage())

                <span class="pagination-disabled">
                    <i class="bi bi-arrow-left"></i>
                    PREVIOUS
                </span>

            @else

                <a href="{{ $products->previousPageUrl() }}">
                    <i class="bi bi-arrow-left"></i>
                    PREVIOUS
                </a>

            @endif


            <span class="pagination-page">

                PAGE
                {{ $products->currentPage() }}
                OF
                {{ $products->lastPage() }}

            </span>


            @if($products->hasMorePages())

                <a href="{{ $products->nextPageUrl() }}">
                    NEXT
                    <i class="bi bi-arrow-right"></i>
                </a>

            @else

                <span class="pagination-disabled">
                    NEXT
                    <i class="bi bi-arrow-right"></i>
                </span>

            @endif

        </div>

    @endif

</section>

@endsection