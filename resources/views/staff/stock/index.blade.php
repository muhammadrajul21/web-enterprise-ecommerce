<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Stock Management</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/staff/dashboard.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/staff/stock.css') }}"
    >
</head>

<body>

<div class="staff-layout">

    {{-- =========================
        SIDEBAR
    ========================= --}}

    <aside class="staff-sidebar">

        <div class="sidebar-brand">
            <h2>LIFESTYLE</h2>
            <span>WAREHOUSE PANEL</span>
        </div>


        <nav class="sidebar-menu">

            <a href="{{ route('staff.dashboard') }}">
                <i class="bi bi-grid"></i>
                Dashboard
            </a>


            <a
                href="{{ route('staff.stock.index') }}"
                class="active"
            >
                <i class="bi bi-box-seam"></i>
                Stock
            </a>


            <a href="#">
                <i class="bi bi-box2-heart"></i>
                Processing
            </a>


            <a href="#">
                <i class="bi bi-truck"></i>
                Shipping
            </a>

        </nav>


        <div class="sidebar-footer">

            <div class="staff-user">

                <div class="staff-avatar">

                    {{ strtoupper(
                        substr(auth()->user()->name, 0, 1)
                    ) }}

                </div>

                <div>

                    <strong>
                        {{ auth()->user()->name }}
                    </strong>

                    <span>
                        Staff Gudang
                    </span>

                </div>

            </div>


            <form
                action="{{ route('logout') }}"
                method="POST"
            >

                @csrf

                <button
                    type="submit"
                    class="logout-button"
                >

                    <i class="bi bi-box-arrow-right"></i>
                    Logout

                </button>

            </form>

        </div>

    </aside>


    {{-- =========================
        MAIN
    ========================= --}}

    <main class="staff-main">

        {{-- HEADER --}}

        <div class="page-header">

            <div>

                <span class="page-label">
                    WAREHOUSE
                </span>

                <h1>
                    Stock Management
                </h1>

                <p>
                    Manage product inventory and stock movements.
                </p>

            </div>


            <div class="header-icon">
                <i class="bi bi-box-seam"></i>
            </div>

        </div>


        {{-- =========================
            ALERT
        ========================= --}}

        @if (session('success'))

            <div class="stock-alert stock-alert-success">

                <i class="bi bi-check-circle"></i>

                {{ session('success') }}

            </div>

        @endif


        @if ($errors->any())

            <div class="stock-alert stock-alert-error">

                <i class="bi bi-exclamation-circle"></i>

                <div>

                    @foreach ($errors->all() as $error)

                        <div>
                            {{ $error }}
                        </div>

                    @endforeach

                </div>

            </div>

        @endif


        {{-- =========================
            FILTER
        ========================= --}}

        <section class="stock-filter-card">

            <form
                action="{{ route('staff.stock.index') }}"
                method="GET"
                class="stock-filter-form"
            >

                <div class="stock-search">

                    <i class="bi bi-search"></i>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search product, SKU, color or size..."
                    >

                </div>


                <select name="stock_filter">

                    <option value="">
                        All Stock
                    </option>

                    <option
                        value="available"
                        {{ request('stock_filter') === 'available'
                            ? 'selected'
                            : ''
                        }}
                    >
                        Available
                    </option>

                    <option
                        value="low"
                        {{ request('stock_filter') === 'low'
                            ? 'selected'
                            : ''
                        }}
                    >
                        Low Stock
                    </option>

                    <option
                        value="out"
                        {{ request('stock_filter') === 'out'
                            ? 'selected'
                            : ''
                        }}
                    >
                        Out of Stock
                    </option>

                </select>


                <select name="status">

                    <option value="">
                        All Status
                    </option>

                    <option
                        value="active"
                        {{ request('status') === 'active'
                            ? 'selected'
                            : ''
                        }}
                    >
                        Active
                    </option>

                    <option
                        value="inactive"
                        {{ request('status') === 'inactive'
                            ? 'selected'
                            : ''
                        }}
                    >
                        Inactive
                    </option>

                </select>


                <button
                    type="submit"
                    class="stock-filter-button"
                >
                    FILTER
                </button>


                @if (
                    request('search')
                    || request('stock_filter')
                    || request('status')
                )

                    <a
                        href="{{ route('staff.stock.index') }}"
                        class="stock-reset-button"
                    >
                        RESET
                    </a>

                @endif

            </form>

        </section>


        {{-- =========================
            STOCK TABLE
        ========================= --}}

        <section class="stock-card">

            <div class="stock-card-header">

                <div>

                    <h2>
                        Product Variants
                    </h2>

                    <p>
                        {{ $variants->total() }}
                        variants found
                    </p>

                </div>

            </div>


            <div class="stock-table-wrapper">

                <table class="stock-table">

                    <thead>

                    <tr>
                        <th>PRODUCT</th>
                        <th>SKU</th>
                        <th>COLOR</th>
                        <th>SIZE</th>
                        <th>STOCK</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                    </tr>

                    </thead>


                    <tbody>

                    @forelse ($variants as $variant)

                        @php

                            if ($variant->stock == 0) {
                                $stockClass = 'out';
                                $stockLabel = 'Out of Stock';
                            } elseif ($variant->stock <= 5) {
                                $stockClass = 'low';
                                $stockLabel = 'Low Stock';
                            } else {
                                $stockClass = 'available';
                                $stockLabel = 'Available';
                            }

                        @endphp


                        <tr>

                            <td>

                                <div class="stock-product">

                                    <div class="stock-product-icon">

                                        <i class="bi bi-box"></i>

                                    </div>


                                    <div>

                                        <strong>
                                            {{ $variant->product?->name
                                                ?? 'Product'
                                            }}
                                        </strong>

                                        <span>
                                            Variant #{{ $variant->id }}
                                        </span>

                                    </div>

                                </div>

                            </td>


                            <td>

                                <strong>
                                    {{ $variant->sku }}
                                </strong>

                            </td>


                            <td>
                                {{ $variant->color ?? '-' }}
                            </td>


                            <td>
                                {{ $variant->size ?? '-' }}
                            </td>


                            <td>

                                <div class="stock-value">

                                    <strong>
                                        {{ $variant->stock }}
                                    </strong>

                                    <span
                                        class="
                                            stock-condition
                                            stock-{{ $stockClass }}
                                        "
                                    >
                                        {{ $stockLabel }}
                                    </span>

                                </div>

                            </td>


                            <td>

                                <span
                                    class="
                                        variant-status
                                        variant-{{ $variant->status }}
                                    "
                                >
                                    {{ ucfirst($variant->status) }}
                                </span>

                            </td>


                            <td>

                                <button
                                    type="button"
                                    class="manage-stock-button"
                                    data-variant-id="{{ $variant->id }}"
                                    onclick="
                                        toggleStockForm(
                                            this.dataset.variantId
                                        )
                                    "
                                >

                                    <i class="bi bi-pencil-square"></i>

                                    Manage

                                </button>

                            </td>

                        </tr>


                        {{-- =========================
                            STOCK FORM
                        ========================= --}}

                        <tr
                            class="stock-form-row"
                            id="stock-form-{{ $variant->id }}"
                        >

                            <td colspan="7">

                                <div class="stock-form-container">


                                    <div class="stock-form-heading">

                                        <div>

                                            <span>
                                                UPDATE STOCK
                                            </span>

                                            <h3>
                                                {{ $variant->product?->name }}
                                            </h3>

                                            <p>
                                                {{ $variant->sku }}
                                                · Current stock:
                                                <strong>
                                                    {{ $variant->stock }}
                                                </strong>
                                            </p>

                                        </div>


                                        <button
                                            type="button"
                                            class="close-stock-form"
                                            data-variant-id="{{ $variant->id }}"
                                            onclick="
                                                toggleStockForm(
                                                    this.dataset.variantId
                                                )
                                            "
                                        >
                                            <i class="bi bi-x-lg"></i>
                                        </button>

                                    </div>


                                    <form
                                        action="{{ route(
                                            'staff.stock.update',
                                            $variant
                                        ) }}"
                                        method="POST"
                                        class="stock-update-form"
                                    >

                                        @csrf


                                        {{-- TYPE --}}

                                        <div class="form-group">

                                            <label>
                                                Stock Movement
                                            </label>

                                            <select
                                                name="type"
                                                class="movement-type"
                                                data-variant-id="{{ $variant->id }}"
                                                onchange="
                                                    changeStockType(
                                                        this.dataset.variantId
                                                    )
                                                "
                                                required
                                            >

                                                <option value="">
                                                    Select movement
                                                </option>

                                                <option value="IN">
                                                    IN - Stock Masuk
                                                </option>

                                                <option value="OUT">
                                                    OUT - Stock Keluar
                                                </option>

                                                <option value="RETURN">
                                                    RETURN - Barang Kembali
                                                </option>

                                                <option value="ADJUSTMENT">
                                                    ADJUSTMENT - Koreksi Stok
                                                </option>

                                            </select>

                                        </div>


                                        {{-- QUANTITY --}}

                                        <div
                                            class="form-group quantity-group"
                                            id="quantity-group-{{ $variant->id }}"
                                        >

                                            <label>
                                                Quantity
                                            </label>

                                            <input
                                                type="number"
                                                name="quantity"
                                                min="1"
                                                placeholder="Example: 5"
                                            >

                                        </div>


                                        {{-- NEW STOCK --}}

                                        <div
                                            class="form-group new-stock-group"
                                            id="new-stock-group-{{ $variant->id }}"
                                        >

                                            <label>
                                                New Stock
                                            </label>

                                            <input
                                                type="number"
                                                name="new_stock"
                                                min="0"
                                                placeholder="Final physical stock"
                                            >

                                            <small>
                                                Only used for Adjustment.
                                            </small>

                                        </div>


                                        {{-- NOTE --}}

                                        <div class="form-group form-note">

                                            <label>
                                                Note
                                            </label>

                                            <input
                                                type="text"
                                                name="note"
                                                maxlength="1000"
                                                placeholder="Example: Restock from supplier"
                                            >

                                        </div>


                                        <div class="form-submit">

                                            <button
                                                type="submit"
                                                class="save-stock-button"
                                            >

                                                <i class="bi bi-check-lg"></i>

                                                UPDATE STOCK

                                            </button>

                                        </div>

                                    </form>

                                </div>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="stock-empty"
                            >

                                <i class="bi bi-box-seam"></i>

                                <h3>
                                    No variants found
                                </h3>

                                <p>
                                    Product variants will appear here.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>


            {{-- =========================
                PAGINATION
            ========================= --}}

            @if ($variants->hasPages())

                <div class="stock-pagination">

                    @if ($variants->onFirstPage())

                        <span class="disabled">
                            <i class="bi bi-arrow-left"></i>
                            Previous
                        </span>

                    @else

                        <a href="{{ $variants->previousPageUrl() }}">
                            <i class="bi bi-arrow-left"></i>
                            Previous
                        </a>

                    @endif


                    <span>
                        Page
                        {{ $variants->currentPage() }}
                        of
                        {{ $variants->lastPage() }}
                    </span>


                    @if ($variants->hasMorePages())

                        <a href="{{ $variants->nextPageUrl() }}">
                            Next
                            <i class="bi bi-arrow-right"></i>
                        </a>

                    @else

                        <span class="disabled">
                            Next
                            <i class="bi bi-arrow-right"></i>
                        </span>

                    @endif

                </div>

            @endif

        </section>


        {{-- =========================
            STOCK HISTORY
        ========================= --}}

        <section class="stock-card stock-history-card">

            <div class="stock-card-header">

                <div>

                    <h2>
                        Stock History
                    </h2>

                    <p>
                        Latest inventory movements.
                    </p>

                </div>

                <i class="bi bi-clock-history"></i>

            </div>


            <div class="stock-table-wrapper">

                <table class="stock-table history-table">

                    <thead>

                    <tr>
                        <th>DATE</th>
                        <th>PRODUCT</th>
                        <th>SKU</th>
                        <th>TYPE</th>
                        <th>QTY</th>
                        <th>BEFORE</th>
                        <th>AFTER</th>
                        <th>STAFF</th>
                        <th>NOTE</th>
                    </tr>

                    </thead>


                    <tbody>

                    @forelse ($stockLogs as $log)

                        <tr>

                            <td>

                                {{ $log->created_at
                                    ?->format('d M Y')
                                }}

                                <small class="history-time">
                                    {{ $log->created_at
                                        ?->format('H:i')
                                    }}
                                </small>

                            </td>


                            <td>

                                <strong>
                                    {{ $log->variant
                                        ?->product
                                        ?->name ?? '-'
                                    }}
                                </strong>

                            </td>


                            <td>
                                {{ $log->variant?->sku ?? '-' }}
                            </td>


                            <td>

                                <span
                                    class="
                                        movement-badge
                                        movement-{{ strtolower($log->type) }}
                                    "
                                >
                                    {{ $log->type }}
                                </span>

                            </td>


                            <td>
                                {{ $log->quantity }}
                            </td>


                            <td>
                                {{ $log->stock_before }}
                            </td>


                            <td>

                                <strong>
                                    {{ $log->stock_after }}
                                </strong>

                            </td>


                            <td>
                                {{ $log->creator?->name ?? '-' }}
                            </td>


                            <td class="history-note">
                                {{ $log->note ?? '-' }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="9"
                                class="stock-empty"
                            >

                                <i class="bi bi-clock-history"></i>

                                <h3>
                                    No stock history
                                </h3>

                                <p>
                                    Update stock to create the first log.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </section>

    </main>

</div>


<script>

    function toggleStockForm(variantId) {

        const row = document.getElementById(
            'stock-form-' + variantId
        );

        if (!row) {
            return;
        }

        row.classList.toggle('show');
    }


    function changeStockType(variantId) {

        const formRow = document.getElementById(
            'stock-form-' + variantId
        );

        if (!formRow) {
            return;
        }

        const select = formRow.querySelector(
            '.movement-type'
        );

        const quantityGroup =
            document.getElementById(
                'quantity-group-' + variantId
            );

        const newStockGroup =
            document.getElementById(
                'new-stock-group-' + variantId
            );

        const quantityInput =
            quantityGroup.querySelector('input');

        const newStockInput =
            newStockGroup.querySelector('input');


        if (select.value === 'ADJUSTMENT') {

            quantityGroup.style.display = 'none';

            newStockGroup.style.display = 'flex';

            quantityInput.required = false;

            quantityInput.value = '';

            newStockInput.required = true;

        } else {

            quantityGroup.style.display = 'flex';

            newStockGroup.style.display = 'none';

            newStockInput.required = false;

            newStockInput.value = '';

            if (select.value !== '') {
                quantityInput.required = true;
            } else {
                quantityInput.required = false;
            }
        }
    }

</script>

</body>
</html>