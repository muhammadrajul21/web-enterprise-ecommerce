<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Product Management</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- CSS utama Admin milik Fatur --}}
    <link
        rel="stylesheet"
        href="{{ asset('css/admin/orders.css') }}">

    {{-- Tambahan khusus halaman Product --}}
    <link
        rel="stylesheet"
        href="{{ asset('css/admin/products.css') }}">

</head>

<body>

    <div class="admin-layout">

        {{-- =========================
        SIDEBAR
    ========================= --}}
        <aside class="admin-sidebar">

            <div class="sidebar-brand">

                <h2>
                    LIFESTYLE
                </h2>

                <span>
                    ADMIN PANEL
                </span>

            </div>


            <nav class="sidebar-menu">

                <a href="{{ route('admin.dashboard') }}">

                    <i class="bi bi-grid"></i>

                    Dashboard

                </a>


                <a
                    href="{{ route('admin.products.index') }}"
                    class="active">

                    <i class="bi bi-box-seam"></i>

                    Products

                </a>

                <a href="{{ route('admin.variants.index') }}">

                    <i class="bi bi-boxes"></i>

                    Variants

                </a>

                <a href="{{ route('admin.categories.index') }}">
                    <i class="bi bi-tags"></i>
                    Categories
                </a>

                <a href="{{ route('admin.collections.index') }}">
                    <i class="bi bi-collection"></i>
                    Collections
                </a>


                <a href="{{ route('admin.vouchers.index') }}">
                    <i class="bi bi-ticket-perforated"></i>
                    Vouchers
                </a>


                <a href="{{ route('admin.orders.index') }}">

                    <i class="bi bi-bag-check"></i>

                    Orders

                </a>


                <a href="{{ route('admin.payments.index') }}">

                    <i class="bi bi-credit-card"></i>

                    Payments

                </a>

            </nav>


            <div class="sidebar-footer">

                <div class="admin-user">

                    <div class="admin-avatar">

                        {{ strtoupper(
                        substr(
                            auth()->user()->name,
                            0,
                            1
                        )
                    ) }}

                    </div>


                    <div>

                        <strong>
                            {{ auth()->user()->name }}
                        </strong>

                        <span>
                            Administrator
                        </span>

                    </div>

                </div>


                <form
                    action="{{ route('logout') }}"
                    method="POST">

                    @csrf

                    <button
                        type="submit"
                        class="logout-button">

                        <i class="bi bi-box-arrow-right"></i>

                        Logout

                    </button>

                </form>

            </div>

        </aside>


        {{-- =========================
        MAIN CONTENT
    ========================= --}}
        <main class="admin-main">


            {{-- HEADER --}}
            <div class="admin-header">

                <div>

                    <span class="page-label">
                        ADMIN
                    </span>

                    <h1>
                        Product Management
                    </h1>

                    <p>
                        Manage products, variants, prices and product status.
                    </p>

                </div>


                <div class="header-icon">

                    <i class="bi bi-box-seam"></i>

                </div>

            </div>


            {{-- =========================
            SUCCESS MESSAGE
        ========================= --}}
            @if (session('success'))

            <div class="product-alert">

                <i class="bi bi-check-circle"></i>

                {{ session('success') }}

            </div>

            @endif


            {{-- =========================
            FILTER
        ========================= --}}
            <section class="filter-card">

                <form
                    action="{{ route('admin.products.index') }}"
                    method="GET"
                    class="order-filter">


                    {{-- SEARCH --}}
                    <div class="search-box">

                        <i class="bi bi-search"></i>

                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Search product, category or segment...">

                    </div>


                    {{-- STATUS --}}
                    <select name="status">

                        <option value="">
                            All Status
                        </option>

                        <option
                            value="active"
                            {{ $status === 'active'
                            ? 'selected'
                            : '' }}>

                            Active

                        </option>

                        <option
                            value="draft"
                            {{ $status === 'draft'
                            ? 'selected'
                            : '' }}>

                            Draft

                        </option>

                        <option
                            value="inactive"
                            {{ $status === 'inactive'
                            ? 'selected'
                            : '' }}>

                            Inactive

                        </option>

                    </select>


                    <button
                        type="submit"
                        class="filter-button">

                        FILTER

                    </button>


                    @if ($search || $status)

                    <a
                        href="{{ route('admin.products.index') }}"
                        class="reset-button">

                        RESET

                    </a>

                    @endif

                </form>

            </section>


            {{-- =========================
            PRODUCT TABLE
        ========================= --}}
            <section class="order-card">


                <div class="order-card-header product-card-header">

                    <div>

                        <h2>
                            Products
                        </h2>

                        <p>
                            {{ $products->total() }}
                            total products
                        </p>

                    </div>


                    <a
                        href="{{ route('admin.products.create') }}"
                        class="add-product-button">

                        <i class="bi bi-plus-lg"></i>

                        ADD PRODUCT

                    </a>

                </div>


                <div class="table-wrapper">

                    <table class="order-table product-table">

                        <thead>

                            <tr>

                                <th>
                                    PRODUCT
                                </th>

                                <th>
                                    SEGMENT
                                </th>

                                <th>
                                    CATEGORY
                                </th>

                                <th>
                                    PRICE
                                </th>

                                <th>
                                    VARIANTS
                                </th>

                                <th>
                                    FEATURED
                                </th>

                                <th>
                                    STATUS
                                </th>

                                <th>
                                    ACTION
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse ($products as $product)

                            @php

                            $primaryImage =
                            $product
                            ->images
                            ->first();

                            $imageExists =
                            $primaryImage
                            && file_exists(
                            public_path(
                            $primaryImage->image_path
                            )
                            );

                            @endphp


                            <tr>


                                {{-- PRODUCT --}}
                                <td>

                                    <div class="product-info">

                                        <div class="product-thumbnail">

                                            @if ($imageExists)

                                            <img
                                                src="{{ asset(
                                                    $primaryImage->image_path
                                                ) }}"
                                                alt="{{ $product->name }}">

                                            @else

                                            <i class="bi bi-image"></i>

                                            @endif

                                        </div>


                                        <div class="product-name">

                                            <strong>
                                                {{ $product->name }}
                                            </strong>

                                            <span>
                                                {{ $product->slug }}
                                            </span>

                                        </div>

                                    </div>

                                </td>


                                {{-- SEGMENT --}}
                                <td>

                                    {{ $product->segment?->name ?? '-' }}

                                </td>


                                {{-- CATEGORY --}}
                                <td>

                                    {{ $product->category?->name ?? '-' }}

                                </td>


                                {{-- PRICE --}}
                                <td>

                                    @if ($product->variants_min_price !== null)

                                    <strong>

                                        Rp
                                        {{ number_format(
                                            $product->variants_min_price,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    </strong>


                                    @if (
                                    $product->variants_max_price !== null
                                    &&
                                    $product->variants_min_price
                                    !=
                                    $product->variants_max_price
                                    )

                                    <span class="price-range">

                                        -
                                        Rp
                                        {{ number_format(
                                                $product->variants_max_price,
                                                0,
                                                ',',
                                                '.'
                                            ) }}

                                    </span>

                                    @endif

                                    @else

                                    <span class="table-muted">
                                        No price
                                    </span>

                                    @endif

                                </td>


                                {{-- VARIANTS --}}
                                <td>

                                    {{ $product->variants_count }}

                                    {{ $product->variants_count == 1
                                    ? 'Variant'
                                    : 'Variants' }}

                                </td>


                                {{-- FEATURED --}}
                                <td>

                                    @if ($product->is_featured)

                                    <span class="featured-product">

                                        <i class="bi bi-star-fill"></i>

                                        Yes

                                    </span>

                                    @else

                                    <span class="table-muted">
                                        No
                                    </span>

                                    @endif

                                </td>


                                {{-- STATUS --}}
                                <td>

                                    <span
                                        class="product-status
                                    product-status-{{ $product->status }}">

                                        {{ strtoupper(
                                        $product->status
                                    ) }}

                                    </span>

                                </td>


                                {{-- ACTION --}}
                                <td>

                                    <div class="product-actions">

                                        <button
                                            type="button"
                                            title="Product detail"
                                            disabled>

                                            <i class="bi bi-eye"></i>

                                        </button>


                                        <a
                                            href="{{ route(
                                                'admin.products.edit',
                                                $product
                                            ) }}"
                                            class="product-action-link"
                                            title="Edit product">

                                            <i class="bi bi-pencil"></i>

                                        </a>

                                    </div>

                                </td>

                            </tr>


                            @empty

                            <tr>

                                <td
                                    colspan="8"
                                    class="product-empty">

                                    <i class="bi bi-box"></i>

                                    <strong>
                                        No products found
                                    </strong>

                                    <span>
                                        Product data will appear here.
                                    </span>

                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                @if ($products->hasPages())

                <div class="product-pagination">

                    {{ $products->links() }}

                </div>

                @endif

            </section>

        </main>

    </div>

</body>

</html>