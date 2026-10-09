<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Processing Orders</title>

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


            <a
                href="{{ route('staff.orders.processing') }}"
                class="active"
            >
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


    <main class="staff-main">

        <div class="page-header">

            <div>

                <span class="page-label">
                    WAREHOUSE
                </span>

                <h1>
                    Processing Orders
                </h1>

                <p>
                    Process verified orders and prepare them for packing.
                </p>

            </div>

            <div class="header-icon">
                <i class="bi bi-box2-heart"></i>
            </div>

        </div>


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


        <section class="order-filter-card">

            <form
                action="{{ route('staff.orders.processing') }}"
                method="GET"
                class="order-filter-form"
            >

                <div class="order-search">

                    <i class="bi bi-search"></i>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search order or customer..."
                    >

                </div>


                <select name="status">

                    <option value="">
                        All Status
                    </option>

                    <option
                        value="processing"
                        {{ request('status') === 'processing'
                            ? 'selected'
                            : ''
                        }}
                    >
                        Processing
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
                        href="{{ route('staff.orders.processing') }}"
                        class="order-reset-button"
                    >
                        RESET
                    </a>

                @endif

            </form>

        </section>


        <section class="processing-card">

            <div class="processing-card-header">

                <div>

                    <h2>
                        Warehouse Orders
                    </h2>

                    <p>
                        {{ $orders->total() }}
                        active orders
                    </p>

                </div>

            </div>


            <div class="processing-list">

                @forelse ($orders as $order)

                    <article class="processing-order">

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
                                    order-status
                                    order-status-{{ $order->status }}
                                "
                            >
                                {{ ucwords(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $order->status
                                    )
                                ) }}
                            </span>

                        </div>


                        <div class="processing-order-body">

                            <div class="order-information">

                                <div>

                                    <span>
                                        Recipient
                                    </span>

                                    <strong>
                                        {{ $order->recipient_name }}
                                    </strong>

                                </div>


                                <div>

                                    <span>
                                        Phone
                                    </span>

                                    <strong>
                                        {{ $order->recipient_phone }}
                                    </strong>

                                </div>


                                <div>

                                    <span>
                                        Destination
                                    </span>

                                    <strong>
                                        {{ $order->shipping_city }},
                                        {{ $order->shipping_province }}
                                    </strong>

                                </div>


                                <div>

                                    <span>
                                        Total
                                    </span>

                                    <strong>
                                        Rp {{ number_format(
                                            $order->total_amount,
                                            0,
                                            ',',
                                            '.'
                                        ) }}
                                    </strong>

                                </div>

                            </div>


                            <div class="order-items">

                                <div class="items-title">

                                    <span>
                                        ITEMS
                                    </span>

                                    <strong>
                                        {{ $order->items->sum('quantity') }}
                                        pcs
                                    </strong>

                                </div>


                                @foreach ($order->items as $item)

                                    <div class="warehouse-item">

                                        <div>

                                            <strong>
                                                {{ $item->product_name }}
                                            </strong>

                                            <span>
                                                SKU:
                                                {{ $item->sku ?? '-' }}
                                            </span>

                                        </div>


                                        <div class="warehouse-variant">

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


                        <div class="processing-order-footer">

                            <span>
                                Created:
                                {{ $order->created_at
                                    ?->format('d M Y, H:i')
                                }}
                            </span>


                            @if ($order->status === 'processing')

                                <form
                                    action="{{ route(
                                        'staff.orders.packing',
                                        $order
                                    ) }}"
                                    method="POST"
                                    onsubmit="
                                        return confirm(
                                            'Move this order to packing?'
                                        );
                                    "
                                >

                                    @csrf

                                    <button
                                        type="submit"
                                        class="packing-button"
                                    >

                                        <i class="bi bi-box2"></i>

                                        MARK AS PACKING

                                    </button>

                                </form>

                            @elseif ($order->status === 'packing')

                                <div class="packing-ready">

                                    <i class="bi bi-check-circle"></i>

                                    Ready for shipping

                                </div>

                            @endif

                        </div>

                    </article>

                @empty

                    <div class="processing-empty">

                        <i class="bi bi-box2"></i>

                        <h3>
                            No processing orders
                        </h3>

                        <p>
                            Verified customer orders will appear here.
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