@extends('layouts.store')

@section('title', 'Search')

@section('content')

<section class="search-header">

    <div class="search-header-content">

        <span class="section-small-title">
            SEARCH
        </span>

        <h1>
            SEARCH PRODUCTS
        </h1>

        <form
            action="{{ route('search.preview') }}"
            method="GET"
            class="search-form"
        >

            <div class="search-input-wrapper">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    name="q"
                    value="{{ $keyword }}"
                    placeholder="Search products..."
                    autofocus
                >

                <button type="submit">
                    SEARCH
                </button>

            </div>

        </form>

    </div>

</section>


<section class="search-result-section">

    @if($keyword)

        <div class="search-result-heading">

            <div>

                <p>
                    SEARCH RESULTS FOR
                </p>

                <h2>
                    "{{ $keyword }}"
                </h2>

            </div>

            <span>
                {{ $products->total() }} products
            </span>

        </div>

    @else

        <div class="search-result-heading">

            <h2>
                START SEARCHING
            </h2>

        </div>

    @endif


    @if($keyword)

        <div class="product-grid">

            @forelse($products as $product)

                @include('components.product-card', [
                    'product' => $product
                ])

            @empty

                <div class="search-empty">

                    <i class="bi bi-search"></i>

                    <h2>
                        NO PRODUCTS FOUND
                    </h2>

                    <p>
                        We couldn't find products matching
                        "{{ $keyword }}".
                    </p>

                    <a
                        href="{{ route('catalog.preview') }}"
                        class="btn-primary-store"
                    >
                        BROWSE ALL PRODUCTS
                    </a>

                </div>

            @endforelse

        </div>


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
                    PAGE {{ $products->currentPage() }}
                    OF {{ $products->lastPage() }}
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

    @endif

</section>

@endsection