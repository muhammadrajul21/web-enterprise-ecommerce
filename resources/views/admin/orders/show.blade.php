<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Order {{ $order->order_code }}
    </title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link
        rel="stylesheet"
        href="{{ asset('css/admin/orders.css') }}">
</head>


<body>

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


    $paymentLabel = match ($payment?->status) {

    'verified'
    => 'Verified',

    'pending'
    => 'Pending',

    'rejected'
    => 'Rejected',

    default
    => 'Not Paid',
    };

    @endphp


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

                {{-- DASHBOARD --}}
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">

                    <i class="bi bi-grid"></i>

                    Dashboard

                </a>


                {{-- PRODUCTS --}}
                <a
                    href="{{ route('admin.products.index') }}"
                    class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">

                    <i class="bi bi-box-seam"></i>

                    Products

                </a>


                {{-- VARIANTS --}}
                <a
                    href="{{ route('admin.variants.index') }}"
                    class="{{ request()->routeIs('admin.variants.*') ? 'active' : '' }}">

                    <i class="bi bi-boxes"></i>

                    Variants

                </a>


                {{-- CATEGORIES --}}
                <a
                    href="{{ route('admin.categories.index') }}"
                    class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">

                    <i class="bi bi-tags"></i>

                    Categories

                </a>


                {{-- COLLECTIONS --}}
                <a
                    href="{{ route('admin.collections.index') }}"
                    class="{{ request()->routeIs('admin.collections.*') ? 'active' : '' }}">

                    <i class="bi bi-collection"></i>

                    Collections

                </a>


                {{-- VOUCHERS --}}
                <a
                    href="{{ route('admin.vouchers.index') }}"
                    class="{{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}">

                    <i class="bi bi-ticket-perforated"></i>

                    Vouchers

                </a>


                {{-- ORDERS --}}
                <a
                    href="{{ route('admin.orders.index') }}"
                    class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">

                    <i class="bi bi-bag-check"></i>

                    Orders

                </a>


                {{-- PAYMENTS --}}
                <a
                    href="{{ route('admin.payments.index') }}"
                    class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">

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


            {{-- BACK BUTTON --}}

            <a
                href="{{ route('admin.orders.index') }}"
                class="back-link">

                <i class="bi bi-arrow-left"></i>

                Back to Orders

            </a>



            {{-- =========================
            HEADER
        ========================= --}}

            <div class="detail-header">

                <div>

                    <span class="page-label">
                        ORDER DETAIL
                    </span>

                    <h1>
                        {{ $order->order_code }}
                    </h1>

                    <p>

                        Created on

                        {{ $order->created_at
                        ?->format('d M Y, H:i')
                    }}

                    </p>

                </div>


                <span
                    class="
                    status-badge
                    status-{{ $order->status }}
                    detail-status
                ">

                    {{ $statusLabel }}

                </span>

            </div>



            {{-- =========================
            ORDER INFORMATION
        ========================= --}}

            <div class="detail-grid">


                {{-- CUSTOMER --}}

                <section class="detail-card">

                    <div class="detail-card-title">

                        <i class="bi bi-person"></i>

                        <h2>
                            Customer
                        </h2>

                    </div>


                    <div class="info-list">

                        <div class="info-row">

                            <span>
                                Name
                            </span>

                            <strong>
                                {{ $order->user?->name ?? '-' }}
                            </strong>

                        </div>


                        <div class="info-row">

                            <span>
                                Email
                            </span>

                            <strong>
                                {{ $order->user?->email ?? '-' }}
                            </strong>

                        </div>


                        <div class="info-row">

                            <span>
                                Recipient
                            </span>

                            <strong>
                                {{ $order->recipient_name }}
                            </strong>

                        </div>


                        <div class="info-row">

                            <span>
                                Phone
                            </span>

                            <strong>
                                {{ $order->recipient_phone }}
                            </strong>

                        </div>

                    </div>

                </section>



                {{-- SHIPPING --}}

                <section class="detail-card">

                    <div class="detail-card-title">

                        <i class="bi bi-truck"></i>

                        <h2>
                            Shipping
                        </h2>

                    </div>


                    <div class="shipping-address">

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


                    <div class="shipping-meta">

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

                            <strong>

                                {{ $order->tracking_number ?? '-' }}

                            </strong>

                        </div>

                    </div>

                </section>

            </div>



            {{-- =========================
            ORDER ITEMS
        ========================= --}}

            <section class="detail-card detail-card-full">

                <div class="detail-card-title">

                    <i class="bi bi-bag"></i>

                    <h2>
                        Order Items
                    </h2>

                </div>


                <div class="table-wrapper">

                    <table class="order-table item-table">

                        <thead>

                            <tr>

                                <th>
                                    PRODUCT
                                </th>

                                <th>
                                    SKU
                                </th>

                                <th>
                                    VARIANT
                                </th>

                                <th>
                                    PRICE
                                </th>

                                <th>
                                    QTY
                                </th>

                                <th>
                                    SUBTOTAL
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse ($order->items as $item)

                            <tr>

                                <td>

                                    <div class="item-product">

                                        <div class="item-icon">

                                            <i class="bi bi-box"></i>

                                        </div>


                                        <div>

                                            <strong>
                                                {{ $item->product_name }}
                                            </strong>

                                            <span>

                                                {{ $item->variant?->product?->name
                                                ?? 'Product'
                                            }}

                                            </span>

                                        </div>

                                    </div>

                                </td>


                                <td>
                                    {{ $item->sku ?? '-' }}
                                </td>


                                <td>

                                    <div class="variant-info">

                                        <span>

                                            Color:
                                            {{ $item->color ?? '-' }}

                                        </span>

                                        <span>

                                            Size:
                                            {{ $item->size ?? '-' }}

                                        </span>

                                    </div>

                                </td>


                                <td>

                                    Rp {{ number_format(
                                    $item->price,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                                </td>


                                <td>

                                    {{ $item->quantity }}

                                </td>


                                <td>

                                    <strong>

                                        Rp {{ number_format(
                                        $item->subtotal,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                    </strong>

                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="empty-order">

                                    No order items found.

                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </section>



            {{-- =========================
            PAYMENT + SUMMARY
        ========================= --}}

            <div class="detail-grid detail-bottom">


                {{-- PAYMENT --}}

                <section class="detail-card">

                    <div class="detail-card-title">

                        <i class="bi bi-credit-card"></i>

                        <h2>
                            Payment
                        </h2>

                    </div>


                    @if ($payment)

                    <div class="info-list">

                        <div class="info-row">

                            <span>
                                Method
                            </span>

                            <strong>

                                {{ ucwords(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $payment->payment_method
                                    )
                                ) }}

                            </strong>

                        </div>


                        <div class="info-row">

                            <span>
                                Amount
                            </span>

                            <strong>

                                Rp {{ number_format(
                                    $payment->amount,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </strong>

                        </div>


                        <div class="info-row">

                            <span>
                                Status
                            </span>

                            <strong>

                                <span
                                    class="
                                        payment-badge
                                        payment-{{ $payment->status }}
                                    ">

                                    {{ $paymentLabel }}

                                </span>

                            </strong>

                        </div>


                        <div class="info-row">

                            <span>
                                Paid At
                            </span>

                            <strong>

                                {{ $payment->paid_at
                                    ?->format('d M Y, H:i')
                                    ?? '-'
                                }}

                            </strong>

                        </div>


                        <div class="info-row">

                            <span>
                                Verified By
                            </span>

                            <strong>

                                {{ $payment->verifier?->name
                                    ?? '-'
                                }}

                            </strong>

                        </div>


                        <div class="info-row">

                            <span>
                                Verified At
                            </span>

                            <strong>

                                {{ $payment->verified_at
                                    ?->format('d M Y, H:i')
                                    ?? '-'
                                }}

                            </strong>

                        </div>

                    </div>


                    @if ($payment->status === 'rejected')

                    <div class="rejection-box">

                        <strong>
                            Rejection Reason
                        </strong>

                        <p>

                            {{ $payment->rejection_reason
                                    ?? '-'
                                }}

                        </p>

                    </div>

                    @endif


                    <div class="proof-box">

                        <span>
                            Payment Proof
                        </span>


                        @if (
                        $payment->proof_image
                        &&
                        file_exists(
                        public_path(
                        $payment->proof_image
                        )
                        )
                        )

                        <img
                            src="{{ asset($payment->proof_image) }}"
                            alt="Payment proof">

                        @elseif ($payment->proof_image)

                        <div class="proof-placeholder">

                            <i class="bi bi-image"></i>

                            <span>
                                {{ basename(
                                        $payment->proof_image
                                    ) }}
                            </span>

                            <small>
                                Demo proof image
                            </small>

                        </div>

                        @else

                        <div class="proof-placeholder">

                            <i class="bi bi-image"></i>

                            <span>
                                No payment proof
                            </span>

                        </div>

                        @endif

                    </div>

                    @else

                    <div class="no-payment">

                        <i class="bi bi-wallet2"></i>

                        <strong>
                            Payment has not been submitted.
                        </strong>

                        <p>
                            Customer has not uploaded
                            payment proof yet.
                        </p>

                    </div>

                    @endif

                </section>



                {{-- SUMMARY --}}

                <section class="detail-card">

                    <div class="detail-card-title">

                        <i class="bi bi-receipt"></i>

                        <h2>
                            Order Summary
                        </h2>

                    </div>


                    <div class="summary-list">


                        <div>

                            <span>
                                Subtotal
                            </span>

                            <strong>

                                Rp {{ number_format(
                                $order->subtotal,
                                0,
                                ',',
                                '.'
                            ) }}

                            </strong>

                        </div>


                        <div>

                            <span>
                                Discount
                            </span>

                            <strong>

                                - Rp {{ number_format(
                                $order->discount_amount,
                                0,
                                ',',
                                '.'
                            ) }}

                            </strong>

                        </div>


                        <div>

                            <span>
                                Shipping Cost
                            </span>

                            <strong>

                                Rp {{ number_format(
                                $order->shipping_cost,
                                0,
                                ',',
                                '.'
                            ) }}

                            </strong>

                        </div>


                        <div class="summary-total">

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


                    @if ($order->cancelled_at)

                    <div class="cancel-box">

                        <strong>
                            Order Cancelled
                        </strong>

                        <p>
                            {{ $order->cancel_reason ?? '-' }}
                        </p>

                        <small>

                            {{ $order->cancelled_at
                                ?->format('d M Y, H:i')
                            }}

                        </small>

                    </div>

                    @endif

                </section>

            </div>


        </main>

    </div>

</body>

</html>