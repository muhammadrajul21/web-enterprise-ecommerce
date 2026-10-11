@extends('layouts.store')

@section('title', 'Payment')

@section('content')

<section class="payment-page">

    <div class="payment-container">

        {{-- HEADER --}}
        <div class="payment-header">

            <span class="payment-eyebrow">
                PAYMENT
            </span>

            <h1>
                Complete Your Payment
            </h1>

            <p>
                Order {{ $order->order_code }}
            </p>

        </div>


        {{-- FLASH MESSAGE --}}
        @if(session('success'))

            <div class="payment-alert payment-alert-success">

                <i class="bi bi-check-circle"></i>

                <span>
                    {{ session('success') }}
                </span>

            </div>

        @endif


        @if(session('error'))

            <div class="payment-alert payment-alert-error">

                <i class="bi bi-exclamation-circle"></i>

                <span>
                    {{ session('error') }}
                </span>

            </div>

        @endif


        @if($errors->any())

            <div class="payment-alert payment-alert-error">

                <i class="bi bi-exclamation-circle"></i>

                <span>
                    {{ $errors->first() }}
                </span>

            </div>

        @endif


        <div class="payment-layout">


            {{-- =========================
                PAYMENT CONTENT
            ========================= --}}

            <div class="payment-main">


                {{-- ORDER STATUS --}}
                <section class="payment-card">

                    <div class="payment-card-title">

                        <div class="payment-card-icon">
                            <i class="bi bi-receipt"></i>
                        </div>

                        <div>

                            <h2>
                                Order Status
                            </h2>

                            <p>
                                Current payment status for this order.
                            </p>

                        </div>

                    </div>


                    <div class="payment-status-box">

                        <span>
                            ORDER
                        </span>

                        <strong>
                            {{ $order->order_code }}
                        </strong>

                    </div>


                    <div class="payment-status-box">

                        <span>
                            STATUS
                        </span>

                        <strong>

                            @switch($order->status)

                                @case('pending_payment')
                                    Waiting for Payment
                                    @break

                                @case('waiting_verification')
                                    Waiting for Verification
                                    @break

                                @case('processing')
                                    Payment Verified
                                    @break

                                @case('packing')
                                    Packing
                                    @break

                                @case('shipped')
                                    Shipped
                                    @break

                                @case('completed')
                                    Completed
                                    @break

                                @case('cancelled')
                                    Cancelled
                                    @break

                                @default
                                    {{ ucwords(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $order->status
                                        )
                                    ) }}

                            @endswitch

                        </strong>

                    </div>

                </section>


                {{-- =========================
                    UPLOAD PAYMENT
                ========================= --}}

                @if($order->status === 'pending_payment')

                    <section class="payment-card">

                        <div class="payment-card-title">

                            <div class="payment-card-icon">
                                <i class="bi bi-credit-card"></i>
                            </div>

                            <div>

                                <h2>
                                    Payment Method
                                </h2>

                                <p>
                                    Choose your payment method
                                    and upload the payment proof.
                                </p>

                            </div>

                        </div>


                        <form
                            action="{{ route(
                                'payment.store',
                                $order
                            ) }}"
                            method="POST"
                            enctype="multipart/form-data"
                            class="payment-form"
                        >

                            @csrf


                            {{-- PAYMENT METHOD --}}

                            <div class="payment-methods">

                                <label class="payment-method-option">

                                    <input
                                        type="radio"
                                        name="payment_method"
                                        value="bank_transfer"
                                        {{ old('payment_method', 'bank_transfer') === 'bank_transfer'
                                            ? 'checked'
                                            : '' }}
                                    >

                                    <span class="payment-method-radio"></span>

                                    <span class="payment-method-icon">
                                        <i class="bi bi-bank"></i>
                                    </span>

                                    <span class="payment-method-info">

                                        <strong>
                                            Bank Transfer
                                        </strong>

                                        <small>
                                            Transfer manually through your bank.
                                        </small>

                                    </span>

                                </label>


                                <label class="payment-method-option">

                                    <input
                                        type="radio"
                                        name="payment_method"
                                        value="qris"
                                        {{ old('payment_method') === 'qris'
                                            ? 'checked'
                                            : '' }}
                                    >

                                    <span class="payment-method-radio"></span>

                                    <span class="payment-method-icon">
                                        <i class="bi bi-qr-code"></i>
                                    </span>

                                    <span class="payment-method-info">

                                        <strong>
                                            QRIS
                                        </strong>

                                        <small>
                                            Pay using a QRIS supported application.
                                        </small>

                                    </span>

                                </label>


                                <label class="payment-method-option">

                                    <input
                                        type="radio"
                                        name="payment_method"
                                        value="e_wallet"
                                        {{ old('payment_method') === 'e_wallet'
                                            ? 'checked'
                                            : '' }}
                                    >

                                    <span class="payment-method-radio"></span>

                                    <span class="payment-method-icon">
                                        <i class="bi bi-wallet2"></i>
                                    </span>

                                    <span class="payment-method-info">

                                        <strong>
                                            E-Wallet
                                        </strong>

                                        <small>
                                            Pay using your preferred digital wallet.
                                        </small>

                                    </span>

                                </label>

                            </div>


                            {{-- PAYMENT INSTRUCTION --}}

                            <div class="payment-instruction">

                                <i class="bi bi-info-circle"></i>

                                <div>

                                    <strong>
                                        Payment Instruction
                                    </strong>

                                    <p>
                                        Pay exactly
                                        <b>
                                            Rp {{ number_format(
                                                $order->total_amount,
                                                0,
                                                ',',
                                                '.'
                                            ) }}
                                        </b>
                                        and upload a clear image of
                                        your payment proof below.
                                    </p>

                                </div>

                            </div>


                            {{-- UPLOAD PROOF --}}

                            <div class="payment-upload">

                                <label for="proof_image">

                                    PAYMENT PROOF

                                    <span>
                                        JPG, JPEG, PNG or WEBP — max 5 MB
                                    </span>

                                </label>


                                <input
                                    type="file"
                                    name="proof_image"
                                    id="proof_image"
                                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                    required
                                >

                            </div>


                            <button
                                type="submit"
                                class="submit-payment-button"
                            >

                                SUBMIT PAYMENT

                                <i class="bi bi-arrow-right"></i>

                            </button>

                        </form>

                    </section>

                @endif


                {{-- =========================
                    PAYMENT ALREADY SUBMITTED
                ========================= --}}

                @if($latestPayment)

                    <section class="payment-card">

                        <div class="payment-card-title">

                            <div class="payment-card-icon">

                                @if($latestPayment->status === 'verified')

                                    <i class="bi bi-check-circle"></i>

                                @elseif($latestPayment->status === 'rejected')

                                    <i class="bi bi-x-circle"></i>

                                @else

                                    <i class="bi bi-clock"></i>

                                @endif

                            </div>

                            <div>

                                <h2>
                                    Payment Submission
                                </h2>

                                <p>
                                    Payment information submitted
                                    for this order.
                                </p>

                            </div>

                        </div>


                        <div class="submitted-payment-grid">

                            <div>

                                <span>
                                    METHOD
                                </span>

                                <strong>
                                    {{ ucwords(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $latestPayment->payment_method
                                        )
                                    ) }}
                                </strong>

                            </div>


                            <div>

                                <span>
                                    AMOUNT
                                </span>

                                <strong>

                                    Rp {{ number_format(
                                        $latestPayment->amount,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </strong>

                            </div>


                            <div>

                                <span>
                                    STATUS
                                </span>

                                <strong>
                                    {{ strtoupper(
                                        $latestPayment->status
                                    ) }}
                                </strong>

                            </div>


                            <div>

                                <span>
                                    SUBMITTED AT
                                </span>

                                <strong>

                                    {{ $latestPayment->paid_at
                                        ? $latestPayment->paid_at
                                            ->format('d M Y H:i')
                                        : '-'
                                    }}

                                </strong>

                            </div>

                        </div>


                        {{-- PAYMENT PROOF --}}

                        <div class="customer-payment-proof">

                            <span>
                                PAYMENT PROOF
                            </span>


                            @if(
                                $latestPayment->proof_image
                                && file_exists(
                                    public_path(
                                        $latestPayment->proof_image
                                    )
                                )
                            )

                                <img
                                    src="{{ asset(
                                        $latestPayment->proof_image
                                    ) }}"
                                    alt="Payment proof"
                                >

                            @else

                                <div class="payment-proof-placeholder">

                                    <i class="bi bi-image"></i>

                                    <span>
                                        Payment proof unavailable
                                    </span>

                                </div>

                            @endif

                        </div>


                        @if(
                            $latestPayment->status === 'rejected'
                            && $latestPayment->rejection_reason
                        )

                            <div class="payment-rejection">

                                <strong>
                                    Rejection Reason
                                </strong>

                                <p>
                                    {{ $latestPayment->rejection_reason }}
                                </p>

                            </div>

                        @endif

                    </section>

                @endif

            </div>


            {{-- =========================
                ORDER SUMMARY
            ========================= --}}

            <aside class="payment-summary">

                <div class="payment-summary-card">

                    <h2>
                        Order Summary
                    </h2>


                    <div class="payment-order-items">

                        @foreach($order->items as $item)

                            <div class="payment-order-item">

                                <div>

                                    <strong>
                                        {{ $item->product_name }}
                                    </strong>

                                    <span>

                                        {{ $item->color ?: '-' }}

                                        /

                                        {{ $item->size ?: '-' }}

                                        · Qty {{ $item->quantity }}

                                    </span>

                                </div>

                                <strong>

                                    Rp {{ number_format(
                                        $item->subtotal,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </strong>

                            </div>

                        @endforeach

                    </div>


                    <div class="payment-divider"></div>


                    <div class="payment-summary-row">

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


                    <div class="payment-summary-row">

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


                    <div class="payment-summary-row">

                        <span>
                            Shipping
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


                    <div class="payment-divider"></div>


                    <div class="payment-total">

                        <span>
                            TOTAL PAYMENT
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


                    <div class="payment-shipping-info">

                        <span>
                            SHIPPING TO
                        </span>

                        <strong>
                            {{ $order->recipient_name }}
                        </strong>

                        <p>
                            {{ $order->shipping_address }},
                            {{ $order->shipping_city }},
                            {{ $order->shipping_province }}
                            {{ $order->shipping_postal_code }}
                        </p>

                    </div>


                    <a
                        href="{{ route('catalog.preview') }}"
                        class="payment-back-store"
                    >

                        <i class="bi bi-arrow-left"></i>

                        Continue Shopping

                    </a>

                </div>

            </aside>

        </div>

    </div>

</section>

@endsection