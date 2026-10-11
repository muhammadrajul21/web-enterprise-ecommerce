<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Dashboard | LIFESTYLE</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/admin/dashboard.css') }}"
    >

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

            <a
                href="{{ route('admin.dashboard') }}"
                class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
            >
                <i class="bi bi-grid"></i>
                Dashboard
            </a>


            <a
                href="{{ route('admin.products.index') }}"
                class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}"
            >
                <i class="bi bi-box-seam"></i>
                Products
            </a>


            <a
                href="{{ route('admin.variants.index') }}"
                class="{{ request()->routeIs('admin.variants.*') ? 'active' : '' }}"
            >
                <i class="bi bi-boxes"></i>
                Variants
            </a>


            <a
                href="{{ route('admin.categories.index') }}"
                class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
            >
                <i class="bi bi-tags"></i>
                Categories
            </a>


            <a
                href="{{ route('admin.collections.index') }}"
                class="{{ request()->routeIs('admin.collections.*') ? 'active' : '' }}"
            >
                <i class="bi bi-collection"></i>
                Collections
            </a>


            <a
                href="{{ route('admin.vouchers.index') }}"
                class="{{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}"
            >
                <i class="bi bi-ticket-perforated"></i>
                Vouchers
            </a>


            <a
                href="{{ route('admin.orders.index') }}"
                class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
            >
                <i class="bi bi-bag-check"></i>
                Orders
            </a>


            <a
                href="{{ route('admin.payments.index') }}"
                class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}"
            >
                <i class="bi bi-credit-card"></i>
                Payments
            </a>

        </nav>


        <div class="sidebar-footer">

            <div class="admin-user">

                <div class="admin-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
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


    {{-- MAIN --}}
    <main class="admin-main">


        {{-- HEADER --}}
        <div class="admin-header">

            <div>

                <span class="page-label">
                    ADMIN
                </span>

                <h1>
                    Dashboard
                </h1>

                <p>
                    Monitor store performance, customer orders,
                    payments, and product data.
                </p>

            </div>


            <div class="header-icon">
                <i class="bi bi-grid"></i>
            </div>

        </div>


        {{-- MAIN STATS --}}
        <section class="stats-grid">

            <div class="stat-card">

                <div class="stat-icon">
                    <i class="bi bi-cash-stack"></i>
                </div>

                <div class="stat-content">

                    <span>
                        TOTAL REVENUE
                    </span>

                    <strong>
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
                        Customer orders
                    </p>

                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon">
                    <i class="bi bi-box-seam"></i>
                </div>

                <div class="stat-content">

                    <span>
                        PRODUCTS
                    </span>

                    <strong>
                        {{ $totalProducts }}
                    </strong>

                    <p>
                        Product records
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


        {{-- SECONDARY STATS --}}
        <section class="small-stats">

            <div class="small-stat">

                <i class="bi bi-boxes"></i>

                <div>
                    <span>PRODUCT VARIANTS</span>
                    <strong>{{ $totalVariants }}</strong>
                </div>

            </div>


            <div class="small-stat">

                <i class="bi bi-hourglass-split"></i>

                <div>
                    <span>PENDING PAYMENTS</span>
                    <strong>{{ $pendingPayments }}</strong>
                </div>

            </div>

        </section>


        {{-- ORDER STATUS --}}
        <section class="dashboard-card">

            <div class="card-header">

                <div>

                    <h2>
                        Order Status Overview
                    </h2>

                    <p>
                        Current distribution of all customer orders.
                    </p>

                </div>

                <i class="bi bi-list-check"></i>

            </div>


            @php
                $statuses = [
                    'pending_payment' => [
                        'label' => 'Pending Payment',
                        'icon' => 'bi-wallet2'
                    ],

                    'waiting_verification' => [
                        'label' => 'Verification',
                        'icon' => 'bi-hourglass-split'
                    ],

                    'processing' => [
                        'label' => 'Processing',
                        'icon' => 'bi-arrow-repeat'
                    ],

                    'packing' => [
                        'label' => 'Packing',
                        'icon' => 'bi-box2'
                    ],

                    'shipped' => [
                        'label' => 'Shipped',
                        'icon' => 'bi-truck'
                    ],

                    'completed' => [
                        'label' => 'Completed',
                        'icon' => 'bi-check-circle'
                    ],

                    'cancelled' => [
                        'label' => 'Cancelled',
                        'icon' => 'bi-x-circle'
                    ],
                ];
            @endphp


            <div class="status-grid">

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


        {{-- CONTENT GRID --}}
        <div class="dashboard-grid">


            {{-- RECENT ORDERS --}}
            <section class="dashboard-card">

                <div class="card-header">

                    <div>

                        <h2>
                            Recent Orders
                        </h2>

                        <p>
                            Latest transactions from customers.
                        </p>

                    </div>


                    <a
                        href="{{ route('admin.orders.index') }}"
                        class="view-link"
                    >
                        View All
                        <i class="bi bi-arrow-right"></i>
                    </a>

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

                            <div class="order-info">

                                <strong>
                                    {{ $order->order_code }}
                                </strong>

                                <span>
                                    {{ $order->user?->name ?? '-' }}
                                </span>

                                <small>
                                    {{ $order->created_at->format(
                                        'd M Y, H:i'
                                    ) }}
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
                                    class="status-badge status-{{ $order->status }}"
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

                            <span>
                                Customer orders will appear here.
                            </span>

                        </div>

                    @endforelse

                </div>

            </section>


            {{-- PENDING PAYMENTS --}}
            <section class="dashboard-card">

                <div class="card-header">

                    <div>

                        <h2>
                            Pending Payments
                        </h2>

                        <p>
                            Payments waiting for verification.
                        </p>

                    </div>


                    <a
                        href="{{ route('admin.payments.index') }}"
                        class="view-link"
                    >
                        View All
                        <i class="bi bi-arrow-right"></i>
                    </a>

                </div>


                <div class="payment-list">

                    @forelse($recentPendingPayments as $payment)

                        <div class="payment-item">

                            <div class="payment-icon">
                                <i class="bi bi-credit-card"></i>
                            </div>


                            <div class="payment-info">

                                <strong>
                                    {{ $payment->order?->order_code ?? '-' }}
                                </strong>

                                <span>
                                    {{ $payment->order?->user?->name ?? '-' }}
                                </span>

                                <small>
                                    {{ ucfirst(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $payment->payment_method
                                        )
                                    ) }}
                                </small>

                            </div>


                            <div class="payment-amount">

                                <strong>
                                    Rp {{ number_format(
                                        $payment->amount,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </strong>

                                <span>
                                    PENDING
                                </span>

                            </div>

                        </div>

                    @empty

                        <div class="empty-state">

                            <i class="bi bi-check-circle"></i>

                            <strong>
                                No pending payments
                            </strong>

                            <span>
                                All submitted payments have been reviewed.
                            </span>

                        </div>

                    @endforelse

                </div>

            </section>

        </div>


    </main>

</div>

</body>

</html>