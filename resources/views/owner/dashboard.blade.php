<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Owner Dashboard</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/owner/dashboard.css') }}"
    >
</head>

<body>

    <div class="owner-layout">

        {{-- =========================
            SIDEBAR
        ========================= --}}

        <aside class="owner-sidebar">

            <div class="sidebar-brand">

                <h2>
                    LIFESTYLE
                </h2>

                <span>
                    OWNER PANEL
                </span>

            </div>


            <nav class="sidebar-menu">

                <a
                    href="{{ route('owner.dashboard') }}"
                    class="active"
                >
                    <i class="bi bi-grid"></i>
                    Dashboard
                </a>

            </nav>


            <div class="sidebar-footer">

                <div class="owner-user">

                    <div class="owner-avatar">
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
                            Owner
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

        <main class="owner-main">

            {{-- HEADER --}}

            <div class="page-header">

                <div>

                    <span class="page-label">
                        BUSINESS OVERVIEW
                    </span>

                    <h1>
                        Owner Dashboard
                    </h1>

                    <p>
                        Monitor sales, orders, customers,
                        inventory, and customer feedback.
                    </p>

                </div>


                <div class="header-icon">
                    <i class="bi bi-bar-chart-line"></i>
                </div>

            </div>


            {{-- =========================
                MAIN STATISTICS
            ========================= --}}

            <section class="stats-grid">

                <div class="stat-card">

                    <div class="stat-icon">
                        <i class="bi bi-cash-stack"></i>
                    </div>

                    <div class="stat-content">

                        <span>
                            TOTAL REVENUE
                        </span>

                        <strong class="revenue-value">
                            Rp {{ number_format(
                                $totalRevenue,
                                0,
                                ',',
                                '.'
                            ) }}
                        </strong>

                        <p>
                            Verified payments
                        </p>

                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-icon">
                        <i class="bi bi-bag-check"></i>
                    </div>

                    <div class="stat-content">

                        <span>
                            TOTAL ORDERS
                        </span>

                        <strong>
                            {{ $totalOrders }}
                        </strong>

                        <p>
                            All customer orders
                        </p>

                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-icon">
                        <i class="bi bi-check2-circle"></i>
                    </div>

                    <div class="stat-content">

                        <span>
                            COMPLETED
                        </span>

                        <strong>
                            {{ $completedOrders }}
                        </strong>

                        <p>
                            Completed orders
                        </p>

                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-icon">
                        <i class="bi bi-people"></i>
                    </div>

                    <div class="stat-content">

                        <span>
                            CUSTOMERS
                        </span>

                        <strong>
                            {{ $totalCustomers }}
                        </strong>

                        <p>
                            Registered customers
                        </p>

                    </div>

                </div>

            </section>


            {{-- =========================
                ORDER STATUS
            ========================= --}}

            <section class="dashboard-card">

                <div class="card-header">

                    <div>

                        <h2>
                            Order Status Overview
                        </h2>

                        <p>
                            Current distribution of order statuses.
                        </p>

                    </div>

                    <i class="bi bi-list-check"></i>

                </div>


                <div class="status-grid">

                    @php
                        $statuses = [
                            'pending_payment' => [
                                'label' => 'Pending Payment',
                                'icon' => 'bi-wallet2',
                            ],
                            'waiting_verification' => [
                                'label' => 'Verification',
                                'icon' => 'bi-hourglass-split',
                            ],
                            'processing' => [
                                'label' => 'Processing',
                                'icon' => 'bi-arrow-repeat',
                            ],
                            'packing' => [
                                'label' => 'Packing',
                                'icon' => 'bi-box2',
                            ],
                            'shipped' => [
                                'label' => 'Shipped',
                                'icon' => 'bi-truck',
                            ],
                            'completed' => [
                                'label' => 'Completed',
                                'icon' => 'bi-check-circle',
                            ],
                            'cancelled' => [
                                'label' => 'Cancelled',
                                'icon' => 'bi-x-circle',
                            ],
                        ];
                    @endphp


                    @foreach($statuses as $status => $data)

                        <div class="status-item">

                            <i class="bi {{ $data['icon'] }}"></i>

                            <div>

                                <span>
                                    {{ $data['label'] }}
                                </span>

                                <strong>
                                    {{ $orderStatusCounts[$status] ?? 0 }}
                                </strong>

                            </div>

                        </div>

                    @endforeach

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
                                Latest customer transactions.
                            </p>

                        </div>

                        <i class="bi bi-receipt"></i>

                    </div>


                    <div class="order-list">

                        @forelse($recentOrders as $order)

                            @php
                                $statusLabel = [
                                    'pending_payment' => 'Pending Payment',
                                    'waiting_verification' => 'Verification',
                                    'processing' => 'Processing',
                                    'packing' => 'Packing',
                                    'shipped' => 'Shipped',
                                    'completed' => 'Completed',
                                    'cancelled' => 'Cancelled',
                                ][$order->status]
                                ?? ucwords(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $order->status
                                    )
                                );
                            @endphp


                            <div class="order-item">

                                <div class="order-main">

                                    <strong>
                                        {{ $order->order_code }}
                                    </strong>

                                    <span>
                                        {{ $order->user?->name ?? '-' }}
                                    </span>

                                    <small>
                                        {{ $order->created_at
                                            ->format('d M Y, H:i') }}
                                    </small>

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
                                        "
                                    >
                                        {{ $statusLabel }}
                                    </span>

                                </div>

                            </div>

                        @empty

                            <div class="empty-state">

                                <i class="bi bi-bag-x"></i>

                                <strong>
                                    No orders yet
                                </strong>

                                <p>
                                    Customer orders will appear here.
                                </p>

                            </div>

                        @endforelse

                    </div>

                </section>


                {{-- BUSINESS HEALTH --}}

                <section class="dashboard-card">

                    <div class="card-header">

                        <div>

                            <h2>
                                Business Health
                            </h2>

                            <p>
                                Inventory and customer feedback summary.
                            </p>

                        </div>

                        <i class="bi bi-activity"></i>

                    </div>


                    <div class="health-grid">

                        <div class="health-item">

                            <div class="health-icon warning">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>

                            <div>

                                <span>
                                    LOW STOCK
                                </span>

                                <strong>
                                    {{ $lowStockCount }}
                                </strong>

                                <small>
                                    Stock between 1–5
                                </small>

                            </div>

                        </div>


                        <div class="health-item">

                            <div class="health-icon danger">
                                <i class="bi bi-box-seam"></i>
                            </div>

                            <div>

                                <span>
                                    OUT OF STOCK
                                </span>

                                <strong>
                                    {{ $outOfStockCount }}
                                </strong>

                                <small>
                                    No stock available
                                </small>

                            </div>

                        </div>


                        <div class="health-item">

                            <div class="health-icon">
                                <i class="bi bi-star"></i>
                            </div>

                            <div>

                                <span>
                                    AVG. RATING
                                </span>

                                <strong>
                                    {{ number_format(
                                        $averageRating,
                                        1
                                    ) }}
                                </strong>

                                <small>
                                    Out of 5
                                </small>

                            </div>

                        </div>


                        <div class="health-item">

                            <div class="health-icon">
                                <i class="bi bi-chat-square-text"></i>
                            </div>

                            <div>

                                <span>
                                    TOTAL REVIEWS
                                </span>

                                <strong>
                                    {{ $totalReviews }}
                                </strong>

                                <small>
                                    Visible reviews
                                </small>

                            </div>

                        </div>

                    </div>

                </section>

            </div>


            {{-- =========================
                LOW STOCK
            ========================= --}}

            <section class="dashboard-card stock-card">

                <div class="card-header">

                    <div>

                        <h2>
                            Low Stock Variants
                        </h2>

                        <p>
                            Variants that may require restocking.
                        </p>

                    </div>

                    <i class="bi bi-box-seam"></i>

                </div>


                <div class="stock-table-wrapper">

                    <table class="stock-table">

                        <thead>

                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Variant</th>
                                <th>Stock</th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse($lowStockVariants as $variant)

                                <tr>

                                    <td>
                                        <strong>
                                            {{ $variant->product?->name
                                                ?? 'Product' }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $variant->sku }}
                                    </td>

                                    <td>
                                        {{ $variant->color ?: '-' }}
                                        /
                                        {{ $variant->size ?: '-' }}
                                    </td>

                                    <td>

                                        <span class="stock-badge">
                                            {{ $variant->stock }}
                                        </span>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="4"
                                        class="table-empty"
                                    >
                                        <i class="bi bi-check-circle"></i>

                                        No low-stock variants.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </section>

        </main>

    </div>

</body>

</html>