<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Voucher Management</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link
        rel="stylesheet"
        href="{{ asset('css/admin/orders.css') }}">

    <link
        rel="stylesheet"
        href="{{ asset('css/admin/vouchers.css') }}">
</head>

<body>

<div class="admin-layout">

    <aside class="admin-sidebar">

        <div class="sidebar-brand">
            <h2>LIFESTYLE</h2>
            <span>ADMIN PANEL</span>
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

            <a href="{{ route('admin.categories.index') }}">
                <i class="bi bi-tags"></i>
                Categories
            </a>

            <a href="{{ route('admin.collections.index') }}">
                <i class="bi bi-collection"></i>
                Collections
            </a>

            <a
                href="{{ route('admin.vouchers.index') }}"
                class="active">

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
                    Voucher Management
                </h1>

                <p>
                    Manage discounts, usage limits, and voucher periods.
                </p>

            </div>

            <div class="header-icon">

                <i class="bi bi-ticket-perforated"></i>

            </div>

        </div>


        @if (session('success'))

            <div class="voucher-alert success">

                <i class="bi bi-check-circle"></i>

                {{ session('success') }}

            </div>

        @endif


        @if (session('error'))

            <div class="voucher-alert error">

                <i class="bi bi-exclamation-circle"></i>

                {{ session('error') }}

            </div>

        @endif


        <section class="filter-card">

            <form
                action="{{ route('admin.vouchers.index') }}"
                method="GET"
                class="order-filter">

                <div class="search-box">

                    <i class="bi bi-search"></i>

                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search code or voucher name...">

                </div>


                <select name="type">

                    <option value="">
                        All Types
                    </option>

                    <option
                        value="percentage"
                        {{ $type === 'percentage'
                            ? 'selected'
                            : '' }}>

                        Percentage

                    </option>

                    <option
                        value="fixed"
                        {{ $type === 'fixed'
                            ? 'selected'
                            : '' }}>

                        Fixed

                    </option>

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


                @if ($search || $type || $status)

                    <a
                        href="{{ route('admin.vouchers.index') }}"
                        class="reset-button">

                        RESET

                    </a>

                @endif

            </form>

        </section>


        <section class="order-card">

            <div class="order-card-header voucher-card-header">

                <div>

                    <h2>
                        Vouchers
                    </h2>

                    <p>
                        {{ $vouchers->total() }}
                        total vouchers
                    </p>

                </div>


                <a
                    href="{{ route('admin.vouchers.create') }}"
                    class="add-voucher-button">

                    <i class="bi bi-plus-lg"></i>

                    ADD VOUCHER

                </a>

            </div>


            <div class="table-wrapper">

                <table class="order-table voucher-table">

                    <thead>

                    <tr>
                        <th>VOUCHER</th>
                        <th>DISCOUNT</th>
                        <th>MIN. ORDER</th>
                        <th>USAGE</th>
                        <th>PERIOD</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                    </tr>

                    </thead>

                    <tbody>

                    @forelse ($vouchers as $voucher)

                        <tr>

                            <td>

                                <div class="voucher-identity">

                                    <strong>
                                        {{ $voucher->code }}
                                    </strong>

                                    <span>
                                        {{ $voucher->name }}
                                    </span>

                                </div>

                            </td>


                            <td>

                                @if ($voucher->discount_type === 'percentage')

                                    <strong class="voucher-discount">
                                        {{ rtrim(
                                            rtrim(
                                                number_format(
                                                    $voucher->discount_value,
                                                    2,
                                                    '.',
                                                    ''
                                                ),
                                                '0'
                                            ),
                                            '.'
                                        ) }}%
                                    </strong>

                                @else

                                    <strong class="voucher-discount">

                                        Rp{{ number_format(
                                            $voucher->discount_value,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    </strong>

                                @endif

                                <span class="voucher-type">

                                    {{ strtoupper(
                                        $voucher->discount_type
                                    ) }}

                                </span>

                            </td>


                            <td>

                                Rp{{ number_format(
                                    $voucher->min_order_amount,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>


                            <td>

                                <strong>
                                    {{ $voucher->used_count }}
                                </strong>

                                /

                                {{ $voucher->usage_limit
                                    ?? '∞' }}

                            </td>


                            <td>

                                <div class="voucher-period">

                                    <span>

                                        {{ $voucher->starts_at
                                            ? $voucher->starts_at->format('d M Y')
                                            : 'No start limit' }}

                                    </span>

                                    <small>
                                        to
                                    </small>

                                    <span>

                                        {{ $voucher->expires_at
                                            ? $voucher->expires_at->format('d M Y')
                                            : 'No expiry' }}

                                    </span>

                                </div>

                            </td>


                            <td>

                                <span
                                    class="voucher-status
                                    {{ $voucher->is_active
                                        ? 'voucher-status-active'
                                        : 'voucher-status-inactive' }}">

                                    {{ $voucher->is_active
                                        ? 'ACTIVE'
                                        : 'INACTIVE' }}

                                </span>

                            </td>


                            <td>

                                <div class="voucher-actions">

                                    <a
                                        href="{{ route(
                                            'admin.vouchers.edit',
                                            $voucher
                                        ) }}"
                                        title="Edit voucher">

                                        <i class="bi bi-pencil"></i>

                                    </a>


                                    <form
                                        action="{{ route(
                                            'admin.vouchers.destroy',
                                            $voucher
                                        ) }}"
                                        method="POST"
                                        onsubmit="return confirm(
                                            'Hapus voucher {{ $voucher->code }}?'
                                        );">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            title="Delete voucher">

                                            <i class="bi bi-trash"></i>

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="voucher-empty">

                                <i class="bi bi-ticket-perforated"></i>

                                <strong>
                                    No vouchers found
                                </strong>

                                <span>
                                    Voucher data will appear here.
                                </span>

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>


            @if ($vouchers->hasPages())

                <div class="voucher-pagination">

                    <div class="pagination-info">

                        Page
                        {{ $vouchers->currentPage() }}
                        of
                        {{ $vouchers->lastPage() }}

                    </div>


                    <div class="pagination-buttons">

                        @if ($vouchers->onFirstPage())

                            <span class="pagination-disabled">

                                <i class="bi bi-chevron-left"></i>
                                PREVIOUS

                            </span>

                        @else

                            <a
                                href="{{ $vouchers->previousPageUrl() }}"
                                class="pagination-button">

                                <i class="bi bi-chevron-left"></i>
                                PREVIOUS

                            </a>

                        @endif


                        @if ($vouchers->hasMorePages())

                            <a
                                href="{{ $vouchers->nextPageUrl() }}"
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