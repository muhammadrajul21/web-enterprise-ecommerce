<h1>Dashboard Staff Gudang</h1>

<p>Login sebagai: {{ auth()->user()->name }}</p>

<form action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit">Logout</button>
</form>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Warehouse Dashboard</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link
        rel="stylesheet"
        href="{{ asset('css/staff/dashboard.css') }}">
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

                <a
                    href="{{ route('staff.dashboard') }}"
                    class="active">
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

                <a href="{{ route('staff.orders.shipping') }}">
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
                        Dashboard
                    </h1>

                    <p>
                        Monitor warehouse orders and inventory.
                    </p>

                </div>

                <div class="header-icon">
                    <i class="bi bi-box-seam"></i>
                </div>

            </div>


            {{-- =========================
            STATISTICS
        ========================= --}}

            <section class="stats-grid">


                {{-- PROCESSING --}}

                <div class="stat-card">

                    <div class="stat-icon">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>

                    <div class="stat-content">

                        <span>
                            PROCESSING
                        </span>

                        <strong>
                            {{ $processingOrders }}
                        </strong>

                        <p>
                            Orders ready to process
                        </p>

                    </div>

                </div>


                {{-- PACKING --}}

                <div class="stat-card">

                    <div class="stat-icon">
                        <i class="bi bi-box2"></i>
                    </div>

                    <div class="stat-content">

                        <span>
                            PACKING
                        </span>

                        <strong>
                            {{ $packingOrders }}
                        </strong>

                        <p>
                            Orders being packed
                        </p>

                    </div>

                </div>


                {{-- SHIPPED --}}

                <div class="stat-card">

                    <div class="stat-icon">
                        <i class="bi bi-truck"></i>
                    </div>

                    <div class="stat-content">

                        <span>
                            SHIPPED
                        </span>

                        <strong>
                            {{ $shippedOrders }}
                        </strong>

                        <p>
                            Orders already shipped
                        </p>

                    </div>

                </div>


                {{-- LOW STOCK --}}

                <div class="stat-card">

                    <div class="stat-icon warning">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>

                    <div class="stat-content">

                        <span>
                            LOW STOCK
                        </span>

                        <strong>
                            {{ $lowStockVariants }}
                        </strong>

                        <p>
                            Variants with stock ≤ 5
                        </p>

                    </div>

                </div>

            </section>


            {{-- =========================
            CONTENT GRID
        ========================= --}}

            <div class="dashboard-grid">


                {{-- RECENT ORDERS --}}

                <section class="dashboard-card">

                    <div class="card-header">

                        <div>
                            <h2>
                                Recent Orders
                            </h2>

                            <p>
                                Orders requiring warehouse attention.
                            </p>
                        </div>

                        <i class="bi bi-bag"></i>

                    </div>


                    <div class="order-list">

                        @forelse ($recentOrders as $order)

                        @php

                        $statusLabel = match ($order->status) {

                        'processing'
                        => 'Processing',

                        'packing'
                        => 'Packing',

                        'shipped'
                        => 'Shipped',

                        default
                        => ucfirst($order->status),
                        };

                        @endphp


                        <div class="order-item">

                            <div class="order-main">

                                <strong>
                                    {{ $order->order_code }}
                                </strong>

                                <span>
                                    {{ $order->user?->name ?? '-' }}
                                </span>

                            </div>


                            <div class="order-meta">

                                <strong>
                                    Rp {{ number_format(
                                        $order->total_amount,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </strong>

                                <span
                                    class="
                                        status-badge
                                        status-{{ $order->status }}
                                    ">
                                    {{ $statusLabel }}
                                </span>

                            </div>

                        </div>

                        @empty

                        <div class="empty-state">

                            <i class="bi bi-bag-x"></i>

                            <strong>
                                No active orders
                            </strong>

                            <p>
                                Processing orders will appear here.
                            </p>

                        </div>

                        @endforelse

                    </div>

                </section>


                {{-- STOCK ACTIVITY --}}

                <section class="dashboard-card">

                    <div class="card-header">

                        <div>

                            <h2>
                                Stock Activity
                            </h2>

                            <p>
                                Latest inventory activity.
                            </p>

                        </div>

                        <i class="bi bi-clock-history"></i>

                    </div>


                    <div class="stock-activity">

                        @forelse ($recentStockLogs as $log)

                        <div class="activity-item">

                            <div class="activity-icon">
                                <i class="bi bi-box-seam"></i>
                            </div>


                            <div class="activity-content">

                                <strong>
                                    {{ $log->variant?->product?->name
                                        ?? 'Product'
                                    }}
                                </strong>

                                <span>
                                    SKU:
                                    {{ $log->variant?->sku ?? '-' }}
                                </span>

                                <small>
                                    {{ $log->created_at
                                        ?->format('d M Y, H:i')
                                    }}
                                </small>

                            </div>

                        </div>

                        @empty

                        <div class="empty-state">

                            <i class="bi bi-clock-history"></i>

                            <strong>
                                No stock activity
                            </strong>

                            <p>
                                Stock activity will appear here.
                            </p>

                        </div>

                        @endforelse

                    </div>

                </section>

            </div>

        </main>

    </div>

</body>

</html>