<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Order Management</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link
        rel="stylesheet"
        href="{{ asset('css/admin/orders.css') }}">

</head>


<body>


    <div class="admin-layout">


        {{-- =========================
        SIDEBAR
    ========================= --}}

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
                    href="{{ route('admin.orders.index') }}"
                    class="active">

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



        {{-- =========================
        MAIN CONTENT
    ========================= --}}

        <main class="admin-main">


            {{-- HEADER --}}

            <div class="admin-header">

                <div>

                    <span class="page-label">
                        ADMIN
                    </span>

                    <h1>
                        Order Management
                    </h1>

                    <p>
                        Monitor and manage customer orders.
                    </p>

                </div>


                <div class="header-icon">

                    <i class="bi bi-bag"></i>

                </div>

            </div>



            {{-- =========================
            FILTER
        ========================= --}}

            <section class="filter-card">

                <form
                    action="{{ route('admin.orders.index') }}"
                    method="GET"
                    class="order-filter">


                    {{-- SEARCH --}}

                    <div class="search-box">

                        <i class="bi bi-search"></i>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search order or customer...">

                    </div>


                    {{-- STATUS --}}

                    <select name="status">

                        <option value="">
                            All Status
                        </option>


                        <option
                            value="pending_payment"
                            {{ request('status') === 'pending_payment'
                            ? 'selected'
                            : ''
                        }}>
                            Pending Payment
                        </option>


                        <option
                            value="waiting_verification"
                            {{ request('status') === 'waiting_verification'
                            ? 'selected'
                            : ''
                        }}>
                            Waiting Verification
                        </option>


                        <option
                            value="processing"
                            {{ request('status') === 'processing'
                            ? 'selected'
                            : ''
                        }}>
                            Processing
                        </option>


                        <option
                            value="packing"
                            {{ request('status') === 'packing'
                            ? 'selected'
                            : ''
                        }}>
                            Packing
                        </option>


                        <option
                            value="shipped"
                            {{ request('status') === 'shipped'
                            ? 'selected'
                            : ''
                        }}>
                            Shipped
                        </option>


                        <option
                            value="completed"
                            {{ request('status') === 'completed'
                            ? 'selected'
                            : ''
                        }}>
                            Completed
                        </option>


                        <option
                            value="cancelled"
                            {{ request('status') === 'cancelled'
                            ? 'selected'
                            : ''
                        }}>
                            Cancelled
                        </option>

                    </select>


                    <button
                        type="submit"
                        class="filter-button">

                        FILTER

                    </button>


                    @if (
                    request('search')
                    || request('status')
                    )

                    <a
                        href="{{ route('admin.orders.index') }}"
                        class="reset-button">
                        RESET
                    </a>

                    @endif

                </form>

            </section>



            {{-- =========================
            ORDER TABLE
        ========================= --}}

            <section class="order-card">


                <div class="order-card-header">

                    <div>

                        <h2>
                            Orders
                        </h2>

                        <p>
                            {{ $orders->total() }}
                            total orders
                        </p>

                    </div>

                </div>



                <div class="table-wrapper">

                    <table class="order-table">


                        <thead>

                            <tr>

                                <th>
                                    ORDER
                                </th>

                                <th>
                                    CUSTOMER
                                </th>

                                <th>
                                    DATE
                                </th>

                                <th>
                                    TOTAL
                                </th>

                                <th>
                                    PAYMENT
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

                            @forelse ($orders as $order)

                            @php

                            $payment = $order
                            ->payments
                            ->sortByDesc('created_at')
                            ->first();


                            $statusLabel = match ($order->status) {

                            'pending_payment'
                            => 'Pending Payment',

                            'waiting_verification'
                            => 'Waiting Verification',

                            'processing'
                            => 'Processing',

                            'packing'
                            => 'Packing',

                            'shipped'
                            => 'Shipped',

                            'completed'
                            => 'Completed',

                            'cancelled'
                            => 'Cancelled',

                            default
                            => ucfirst($order->status),

                            };


                            $paymentLabel = match (
                            $payment?->status
                            ) {

                            'verified'
                            => 'Verified',

                            'rejected'
                            => 'Rejected',

                            'pending'
                            => 'Pending',

                            default
                            => 'Not Paid',

                            };

                            @endphp


                            <tr>


                                {{-- ORDER --}}

                                <td>

                                    <div class="order-code">

                                        <strong>
                                            {{ $order->order_code }}
                                        </strong>

                                        <span>
                                            #{{ $order->id }}
                                        </span>

                                    </div>

                                </td>


                                {{-- CUSTOMER --}}

                                <td>

                                    <div class="customer-info">

                                        <strong>
                                            {{ $order->user?->name ?? '-' }}
                                        </strong>

                                        <span>
                                            {{ $order->user?->email ?? '-' }}
                                        </span>

                                    </div>

                                </td>


                                {{-- DATE --}}

                                <td>

                                    {{ $order->created_at
                                    ?->format('d M Y')
                                }}

                                    <small>
                                        {{ $order->created_at
                                        ?->format('H:i')
                                    }}
                                    </small>

                                </td>


                                {{-- TOTAL --}}

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


                                {{-- PAYMENT --}}

                                <td>

                                    <span
                                        class="
                                        payment-badge
                                        payment-{{ $payment?->status ?? 'none' }}
                                    ">

                                        {{ $paymentLabel }}

                                    </span>

                                </td>


                                {{-- STATUS --}}

                                <td>

                                    <span
                                        class="
                                        status-badge
                                        status-{{ $order->status }}
                                    ">

                                        {{ $statusLabel }}

                                    </span>

                                </td>


                                {{-- ACTION --}}

                                <td>

                                    <a
                                        href="{{ route(
                                        'admin.orders.show',
                                        $order
                                    ) }}"
                                        class="detail-button"
                                        title="View order">

                                        <i class="bi bi-arrow-right"></i>

                                    </a>

                                </td>


                            </tr>


                            @empty


                            <tr>

                                <td
                                    colspan="7"
                                    class="empty-order">

                                    <i class="bi bi-bag-x"></i>

                                    <h3>
                                        No orders found
                                    </h3>

                                    <p>
                                        Order data will appear here
                                        when customers make purchases.
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

                @if ($orders->hasPages())

                <div class="admin-pagination">


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