<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Shipping Orders</title>

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
        href="{{ asset('css/staff/orders.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/staff/shipping.css') }}"
    >
</head>

<body>

<div class="staff-layout">

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

            <a href="{{ route('staff.stock.index') }}">
                <i class="bi bi-box-seam"></i>
                Stock
            </a>

            <a href="{{ route('staff.orders.processing') }}">
                <i class="bi bi-box2-heart"></i>
                Processing
            </a>

            <a
                href="{{ route('staff.orders.shipping') }}"
                class="active"
            >
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


    <main class="staff-main">

        <div class="page-header">

            <div>
                <span class="page-label">
                    WAREHOUSE
                </span>

                <h1>
                    Shipping Orders
                </h1>

                <p>
                    Input courier and tracking number for packed orders.
                </p>
            </div>

            <div class="header-icon">
                <i class="bi bi-truck"></i>
            </div>

        </div>


        {{-- ALERT --}}

        @if (session('success'))

            <div class="order-alert order-alert-success">

                <i class="bi bi-check-circle"></i>

                {{ session('success') }}

            </div>

        @endif


        @if (session('error'))

            <div class="order-alert order-alert-error">

                <i class="bi bi-exclamation-circle"></i>

                {{ session('error') }}

            </div>

        @endif


        @if ($errors->any())

            <div class="order-alert order-alert-error">

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


        {{-- FILTER --}}

        <section class="order-filter-card">

            <form
                action="{{ route('staff.orders.shipping') }}"
                method="GET"
                class="order-filter-form"
            >

                <div class="order-search">

                    <i class="bi bi-search"></i>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search order, customer or tracking number..."
                    >

                </div>


                <select name="status">

                    <option value="">
                        All Status
                    </option>

                    <option
                        value="packing"
                        {{ request('status') === 'packing'
                            ? 'selected'
                            : ''
                        }}
                    >
                        Packing
                    </option>

                    <option
                        value="shipped"
                        {{ request('status') === 'shipped'
                            ? 'selected'
                            : ''
                        }}
                    >
                        Shipped
                    </option>

                </select>


                <button
                    type="submit"
                    class="order-filter-button"
                >
                    FILTER
                </button>


                @if (
                    request('search')
                    || request('status')
                )

                    <a
                        href="{{ route('staff.orders.shipping') }}"
                        class="order-reset-button"
                    >
                        RESET
                    </a>

                @endif

            </form>

        </section>


        {{-- LIST --}}

        <section class="processing-card">

            <div class="processing-card-header">

                <div>
                    <h2>
                        Shipping Queue
                    </h2>

                    <p>
                        {{ $orders->total() }}
                        shipping records
                    </p>
                </div>

            </div>


            <div class="processing-list">

                @forelse ($orders as $order)

                    <article class="shipping-order-card">

                        <div class="processing-order-top">

                            <div>

                                <span class="order-label">
                                    ORDER
                                </span>

                                <strong class="order-code">
                                    {{ $order->order_code }}
                                </strong>

                                <span class="order-customer">

                                    {{ $order->user?->name ?? '-' }}

                                    ·

                                    {{ $order->user?->email ?? '-' }}

                                </span>

                            </div>


                            <span
                                class="
                                    shipping-status
                                    shipping-status-{{ $order->status }}
                                "
                            >

                                {{ ucfirst($order->status) }}

                            </span>

                        </div>


                        <div class="shipping-grid">

                            {{-- DESTINATION --}}

                            <div class="shipping-panel">

                                <span class="shipping-section-label">
                                    DESTINATION
                                </span>

                                <div class="destination-box">

                                    <strong>
                                        {{ $order->recipient_name }}
                                    </strong>

                                    <p>
                                        {{ $order->shipping_address }}
                                    </p>

                                    <p>
                                        {{ $order->shipping_city }},
                                        {{ $order->shipping_province }}
                                        {{ $order->shipping_postal_code }}
                                    </p>

                                    <p>
                                        {{ $order->recipient_phone }}
                                    </p>

                                </div>

                            </div>


                            {{-- ITEMS --}}

                            <div class="shipping-panel">

                                <span class="shipping-section-label">
                                    ORDER ITEMS
                                </span>


                                <div class="shipping-items">

                                    @foreach ($order->items as $item)

                                        <div class="shipping-item">

                                            <div>

                                                <strong>
                                                    {{ $item->product_name }}
                                                </strong>

                                                <span>
                                                    SKU:
                                                    {{ $item->sku ?? '-' }}
                                                </span>

                                            </div>


                                            <div>

                                                <span>
                                                    {{ $item->color ?? '-' }}
                                                </span>

                                                <span>
                                                    {{ $item->size ?? '-' }}
                                                </span>

                                                <strong>
                                                    × {{ $item->quantity }}
                                                </strong>

                                            </div>

                                        </div>

                                    @endforeach

                                </div>

                            </div>

                        </div>


                        @if ($order->status === 'packing')

                            {{-- FORM INPUT RESI --}}

                            <div class="shipping-form-wrapper">

                                <div class="shipping-form-heading">

                                    <div>

                                        <span>
                                            SHIPPING INFORMATION
                                        </span>

                                        <h3>
                                            Input Courier & Tracking Number
                                        </h3>

                                    </div>

                                </div>


                                <form
                                    action="{{ route(
                                        'staff.orders.ship',
                                        $order
                                    ) }}"
                                    method="POST"
                                    class="shipping-form"
                                    onsubmit="
                                        return confirm(
                                            'Mark this order as shipped?'
                                        );
                                    "
                                >

                                    @csrf


                                    <div class="shipping-form-group">

                                        <label>
                                            Courier
                                        </label>

                                        <input
                                            type="text"
                                            name="courier"
                                            value="{{ old('courier') }}"
                                            placeholder="Example: JNE"
                                            maxlength="100"
                                            required
                                        >

                                    </div>


                                    <div class="shipping-form-group">

                                        <label>
                                            Shipping Service
                                        </label>

                                        <input
                                            type="text"
                                            name="shipping_service"
                                            value="{{ old('shipping_service') }}"
                                            placeholder="Example: REG"
                                            maxlength="100"
                                            required
                                        >

                                    </div>


                                    <div class="shipping-form-group">

                                        <label>
                                            Tracking Number
                                        </label>

                                        <input
                                            type="text"
                                            name="tracking_number"
                                            value="{{ old('tracking_number') }}"
                                            placeholder="Example: JNE123456789"
                                            maxlength="255"
                                            required
                                        >

                                    </div>


                                    <div class="shipping-submit">

                                        <button
                                            type="submit"
                                            class="ship-button"
                                        >

                                            <i class="bi bi-truck"></i>

                                            MARK AS SHIPPED

                                        </button>

                                    </div>

                                </form>

                            </div>

                        @else

                            {{-- ALREADY SHIPPED --}}

                            <div class="shipped-information">

                                <div>

                                    <span>
                                        Courier
                                    </span>

                                    <strong>
                                        {{ $order->courier ?? '-' }}
                                    </strong>

                                </div>


                                <div>

                                    <span>
                                        Service
                                    </span>

                                    <strong>
                                        {{ $order->shipping_service ?? '-' }}
                                    </strong>

                                </div>


                                <div>

                                    <span>
                                        Tracking Number
                                    </span>

                                    <strong class="tracking-number">
                                        {{ $order->tracking_number ?? '-' }}
                                    </strong>

                                </div>


                                <div>

                                    <span>
                                        Shipped At
                                    </span>

                                    <strong>

                                        {{ $order->shipped_at
                                            ?->format('d M Y, H:i')
                                            ?? '-'
                                        }}

                                    </strong>

                                </div>

                            </div>

                        @endif

                    </article>

                @empty

                    <div class="processing-empty">

                        <i class="bi bi-truck"></i>

                        <h3>
                            No shipping orders
                        </h3>

                        <p>
                            Packed orders will appear here.
                        </p>

                    </div>

                @endforelse

            </div>


            @if ($orders->hasPages())

                <div class="processing-pagination">

                    @if ($orders->onFirstPage())

                        <span class="disabled">
                            <i class="bi bi-arrow-left"></i>
                            Previous
                        </span>

                    @else

                        <a href="{{ $orders->previousPageUrl() }}">
                            <i class="bi bi-arrow-left"></i>
                            Previous
                        </a>

                    @endif


                    <span>
                        Page
                        {{ $orders->currentPage() }}
                        of
                        {{ $orders->lastPage() }}
                    </span>


                    @if ($orders->hasMorePages())

                        <a href="{{ $orders->nextPageUrl() }}">
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

    </main>

</div>

</body>
</html>