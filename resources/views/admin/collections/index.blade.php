<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Collection Management</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link
        rel="stylesheet"
        href="{{ asset('css/admin/orders.css') }}">

    <link
        rel="stylesheet"
        href="{{ asset('css/admin/collections.css') }}">

</head>

<body>

<div class="admin-layout">

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


    <main class="admin-main">

        <div class="admin-header">

            <div>

                <span class="page-label">
                    ADMIN
                </span>

                <h1>
                    Collection Management
                </h1>

                <p>
                    Manage product collections and catalog grouping.
                </p>

            </div>


            <div class="header-icon">

                <i class="bi bi-collection"></i>

            </div>

        </div>


        @if (session('success'))

            <div class="collection-alert success">

                <i class="bi bi-check-circle"></i>

                {{ session('success') }}

            </div>

        @endif


        <section class="filter-card">

            <form
                action="{{ route('admin.collections.index') }}"
                method="GET"
                class="order-filter">

                <div class="search-box">

                    <i class="bi bi-search"></i>

                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search collection...">

                </div>


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


                @if ($search || $status)

                    <a
                        href="{{ route('admin.collections.index') }}"
                        class="reset-button">

                        RESET

                    </a>

                @endif

            </form>

        </section>


        <section class="order-card">

            <div class="order-card-header collection-card-header">

                <div>

                    <h2>
                        Collections
                    </h2>

                    <p>
                        {{ $collections->total() }}
                        total collections
                    </p>

                </div>


                <a
                    href="{{ route('admin.collections.create') }}"
                    class="add-collection-button">

                    <i class="bi bi-plus-lg"></i>

                    ADD COLLECTION

                </a>

            </div>


            <div class="table-wrapper">

                <table class="order-table collection-table">

                    <thead>

                    <tr>

                        <th>COLLECTION</th>
                        <th>SLUG</th>
                        <th>DESCRIPTION</th>
                        <th>PRODUCTS</th>
                        <th>STATUS</th>
                        <th>ACTION</th>

                    </tr>

                    </thead>


                    <tbody>

                    @forelse ($collections as $collection)

                        <tr>

                            <td>

                                <strong>
                                    {{ $collection->name }}
                                </strong>

                            </td>


                            <td>

                                <span class="collection-slug">
                                    {{ $collection->slug }}
                                </span>

                            </td>


                            <td>

                                <span class="collection-description">

                                    {{ $collection->description
                                        ?: '-' }}

                                </span>

                            </td>


                            <td>

                                <span class="product-count">

                                    {{ $collection->products_count }}

                                    {{ $collection->products_count == 1
                                        ? 'Product'
                                        : 'Products' }}

                                </span>

                            </td>


                            <td>

                                <span
                                    class="collection-status
                                    {{ $collection->is_active
                                        ? 'collection-status-active'
                                        : 'collection-status-inactive' }}">

                                    {{ $collection->is_active
                                        ? 'ACTIVE'
                                        : 'INACTIVE' }}

                                </span>

                            </td>


                            <td>

                                <div class="collection-actions">


                                    <a
                                        href="{{ route(
                                            'admin.collections.edit',
                                            $collection
                                        ) }}"
                                        title="Edit collection">

                                        <i class="bi bi-pencil"></i>

                                    </a>


                                    <form
                                        action="{{ route(
                                            'admin.collections.destroy',
                                            $collection
                                        ) }}"
                                        method="POST"
                                        onsubmit="return confirm(
                                            'Hapus collection {{ $collection->name }}?'
                                        );">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            title="Delete collection">

                                            <i class="bi bi-trash"></i>

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="collection-empty">

                                <i class="bi bi-collection"></i>

                                <strong>
                                    No collections found
                                </strong>

                                <span>
                                    Collection data will appear here.
                                </span>

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>


            @if ($collections->hasPages())

                <div class="collection-pagination">

                    <div class="pagination-info">

                        Page
                        {{ $collections->currentPage() }}
                        of
                        {{ $collections->lastPage() }}

                    </div>


                    <div class="pagination-buttons">

                        @if ($collections->onFirstPage())

                            <span class="pagination-disabled">

                                <i class="bi bi-chevron-left"></i>

                                PREVIOUS

                            </span>

                        @else

                            <a
                                href="{{ $collections->previousPageUrl() }}"
                                class="pagination-button">

                                <i class="bi bi-chevron-left"></i>

                                PREVIOUS

                            </a>

                        @endif


                        @if ($collections->hasMorePages())

                            <a
                                href="{{ $collections->nextPageUrl() }}"
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