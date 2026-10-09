<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Payment Verification</title>

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
                <h2>LIFESTYLE</h2>
                <span>ADMIN PANEL</span>
            </div>

            <nav class="sidebar-menu">

                <a href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-grid"></i>
                    Dashboard
                </a>

                <a href="{{ route('admin.orders.index') }}">
                    <i class="bi bi-bag-check"></i>
                    Orders
                </a>

                <a
                    href="{{ route('admin.payments.index') }}"
                    class="active">
                    <i class="bi bi-credit-card"></i>
                    Payments
                </a>

            </nav>

            <div class="sidebar-footer">

                <div class="admin-user">

                    <div class="admin-avatar">
                        {{ strtoupper(
                        substr(auth()->user()->name, 0, 1)
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
        MAIN
    ========================= --}}

        <main class="admin-main">

            {{-- HEADER --}}

            <div class="admin-header">

                <div>
                    <span class="page-label">
                        ADMIN
                    </span>

                    <h1>
                        Payment Verification
                    </h1>

                    <p>
                        Review and verify customer payment proofs.
                    </p>
                </div>

                <div class="header-icon">
                    <i class="bi bi-credit-card"></i>
                </div>

            </div>


            {{-- =========================
            ALERT
        ========================= --}}

            @if (session('success'))

            <div class="alert-box alert-success">

                <i class="bi bi-check-circle"></i>

                <span>
                    {{ session('success') }}
                </span>

            </div>

            @endif


            @if (session('error'))

            <div class="alert-box alert-error">

                <i class="bi bi-exclamation-circle"></i>

                <span>
                    {{ session('error') }}
                </span>

            </div>

            @endif


            @if ($errors->any())

            <div class="alert-box alert-error">

                <i class="bi bi-exclamation-circle"></i>

                <div>
                    @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                    @endforeach
                </div>

            </div>

            @endif


            {{-- =========================
            FILTER
        ========================= --}}

            <section class="filter-card">

                <form
                    action="{{ route('admin.payments.index') }}"
                    method="GET"
                    class="order-filter">

                    <div class="search-box">

                        <i class="bi bi-search"></i>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search order or customer...">

                    </div>


                    <select name="status">

                        <option value="">
                            All Payment Status
                        </option>

                        <option
                            value="pending"
                            {{ request('status') === 'pending'
                            ? 'selected'
                            : ''
                        }}>
                            Pending
                        </option>

                        <option
                            value="verified"
                            {{ request('status') === 'verified'
                            ? 'selected'
                            : ''
                        }}>
                            Verified
                        </option>

                        <option
                            value="rejected"
                            {{ request('status') === 'rejected'
                            ? 'selected'
                            : ''
                        }}>
                            Rejected
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
                        href="{{ route('admin.payments.index') }}"
                        class="reset-button">
                        RESET
                    </a>

                    @endif

                </form>

            </section>


            {{-- =========================
            PAYMENT LIST
        ========================= --}}

            <section class="order-card">

                <div class="order-card-header">

                    <div>
                        <h2>
                            Payments
                        </h2>

                        <p>
                            {{ $payments->total() }}
                            total payment records
                        </p>
                    </div>

                </div>


                <div class="payment-list">

                    @forelse ($payments as $payment)

                    @php

                    $paymentLabel = match ($payment->status) {

                    'pending'
                    => 'Pending',

                    'verified'
                    => 'Verified',

                    'rejected'
                    => 'Rejected',

                    default
                    => ucfirst($payment->status),
                    };

                    @endphp


                    <article class="payment-card">

                        {{-- TOP --}}

                        <div class="payment-card-top">

                            <div>

                                <span class="payment-order-label">
                                    ORDER
                                </span>

                                <a
                                    href="{{ route(
                                        'admin.orders.show',
                                        $payment->order
                                    ) }}"
                                    class="payment-order-code">
                                    {{ $payment->order->order_code }}
                                </a>

                                <span class="payment-customer">
                                    {{ $payment->order->user?->name ?? '-' }}

                                    ·

                                    {{ $payment->order->user?->email ?? '-' }}
                                </span>

                            </div>


                            <span
                                class="
                                    payment-badge
                                    payment-{{ $payment->status }}
                                    payment-large-badge
                                ">
                                {{ $paymentLabel }}
                            </span>

                        </div>


                        {{-- CONTENT --}}

                        <div class="payment-card-content">


                            {{-- PROOF --}}

                            <div class="payment-proof-area">

                                <span class="payment-section-label">
                                    PAYMENT PROOF
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
                                    src="{{ asset(
                                            $payment->proof_image
                                        ) }}"
                                    alt="Payment proof"
                                    class="payment-proof-image">

                                @else

                                <div class="payment-proof-placeholder">

                                    <i class="bi bi-image"></i>

                                    <strong>
                                        Payment Proof
                                    </strong>

                                    <span>
                                        {{ $payment->proof_image
                                                ? basename($payment->proof_image)
                                                : 'No proof uploaded'
                                            }}
                                    </span>

                                    @if ($payment->proof_image)

                                    <small>
                                        Demo image file
                                    </small>

                                    @endif

                                </div>

                                @endif

                            </div>


                            {{-- INFO --}}

                            <div class="payment-information">

                                <span class="payment-section-label">
                                    PAYMENT INFORMATION
                                </span>


                                <div class="payment-info-grid">

                                    <div>
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


                                    <div>
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


                                    <div>
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


                                    <div>
                                        <span>
                                            Order Status
                                        </span>

                                        <strong>
                                            {{ ucwords(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $payment->order->status
                                                )
                                            ) }}
                                        </strong>
                                    </div>


                                    @if ($payment->verified_by)

                                    <div>
                                        <span>
                                            Processed By
                                        </span>

                                        <strong>
                                            {{ $payment->verifier?->name
                                                    ?? '-'
                                                }}
                                        </strong>
                                    </div>

                                    @endif


                                    @if ($payment->verified_at)

                                    <div>
                                        <span>
                                            Processed At
                                        </span>

                                        <strong>
                                            {{ $payment->verified_at
                                                    ?->format(
                                                        'd M Y, H:i'
                                                    )
                                                }}
                                        </strong>
                                    </div>

                                    @endif

                                </div>


                                {{-- REJECTION REASON --}}

                                @if (
                                $payment->status === 'rejected'
                                && $payment->rejection_reason
                                )

                                <div class="rejection-box">

                                    <strong>
                                        Rejection Reason
                                    </strong>

                                    <p>
                                        {{ $payment->rejection_reason }}
                                    </p>

                                </div>

                                @endif


                                {{-- ACTION --}}

                                @if ($payment->status === 'pending')

                                <div class="payment-actions">


                                    {{-- VERIFY --}}

                                    <form
                                        action="{{ route(
                                                'admin.payments.verify',
                                                $payment
                                            ) }}"
                                        method="POST"
                                        onsubmit="
                                                return confirm(
                                                    'Verify this payment?'
                                                );
                                            ">

                                        @csrf

                                        <button
                                            type="submit"
                                            class="verify-button">

                                            <i class="bi bi-check-lg"></i>

                                            VERIFY PAYMENT

                                        </button>

                                    </form>


                                    {{-- REJECT --}}

                                    <button
                                        type="button"
                                        class="reject-toggle-button"
                                        data-payment-id="{{ $payment->id }}"
                                        onclick="toggleRejectForm(this.dataset.paymentId)">
                                        <i class="bi bi-x-lg"></i>

                                        REJECT
                                    </button>

                                </div>


                                {{-- REJECT FORM --}}

                                <div
                                    class="reject-form"
                                    id="reject-form-{{ $payment->id }}">

                                    <form
                                        action="{{ route(
                                                'admin.payments.reject',
                                                $payment
                                            ) }}"
                                        method="POST">

                                        @csrf


                                        <label>
                                            Rejection Reason
                                        </label>


                                        <textarea
                                            name="rejection_reason"
                                            rows="4"
                                            maxlength="500"
                                            required
                                            placeholder="Explain why this payment is rejected..."></textarea>


                                        <div class="reject-form-actions">

                                            <button
                                                type="button"
                                                class="cancel-reject-button"
                                                data-payment-id="{{ $payment->id }}"
                                                onclick="toggleRejectForm(this.dataset.paymentId)">
                                                CANCEL
                                            </button>


                                            <button
                                                type="submit"
                                                class="confirm-reject-button">
                                                CONFIRM REJECT
                                            </button>

                                        </div>

                                    </form>

                                </div>

                                @else

                                <div class="processed-payment">

                                    @if ($payment->status === 'verified')

                                    <i class="bi bi-check-circle"></i>

                                    Payment has been verified.

                                    @else

                                    <i class="bi bi-x-circle"></i>

                                    Payment has been rejected.

                                    @endif

                                </div>

                                @endif

                            </div>

                        </div>

                    </article>

                    @empty

                    <div class="payment-empty">

                        <i class="bi bi-credit-card"></i>

                        <h3>
                            No payments found
                        </h3>

                        <p>
                            Payment records will appear here.
                        </p>

                    </div>

                    @endforelse

                </div>


                {{-- PAGINATION --}}

                @if ($payments->hasPages())

                <div class="admin-pagination">

                    @if ($payments->onFirstPage())

                    <span class="disabled">
                        <i class="bi bi-arrow-left"></i>
                        Previous
                    </span>

                    @else

                    <a href="{{ $payments->previousPageUrl() }}">
                        <i class="bi bi-arrow-left"></i>
                        Previous
                    </a>

                    @endif


                    <span>
                        Page
                        {{ $payments->currentPage() }}
                        of
                        {{ $payments->lastPage() }}
                    </span>


                    @if ($payments->hasMorePages())

                    <a href="{{ $payments->nextPageUrl() }}">
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


    <script>
        function toggleRejectForm(paymentId) {

            const form = document.getElementById(
                'reject-form-' + paymentId
            );

            if (!form) {
                return;
            }

            form.classList.toggle('show');
        }
    </script>

</body>

</html>