@extends('layouts.store')

@php
    // Judul halaman mengikuti filter yang aktif.
    $activeSegment  = $segments->firstWhere('slug', request('segment'));
    $activeCategory = $categories->firstWhere('slug', request('category'));

    $heading = match (true) {
        (bool) $activeSegment                    => $activeSegment->name,
        (bool) $activeCategory                   => $activeCategory->name,
        request('group') === 'clothing'          => 'Clothing',
        (bool) request('collection')             => \Illuminate\Support\Str::headline(request('collection')),
        request('sort') === 'latest'             => 'New arrivals',
        default                                  => 'All products',
    };

    // Chip filter aktif, masing-masing bisa dilepas satu per satu.
    $chips = [];
    if ($activeSegment)            $chips['segment']    = $activeSegment->name;
    if ($activeCategory)           $chips['category']   = $activeCategory->name;
    if (request('group'))          $chips['group']      = ucfirst(request('group'));
    if (request('collection'))     $chips['collection'] = \Illuminate\Support\Str::headline(request('collection'));
@endphp

@section('title', $heading)

@section('content')

<section class="catalog-header">
    <div>
        <h1>{{ $heading }}</h1>
        <p>Everyday pieces designed for comfort, simplicity, and modern living.</p>
    </div>
</section>

<section class="catalog-container">

    {{-- TOOLBAR: satu form membungkus semua filter --}}
    <form action="{{ route('catalog.preview') }}" method="GET" class="catalog-toolbar">

        @if (request('group'))
            <input type="hidden" name="group" value="{{ request('group') }}">
        @endif

        @if (request('collection'))
            <input type="hidden" name="collection" value="{{ request('collection') }}">
        @endif

        <div class="catalog-filters">

            <div class="filter-group">
                <label for="segment">Segment</label>
                <select name="segment" id="segment" onchange="this.form.submit()">
                    <option value="">All</option>
                    @foreach ($segments as $segment)
                        <option value="{{ $segment->slug }}" @selected(request('segment') === $segment->slug)>
                            {{ $segment->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="category">Category</label>
                <select name="category" id="category" onchange="this.form.submit()">
                    <option value="">All</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="sort">Sort by</label>
                <select name="sort" id="sort" onchange="this.form.submit()">
                    <option value="latest" @selected(request('sort', 'latest') === 'latest')>Newest</option>
                    <option value="price_low" @selected(request('sort') === 'price_low')>Price: low to high</option>
                    <option value="price_high" @selected(request('sort') === 'price_high')>Price: high to low</option>
                    <option value="name" @selected(request('sort') === 'name')>Name A-Z</option>
                </select>
            </div>

            {{-- Filter berjalan otomatis; tombol hanya untuk browser tanpa JS --}}
            <noscript>
                <button type="submit" class="catalog-filter-button">Apply</button>
            </noscript>

        </div>
    </form>

    {{-- HASIL + CHIP FILTER --}}
    <div class="catalog-result-header">

        <p><strong>{{ $products->total() }}</strong> {{ $products->total() === 1 ? 'product' : 'products' }}</p>

        @if ($chips)
            <ul class="filter-chips">
                @foreach ($chips as $key => $label)
                    <li>
                        <a
                            href="{{ route('catalog.preview', request()->except([$key, 'page'])) }}"
                            aria-label="Remove filter {{ $label }}">
                            {{ $label }} <i class="bi bi-x"></i>
                        </a>
                    </li>
                @endforeach

                <li>
                    <a href="{{ route('catalog.preview') }}" class="chip-reset">Clear all</a>
                </li>
            </ul>
        @endif

    </div>

    <div class="product-grid catalog-product-grid">

        @forelse ($products as $product)
            @include('components.product-card', ['product' => $product])
        @empty
            <div class="catalog-empty">
                <i class="bi bi-search"></i>
                <h2>No products found</h2>
                <p>Try removing a filter or searching with different terms.</p>
                <a href="{{ route('catalog.preview') }}" class="btn-primary-store">View all products</a>
            </div>
        @endforelse

    </div>

    {{-- PAGINATION --}}
    @if ($products->hasPages())
        <nav class="catalog-pagination" aria-label="Pagination">

            @if ($products->onFirstPage())
                <span class="pagination-disabled"><i class="bi bi-arrow-left"></i> Previous</span>
            @else
                <a href="{{ $products->previousPageUrl() }}" rel="prev"><i class="bi bi-arrow-left"></i> Previous</a>
            @endif

            <span class="pagination-page">Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</span>

            @if ($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}" rel="next">Next <i class="bi bi-arrow-right"></i></a>
            @else
                <span class="pagination-disabled">Next <i class="bi bi-arrow-right"></i></span>
            @endif

        </nav>
    @endif

</section>

@endsection
