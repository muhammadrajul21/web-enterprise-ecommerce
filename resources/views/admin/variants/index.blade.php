<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Variant Management</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link
        rel="stylesheet"
        href="{{ asset('css/admin/orders.css') }}">

    <link
        rel="stylesheet"
        href="{{ asset('css/admin/variants.css') }}">

</head>

<body>

    <div class="admin-layout">

        {{-- SIDEBAR --}}
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

                <a href="{{ route('admin.products.index') }}">
                    <i class="bi bi-box-seam"></i>
                    Products
                </a>

                <a
                    href="{{ route('admin.variants.index') }}"
                    class="active">

                    <i class="bi bi-boxes"></i>
                    Variants

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


        {{-- MAIN --}}
        <main class="admin-main">

            <div class="admin-header">

                <div>

                    <span class="page-label">
                        ADMIN
                    </span>

                    <h1>
                        Variant Management
                    </h1>

                    <p>
                        Manage product SKU, color, size, price and status.
                    </p>

                </div>


                <div class="header-icon">

                    <i class="bi bi-boxes"></i>

                </div>

            </div>


            @if (session('success'))

            <div class="variant-alert">

                <i class="bi bi-check-circle"></i>

                {{ session('success') }}

            </div>

            @endif


            {{-- FILTER --}}
            <section class="filter-card">

                <form
                    action="{{ route('admin.variants.index') }}"
                    method="GET"
                    class="order-filter">


                    <div class="search-box">

                        <i class="bi bi-search"></i>

                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Search SKU, product, color or size...">

                    </div>


                    <select name="product">

                        <option value="">
                            All Products
                        </option>

                        @foreach ($products as $product)

                        <option
                            value="{{ $product->id }}"
                            {{ (string) $productId === (string) $product->id
                                ? 'selected'
                                : '' }}>

                            {{ $product->name }}

                        </option>

                        @endforeach

                    </select>


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


                    @if ($search || $productId || $status)

                    <a
                        href="{{ route('admin.variants.index') }}"
                        class="reset-button">

                        RESET

                    </a>

                    @endif

                </form>

            </section>


            {{-- TABLE --}}
            <section class="order-card">

                <div class="order-card-header variant-card-header">

                    <div>

                        <h2>
                            Product Variants
                        </h2>

                        <p>
                            {{ $variants->total() }}
                            total variants
                        </p>

                    </div>


                    <a
                        href="{{ route('admin.variants.create') }}"
                        class="add-variant-button">

                        <i class="bi bi-plus-lg"></i>

                        ADD VARIANT

                    </a>

                </div>


                <div class="table-wrapper">

                    <table class="order-table variant-table">

                        <thead>

                            <tr>

                                <th>
                                    PRODUCT
                                </th>

                                <th>
                                    SKU
                                </th>

                                <th>
                                    COLOR
                                </th>

                                <th>
                                    SIZE
                                </th>

                                <th>
                                    PRICE
                                </th>

                                <th>
                                    STOCK
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

                            @forelse ($variants as $variant)

                            <tr>

                                <td>

                                    <strong>
                                        {{ $variant->product?->name ?? '-' }}
                                    </strong>

                                </td>


                                <td>

                                    <span class="variant-sku">
                                        {{ $variant->sku }}
                                    </span>

                                </td>


                                <td>
                                    {{ $variant->color ?? '-' }}
                                </td>


                                <td>
                                    {{ $variant->size ?? '-' }}
                                </td>


                                <td>

                                    <strong>

                                        Rp
                                        {{ number_format(
                                        $variant->price,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                    </strong>

                                </td>


                                <td>

                                    <span class="stock-value">

                                        {{ $variant->stock }}

                                    </span>

                                </td>


                                <td>

                                    <span
                                        class="variant-status
                                    variant-status-{{ $variant->status }}">

                                        {{ strtoupper(
                                        $variant->status
                                    ) }}

                                    </span>

                                </td>


                                <td>

                                    <div class="variant-actions">

                                        <a
                                            href="{{ route(
                                            'admin.variants.edit',
                                            $variant
                                        ) }}"
                                            title="Edit variant">

                                            <i class="bi bi-pencil"></i>

                                        </a>

                                    </div>

                                </td>

                            </tr>


                            @empty

                            <tr>

                                <td
                                    colspan="8"
                                    class="variant-empty">

                                    <i class="bi bi-boxes"></i>

                                    <strong>
                                        No variants found
                                    </strong>

                                    <span>
                                        Variant data will appear here.
                                    </span>

                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                @if ($variants->hasPages())

                <div class="variant-pagination">

                    <div class="pagination-info">

                        Page
                        {{ $variants->currentPage() }}
                        of
                        {{ $variants->lastPage() }}

                    </div>


                    <div class="pagination-buttons">

                        @if ($variants->onFirstPage())

                        <span class="pagination-disabled">

                            <i class="bi bi-chevron-left"></i>
                            PREVIOUS

                        </span>

                        @else

                        <a
                            href="{{ $variants->previousPageUrl() }}"
                            class="pagination-button">

                            <i class="bi bi-chevron-left"></i>
                            PREVIOUS

                        </a>

                        @endif


                        @if ($variants->hasMorePages())

                        <a
                            href="{{ $variants->nextPageUrl() }}"
                            class="pagination-button">

                            NEXT
                            <i class="bi bi-chevron-right"></i>

                        </a>

                        @else

                        <span class="pagination-disabled">

                            NEXT
                            <i class="bi bi-chevron-right"></i>

                        </span>

                        @endif

                    </div>

                </div>

                @endif
            </section>

        </main>

    </div>

</body>

</html>