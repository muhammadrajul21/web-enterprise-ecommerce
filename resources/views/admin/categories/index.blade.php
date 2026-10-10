<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Category Management</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link
        rel="stylesheet"
        href="{{ asset('css/admin/orders.css') }}">

    <link
        rel="stylesheet"
        href="{{ asset('css/admin/categories.css') }}">

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

            <a href="{{ route('admin.variants.index') }}">
                <i class="bi bi-boxes"></i>
                Variants
            </a>

            <a
                href="{{ route('admin.categories.index') }}"
                class="active">

                <i class="bi bi-tags"></i>
                Categories

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
                    Category Management
                </h1>

                <p>
                    Manage product categories and catalog organization.
                </p>

            </div>


            <div class="header-icon">

                <i class="bi bi-tags"></i>

            </div>

        </div>


        {{-- SUCCESS --}}
        @if (session('success'))

            <div class="category-alert success">

                <i class="bi bi-check-circle"></i>

                {{ session('success') }}

            </div>

        @endif


        {{-- ERROR --}}
        @if (session('error'))

            <div class="category-alert error">

                <i class="bi bi-exclamation-circle"></i>

                {{ session('error') }}

            </div>

        @endif


        {{-- FILTER --}}
        <section class="filter-card">

            <form
                action="{{ route('admin.categories.index') }}"
                method="GET"
                class="order-filter">

                <div class="search-box">

                    <i class="bi bi-search"></i>

                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search category...">

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
                        href="{{ route('admin.categories.index') }}"
                        class="reset-button">

                        RESET

                    </a>

                @endif

            </form>

        </section>


        {{-- TABLE --}}
        <section class="order-card">

            <div class="order-card-header category-card-header">

                <div>

                    <h2>
                        Categories
                    </h2>

                    <p>
                        {{ $categories->total() }}
                        total categories
                    </p>

                </div>


                <a
                    href="{{ route('admin.categories.create') }}"
                    class="add-category-button">

                    <i class="bi bi-plus-lg"></i>

                    ADD CATEGORY

                </a>

            </div>


            <div class="table-wrapper">

                <table class="order-table category-table">

                    <thead>

                    <tr>

                        <th>
                            CATEGORY
                        </th>

                        <th>
                            SLUG
                        </th>

                        <th>
                            DESCRIPTION
                        </th>

                        <th>
                            PRODUCTS
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

                    @forelse ($categories as $category)

                        <tr>

                            <td>

                                <strong>
                                    {{ $category->name }}
                                </strong>

                            </td>


                            <td>

                                <span class="category-slug">
                                    {{ $category->slug }}
                                </span>

                            </td>


                            <td>

                                <span class="category-description">

                                    {{ $category->description
                                        ?: '-' }}

                                </span>

                            </td>


                            <td>

                                <span class="product-count">

                                    {{ $category->products_count }}

                                    {{ $category->products_count == 1
                                        ? 'Product'
                                        : 'Products' }}

                                </span>

                            </td>


                            <td>

                                <span
                                    class="category-status
                                    {{ $category->is_active
                                        ? 'category-status-active'
                                        : 'category-status-inactive' }}">

                                    {{ $category->is_active
                                        ? 'ACTIVE'
                                        : 'INACTIVE' }}

                                </span>

                            </td>


                            <td>

                                <div class="category-actions">


                                    {{-- EDIT --}}
                                    <a
                                        href="{{ route(
                                            'admin.categories.edit',
                                            $category
                                        ) }}"
                                        title="Edit category">

                                        <i class="bi bi-pencil"></i>

                                    </a>


                                    {{-- DELETE --}}
                                    <form
                                        action="{{ route(
                                            'admin.categories.destroy',
                                            $category
                                        ) }}"
                                        method="POST"
                                        onsubmit="return confirm(
                                            'Hapus category {{ $category->name }}?'
                                        );">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            title="Delete category">

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
                                class="category-empty">

                                <i class="bi bi-tags"></i>

                                <strong>
                                    No categories found
                                </strong>

                                <span>
                                    Category data will appear here.
                                </span>

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>


            {{-- PAGINATION --}}
            @if ($categories->hasPages())

                <div class="category-pagination">

                    <div class="pagination-info">

                        Page
                        {{ $categories->currentPage() }}
                        of
                        {{ $categories->lastPage() }}

                    </div>


                    <div class="pagination-buttons">

                        @if ($categories->onFirstPage())

                            <span class="pagination-disabled">

                                <i class="bi bi-chevron-left"></i>

                                PREVIOUS

                            </span>

                        @else

                            <a
                                href="{{ $categories->previousPageUrl() }}"
                                class="pagination-button">

                                <i class="bi bi-chevron-left"></i>

                                PREVIOUS

                            </a>

                        @endif


                        @if ($categories->hasMorePages())

                            <a
                                href="{{ $categories->nextPageUrl() }}"
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