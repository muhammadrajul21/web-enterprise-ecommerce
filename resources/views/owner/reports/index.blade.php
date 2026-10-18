<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Sales & Order Report</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link
        rel="stylesheet"
        href="{{ asset('css/owner/dashboard.css') }}">

    <link
        rel="stylesheet"
        href="{{ asset('css/owner/reports.css') }}">
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
                    class="{{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid"></i>
                    Dashboard
                </a>

                <a
                    href="{{ route('owner.reports.index') }}"
                    class="{{ request()->routeIs('owner.reports.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                    Reports
                </a>

                <a
                    href="{{ route('owner.operations.index') }}"
                    class="{{ request()->routeIs('owner.operations.*') ? 'active' : '' }}">
                    <i class="bi bi-activity"></i>
                    Operations
                </a>

            </nav>

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

        <main class="owner-main">

            {{-- HEADER --}}

            <div class="page-header">

                <div>

                    <span class="page-label">
                        BUSINESS REPORT
                    </span>

                    <h1>
                        Sales & Order Report
                    </h1>

                    <p>
                        Analyze sales performance and customer orders.
                    </p>

                </div>


                <div class="header-icon">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                </div>

            </div>


            {{-- VALIDATION --}}

            @if($errors->any())

            <div class="report-alert">

                <i class="bi bi-exclamation-triangle"></i>

                <span>
                    {{ $errors->first() }}
                </span>

            </div>

            @endif


            {{-- =========================
            FILTER
        ========================= --}}

            <section class="report-filter-card">

                <div class="filter-heading">

                    <div>

                        <h2>
                            Filter Report
                        </h2>

                        <p>
                            Filter orders by date range and status.
                        </p>

                    </div>

                    <i class="bi bi-funnel"></i>

                </div>


                <form
                    action="{{ route('owner.reports.index') }}"
                    method="GET"
                    class="report-filter-form">

                    <div class="filter-field">

                        <label for="start_date">
                            START DATE
                        </label>

                        <input
                            type="date"
                            id="start_date"
                            name="start_date"
                            value="{{ $startDate }}">

                    </div>


                    <div class="filter-field">

                        <label for="end_date">
                            END DATE
                        </label>

                        <input
                            type="date"
                            id="end_date"
                            name="end_date"
                            value="{{ $endDate }}">

                    </div>


                    <div class="filter-field">

                        <label for="status">
                            ORDER STATUS
                        </label>

                        <select
                            id="status"
                            name="status">

                            <option value="">
                                All Statuses
                            </option>

                            <option
                                value="pending_payment"
                                @selected($status==='pending_payment' )>
                                Pending Payment
                            </option>

                            <option
                                value="waiting_verification"
                                @selected($status==='waiting_verification' )>
                                Waiting Verification
                            </option>

                            <option
                                value="processing"
                                @selected($status==='processing' )>
                                Processing
                            </option>

                            <option
                                value="packing"
                                @selected($status==='packing' )>
                                Packing
                            </option>

                            <option
                                value="shipped"
                                @selected($status==='shipped' )>
                                Shipped
                            </option>

                            <option
                                value="completed"
                                @selected($status==='completed' )>
                                Completed
                            </option>

                            <option
                                value="cancelled"
                                @selected($status==='cancelled' )>
                                Cancelled
                            </option>

                        </select>

                    </div>


                    <div class="filter-actions">

                        <button
                            type="submit"
                            class="filter-button">
                            APPLY FILTER

                            <i class="bi bi-arrow-right"></i>
                        </button>

                        <a
                            href="{{ route('owner.reports.index') }}"
                            class="reset-button">
                            RESET
                        </a>
                        <a
                            href="{{ route('owner.reports.print', array_filter([
        'start_date' => $startDate,
        'end_date' => $endDate,
        'status' => $status,
    ])) }}"
                            target="_blank"
                            class="print-report-button">
                            <i class="bi bi-printer"></i>
                            PRINT REPORT
                        </a>

                    </div>

                </form>

            </section>


            {{-- =========================
            REPORT SUMMARY
        ========================= --}}

            <section class="stats-grid report-stats">

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
                        <i class="bi bi-bag"></i>
                    </div>

                    <div class="stat-content">

                        <span>
                            TOTAL ORDERS
                        </span>

                        <strong>
                            {{ $totalOrders }}
                        </strong>

                        <p>
                            Orders in this report
                        </p>

                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-icon">
                        <i class="bi bi-check-circle"></i>
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
                        <i class="bi bi-x-circle"></i>
                    </div>

                    <div class="stat-content">

                        <span>
                            CANCELLED
                        </span>

                        <strong>
                            {{ $cancelledOrders }}
                        </strong>

                        <p>
                            Cancelled orders
                        </p>

                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-icon">
                        <i class="bi bi-calculator"></i>
                    </div>

                    <div class="stat-content">

                        <span>
                            AVG. ORDER VALUE
                        </span>

                        <strong class="revenue-value">
                            Rp {{ number_format(
                            $averageOrderValue,
                            0,
                            ',',
                            '.'
                        ) }}
                        </strong>

                        <p>
                            Average revenue per order
                        </p>

                    </div>

                </div>

            </section>


            {{-- =========================
            ORDER REPORT TABLE
        ========================= --}}

            <section class="dashboard-card report-table-card">

                <div class="card-header">

                    <div>

                        <h2>
                            Order Report
                        </h2>

                        <p>
                            Detailed transaction records.
                        </p>

                    </div>

                    <i class="bi bi-table"></i>

                </div>


                <div class="report-table-wrapper">

                    <table class="report-table">

                        <thead>

                            <tr>
                                <th>ORDER</th>
                                <th>CUSTOMER</th>
                                <th>DATE</th>
                                <th>TOTAL</th>
                                <th>PAYMENT</th>
                                <th>STATUS</th>
                            </tr>

                        </thead>


                        <tbody>

                            @forelse($orders as $order)

                            @php
                            $latestPayment =
                            $order->payments->first();

                            $statusLabel = [
                            'pending_payment' =>
                            'Pending Payment',

                            'waiting_verification' =>
                            'Verification',

                            'processing' =>
                            'Processing',

                            'packing' =>
                            'Packing',

                            'shipped' =>
                            'Shipped',

                            'completed' =>
                            'Completed',

                            'cancelled' =>
                            'Cancelled',
                            ][$order->status]
                            ?? ucwords(
                            str_replace(
                            '_',
                            ' ',
                            $order->status
                            )
                            );
                            @endphp


                            <tr>

                                <td>

                                    <strong class="order-code">
                                        {{ $order->order_code }}
                                    </strong>

                                </td>


                                <td>

                                    <strong>
                                        {{ $order->user?->name ?? '-' }}
                                    </strong>

                                    <span class="table-subtext">
                                        {{ $order->user?->email ?? '-' }}
                                    </span>

                                </td>


                                <td>

                                    {{ $order->created_at
                                    ->format('d M Y') }}

                                    <span class="table-subtext">
                                        {{ $order->created_at
                                        ->format('H:i') }}
                                    </span>

                                </td>


                                <td>

                                    <strong>
                                        Rp {{ number_format(
                                        $order->total_amount,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                    </strong>

                                </td>


                                <td>

                                    @if($latestPayment)

                                    <span
                                        class="
                                            payment-badge
                                            payment-{{ $latestPayment->status }}
                                        ">
                                        {{ ucfirst(
                                            $latestPayment->status
                                        ) }}
                                    </span>

                                    @else

                                    <span class="payment-badge">
                                        No Payment
                                    </span>

                                    @endif

                                </td>


                                <td>

                                    <span
                                        class="
                                        status-badge
                                        status-{{ $order->status }}
                                    ">
                                        {{ $statusLabel }}
                                    </span>

                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="report-empty">

                                    <i class="bi bi-inbox"></i>

                                    <strong>
                                        No orders found
                                    </strong>

                                    <span>
                                        Try changing the report filters.
                                    </span>

                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                @if($orders->hasPages())

                <div class="report-pagination">

                    {{ $orders->links() }}

                </div>

                @endif

            </section>

        </main>

    </div>

</body>

</html>