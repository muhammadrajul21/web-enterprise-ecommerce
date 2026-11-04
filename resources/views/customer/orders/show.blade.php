@extends('layouts.store')

@section('title', 'Order Detail')

@section('content')

@php
$statusLabels = [
'pending_payment' => 'Pending Payment',
'waiting_verification' => 'Waiting Verification',
'processing' => 'Processing',
'packing' => 'Packing',
'shipped' => 'Shipped',
'completed' => 'Completed',
'cancelled' => 'Cancelled',
];

$statusLabel =
$statusLabels[$order->status]
?? ucwords(
str_replace(
'_',
' ',
$order->status
)
);

$steps = [
'pending_payment',
'waiting_verification',
'processing',
'packing',
'shipped',
'completed',
];

$stepLabels = [
'pending_payment' => 'Payment',
'waiting_verification' => 'Verification',
'processing' => 'Processing',
'packing' => 'Packing',
'shipped' => 'Shipped',
'completed' => 'Completed',
];

$currentIndex = array_search(
$order->status,
$steps,
true
);


$unreviewedItems = $order->items
->filter(fn ($item) => !$item->review)
->values();
@endphp


<section class="order-detail-page">

    <div class="order-detail-container">


        {{-- ================================================= --}}
        {{-- BACK --}}
        {{-- ================================================= --}}

        <a
            href="{{ route('orders.index') }}"
            class="order-detail-back">
            <i class="bi bi-arrow-left"></i>

            Back to My Orders
        </a>


        {{-- ================================================= --}}
        {{-- HEADER --}}
        {{-- ================================================= --}}

        <div class="order-detail-header">

            <div>

                <span class="order-detail-eyebrow">
                    ORDER DETAIL
                </span>

                <h1>
                    {{ $order->order_code }}
                </h1>

                <p>
                    Placed on
                    {{ $order->created_at->format('d M Y, H:i') }}
                </p>

            </div>


            <span
                class="
                    detail-status
                    detail-status-{{ $order->status }}
                ">
                {{ $statusLabel }}
            </span>

        </div>


        {{-- ================================================= --}}
        {{-- FLASH MESSAGE --}}
        {{-- ================================================= --}}

        @if(session('success'))

        <div class="detail-alert detail-alert-success">

            <i class="bi bi-check-circle"></i>

            {{ session('success') }}

        </div>

        @endif


        @if(session('error'))

        <div class="detail-alert detail-alert-error">

            <i class="bi bi-exclamation-circle"></i>

            {{ session('error') }}

        </div>

        @endif


        @if($errors->any())

        <div class="detail-alert detail-alert-error">

            <i class="bi bi-exclamation-triangle"></i>

            <div>

                @foreach($errors->all() as $error)

                <div>
                    {{ $error }}
                </div>

                @endforeach

            </div>

        </div>

        @endif

        {{-- REVIEW REMINDER --}}
        @if(
        $order->status === 'completed'
        && $unreviewedItems->isNotEmpty()
        )

        <div class="detail-alert detail-alert-success">

            <i class="bi bi-star"></i>

            <div>
                <strong>Pesanan selesai! Bagikan pengalamanmu.</strong>

                <p>
                    Kamu masih memiliki
                    {{ $unreviewedItems->count() }}
                    produk yang belum direview.
                </p>

                <a
                    href="#review-item-{{ $unreviewedItems->first()->id }}"
                    style="font-weight: 700;">
                    WRITE A REVIEW
                    <i class="bi bi-arrow-right"></i>
                </a>

            </div>

        </div>

        @endif



        {{-- ================================================= --}}
        {{-- CANCELLED --}}
        {{-- ================================================= --}}

        @if($order->status === 'cancelled')

        <div class="cancelled-box">

            <i class="bi bi-x-circle"></i>

            <div>

                <strong>
                    Order Cancelled
                </strong>

                <p>
                    {{ $order->cancel_reason
                            ?? 'This order has been cancelled.' }}
                </p>

            </div>

        </div>

        @else


        {{-- ================================================= --}}
        {{-- ORDER PROGRESS --}}
        {{-- ================================================= --}}

        <section class="detail-card">

            <div class="detail-card-heading">

                <div class="detail-heading-icon">
                    <i class="bi bi-truck"></i>
                </div>

                <div>

                    <h2>
                        Order Progress
                    </h2>

                    <p>
                        Track the current progress of your order.
                    </p>

                </div>

            </div>


            <div class="order-timeline">

                @foreach($steps as $index => $step)

                @php
                $isActive =
                $currentIndex !== false
                && $index <= $currentIndex;

                    $isCurrent=$currentIndex !==false
                    && $index===$currentIndex;
                    @endphp


                    <div
                    class="
                                timeline-step
                                {{ $isActive ? 'active' : '' }}
                                {{ $isCurrent ? 'current' : '' }}
                            ">

                    <div class="timeline-dot">

                        @if(
                        $currentIndex !== false
                        && $index < $currentIndex
                            )

                            <i class="bi bi-check"></i>

                            @else

                            {{ $index + 1 }}

                            @endif

                    </div>


                    <span>
                        {{ $stepLabels[$step] }}
                    </span>

            </div>

            @endforeach

    </div>

</section>


{{-- DELIVERY STATUS AND CUSTOMER ACTIONS --}}

@if(
$order->status === 'shipped'
|| (
$order->status === 'completed'
&& $order->completion_reason === 'auto_timeout'
)
)

<section class="detail-card">

    <div class="detail-card-heading">
        <div class="detail-heading-icon">
            <i class="bi bi-truck"></i>
        </div>

        <div>
            <h2>Delivery Information</h2>

            <p>
                Check your estimated delivery date and
                confirm when your package has arrived.
            </p>
        </div>
    </div>

    <div class="shipping-estimate-info">

        <p>
            <strong>Estimated Arrival</strong>
        </p>

        <p>
            @if($order->estimated_delivery_start && $order->estimated_delivery_end)
            {{ $order->estimated_delivery_start->format('d M Y') }}
            –
            {{ $order->estimated_delivery_end->format('d M Y') }}
            @else
            Estimated arrival date is not available.
            @endif
        </p>

        <p>
            Courier:
            <strong>{{ $order->courier ?? '-' }}</strong>
        </p>

        <p>
            Tracking Number:
            <strong>{{ $order->tracking_number ?? '-' }}</strong>
        </p>

    </div>

    @php
    $activeComplaint = $order->complaints
    ->whereIn('status', ['open', 'investigating'])
    ->sortByDesc('created_at')
    ->first();

    $latestComplaint = $order->complaints
    ->sortByDesc('created_at')
    ->first();
    @endphp

    {{-- STATUS KOMPLAIN TERAKHIR --}}
    @if($latestComplaint)

    <div class="complaint-status-card">

        <div class="complaint-status-heading">
            <i class="bi bi-chat-left-text"></i>
            <h3>Status Komplain</h3>
        </div>

        <div class="complaint-status-grid">

            <div class="complaint-status-item">
                <span>Status Laporan</span>

                <strong class="complaint-status-badge status-{{ $latestComplaint->status }}">
                    {{ ucfirst(str_replace('_', ' ', $latestComplaint->status)) }}
                </strong>
            </div>

            <div class="complaint-status-item">
                <span>Kategori</span>

                <strong>
                    {{ ucfirst(str_replace('_', ' ', $latestComplaint->category)) }}
                </strong>
            </div>

            <div class="complaint-status-item complaint-status-full">
                <span>Pesan Pelanggan</span>

                <p>{{ $latestComplaint->message }}</p>
            </div>

            <div class="complaint-status-item complaint-status-full">
                <span>Tanggapan Admin</span>

                @if($latestComplaint->admin_response)
                <p>{{ $latestComplaint->admin_response }}</p>
                @else
                <p class="complaint-no-response">
                    Admin belum memberikan tanggapan.
                </p>
                @endif
            </div>

            @if($activeComplaint)
            <div class="complaint-status-note complaint-status-full">
                <i class="bi bi-info-circle"></i>

                Komplain kamu masih diproses. Konfirmasi penerimaan
                dan pengajuan komplain baru dinonaktifkan sampai
                Admin menyelesaikan laporan ini.
            </div>
            @endif

        </div>

    </div>

    @endif


    {{-- CONFIRM RECEIPT --}}
    @if($order->status === 'shipped' && !$activeComplaint)

    <form
        action="{{ route('orders.confirm-received', $order) }}"
        method="POST"
        class="confirm-received-form"
        onsubmit="return confirm('Apakah kamu yakin pesanan sudah diterima? Setelah dikonfirmasi, pesanan akan selesai.');"
        style="margin-top: 24px;">

        @csrf

        <button
            type="submit"
            class="detail-primary-button">

            <i class="bi bi-check-circle"></i>

            CONFIRM RECEIPT

        </button>

    </form>

    @endif


</section>

@endif


<div class="order-detail-layout">


    {{-- ================================================= --}}
    {{-- LEFT CONTENT --}}
    {{-- ================================================= --}}

    <div class="order-detail-main">


        {{-- ================================================= --}}
        {{-- PRODUCTS --}}
        {{-- ================================================= --}}

        <section class="detail-card">

            <div class="detail-card-heading">

                <div class="detail-heading-icon">
                    <i class="bi bi-bag"></i>
                </div>

                <div>

                    <h2>
                        Products
                    </h2>

                    <p>
                        Items included in this order.
                    </p>

                </div>

            </div>


            <div class="detail-products">

                @foreach($order->items as $item)

                <div class="detail-product-wrapper">

                    <div class="detail-product">

                        <div class="detail-product-icon">
                            <i class="bi bi-box"></i>
                        </div>


                        <div class="detail-product-info">

                            <strong>
                                {{ $item->product_name }}
                            </strong>

                            <span>
                                SKU:
                                {{ $item->sku ?: '-' }}
                            </span>

                            <span>
                                {{ $item->color ?: '-' }}
                                /
                                {{ $item->size ?: '-' }}
                            </span>

                        </div>


                        <div class="detail-product-qty">

                            <span>
                                QTY
                            </span>

                            <strong>
                                {{ $item->quantity }}
                            </strong>

                        </div>


                        <div class="detail-product-price">

                            <span>
                                PRICE
                            </span>

                            <strong>
                                Rp {{ number_format(
                                                    $item->subtotal,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }}
                            </strong>

                        </div>

                    </div>


                    {{-- PRODUCT REVIEW --}}

                    @if($order->status === 'completed')

                    <div
                        id="review-item-{{ $item->id }}"
                        class="product-review-section">

                        @if($item->review)

                        <div class="submitted-review">

                            <div class="submitted-review-header">

                                <div>

                                    <span class="review-label">
                                        YOUR REVIEW
                                    </span>

                                    <div class="review-stars">

                                        @for(
                                        $star = 1;
                                        $star <= 5;
                                            $star++
                                            )

                                            <i
                                            class="
                                                                            bi
                                                                            {{
                                                                                $star <= $item->review->rating
                                                                                    ? 'bi-star-fill'
                                                                                    : 'bi-star'
                                                                            }}
                                                                        "></i>

                                            @endfor

                                    </div>

                                </div>


                                <span class="review-rating-number">
                                    {{ $item->review->rating }}/5
                                </span>

                            </div>


                            @if($item->review->review)

                            <p class="submitted-review-text">
                                {{ $item->review->review }}
                            </p>

                            @endif


                            <span class="review-submitted-status">

                                <i class="bi bi-check-circle"></i>

                                Review submitted

                            </span>

                        </div>

                        @else

                        <div class="write-review">

                            <div class="write-review-heading">

                                <div>

                                    <span class="review-label">
                                        PRODUCT REVIEW
                                    </span>

                                    <h3>
                                        How was this product?
                                    </h3>

                                    <p>
                                        Share your experience
                                        with this product.
                                    </p>

                                </div>

                                <i class="bi bi-chat-square-heart"></i>

                            </div>


                            <form
                                action="{{ route(
                                                            'customer.reviews.store',
                                                            [
                                                                'order' => $order,
                                                                'orderItem' => $item,
                                                            ]
                                                        ) }}"
                                method="POST"
                                class="review-form">

                                @csrf


                                <div class="review-form-group">

                                    <label>
                                        Rating
                                    </label>


                                    <div class="review-rating-options">

                                        @for(
                                        $rating = 5;
                                        $rating >= 1;
                                        $rating--
                                        )

                                        <label class="review-rating-option">

                                            <input
                                                type="radio"
                                                name="rating"
                                                value="{{ $rating }}"
                                                {{ old('rating') == $rating
                                                                                ? 'checked'
                                                                                : '' }}
                                                required>

                                            <span>

                                                {{ $rating }}

                                                <i class="bi bi-star-fill"></i>

                                            </span>

                                        </label>

                                        @endfor

                                    </div>

                                </div>


                                <div class="review-form-group">

                                    <label
                                        for="review-{{ $item->id }}">
                                        Review

                                        <small>
                                            (optional)
                                        </small>
                                    </label>

                                    <textarea
                                        id="review-{{ $item->id }}"
                                        name="review"
                                        rows="4"
                                        maxlength="1000"
                                        placeholder="Tell us what you think about this product...">{{ old('review') }}</textarea>

                                </div>


                                <button
                                    type="submit"
                                    class="review-submit-button">
                                    SUBMIT REVIEW

                                    <i class="bi bi-arrow-right"></i>
                                </button>

                            </form>

                        </div>

                        @endif

                    </div>

                    @endif

                </div>

                @endforeach

            </div>

        </section>


        {{-- ================================================= --}}
        {{-- SHIPPING ADDRESS --}}
        {{-- ================================================= --}}

        <section class="detail-card">

            <div class="detail-card-heading">

                <div class="detail-heading-icon">
                    <i class="bi bi-geo-alt"></i>
                </div>

                <div>

                    <h2>
                        Shipping Address
                    </h2>

                    <p>
                        Delivery information for this order.
                    </p>

                </div>

            </div>


            <div class="shipping-detail">

                <strong>
                    {{ $order->recipient_name }}
                </strong>

                <span>
                    {{ $order->recipient_phone }}
                </span>

                <p>
                    {{ $order->shipping_address }}<br>

                    {{ $order->shipping_city }},
                    {{ $order->shipping_province }}
                    {{ $order->shipping_postal_code }}
                </p>

            </div>

        </section>


        {{-- ================================================= --}}
        {{-- SHIPPING INFORMATION --}}
        {{-- ================================================= --}}

        <section class="detail-card">

            <div class="detail-card-heading">

                <div class="detail-heading-icon">
                    <i class="bi bi-truck"></i>
                </div>

                <div>

                    <h2>
                        Shipping Information
                    </h2>

                    <p>
                        Courier and tracking information.
                    </p>

                </div>

            </div>


            <div class="detail-info-grid">

                <div>

                    <span>
                        SHIPPING SERVICE
                    </span>

                    <strong>
                        {{ $order->shipping_service ?? '-' }}
                    </strong>

                </div>


                <div>

                    <span>
                        COURIER
                    </span>

                    <strong>
                        {{ $order->courier
                                        ?? 'Not assigned yet' }}
                    </strong>

                </div>


                <div>

                    <span>
                        TRACKING NUMBER
                    </span>

                    <strong>
                        {{ $order->tracking_number
                                        ?? 'Not available yet' }}
                    </strong>

                </div>


                <div>

                    <span>
                        SHIPPED AT
                    </span>

                    <strong>
                        {{ $order->shipped_at
                                        ?->format('d M Y, H:i')
                                        ?? '-' }}
                    </strong>

                </div>

            </div>

        </section>

        {{-- ================================================= --}}
        {{-- SHIPMENT TRACKING --}}
        {{-- ================================================= --}}

        @if($order->shipped_at)

        <section class="detail-card">

            <div class="detail-card-heading">

                <div class="detail-heading-icon">
                    <i class="bi bi-truck"></i>
                </div>

                <div>
                    <h2>Shipment Tracking</h2>
                    <p>
                        Track your package journey from LIFESTYLE distribution
                        center to your delivery destination.
                    </p>
                </div>

            </div>


            {{-- TRACKING STATUS --}}

            <div style="
        margin-top: 20px;
        padding: 20px;
        background: #f8f8f8;
        border: 1px solid #eaeaea;
        border-radius: 10px;
    ">

                <span style="
            display: block;
            margin-bottom: 8px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #888;
        ">
                    CURRENT SHIPMENT STATUS
                </span>

                <strong style="
            display: block;
            font-size: 17px;
            color: #171717;
        ">
                    {{ $trackingMap['stage_label'] }}
                </strong>

                <p style="
            margin: 8px 0 0;
            font-size: 12px;
            color: #777;
        ">
                    Your package journey is displayed using estimated
                    shipment progress.
                </p>

            </div>


            {{-- TRACKING PROGRESS --}}

            <div style="margin-top: 22px;">

                <div style="
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 9px;
            font-size: 11px;
        ">

                    <strong>Shipment Progress</strong>

                    <strong>
                        {{ $trackingMap['progress'] }}%
                    </strong>

                </div>

                <div style="
            width: 100%;
            height: 7px;
            overflow: hidden;
            border-radius: 20px;
            background: #e9e9e9;
        ">

                    <div style="
                width: {{ $trackingMap['progress'] }}%;
                height: 100%;
                border-radius: 20px;
                background: #171717;
                transition: width .3s ease;
            "></div>

                </div>

            </div>


            {{-- SHIPMENT ROUTE INFORMATION --}}

            <div style="
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        margin-top: 24px;
    ">

                <div style="
            padding: 15px;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
        ">

                    <span style="
                display: block;
                margin-bottom: 7px;
                color: #999;
                font-size: 10px;
                font-weight: 700;
                letter-spacing: .8px;
            ">
                        SHIPPING ORIGIN
                    </span>

                    <strong style="font-size: 13px;">
                        Banda Aceh
                    </strong>

                    <span style="
                display: block;
                margin-top: 5px;
                color: #888;
                font-size: 11px;
            ">
                        LIFESTYLE Distribution Center
                    </span>

                </div>


                <div style="
            padding: 15px;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
        ">

                    <span style="
                display: block;
                margin-bottom: 7px;
                color: #999;
                font-size: 10px;
                font-weight: 700;
                letter-spacing: .8px;
            ">
                        DELIVERY DESTINATION
                    </span>

                    <strong style="font-size: 13px;">
                        {{ $trackingMap['destination']['label'] ?? 'Destination unavailable' }}
                    </strong>

                    <span style="
                display: block;
                margin-top: 5px;
                color: #888;
                font-size: 11px;
            ">
                        Customer delivery address
                    </span>

                </div>

            </div>


            {{-- LEAFLET MAP --}}

            <div style="margin-top: 28px;">

                <div style="
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 13px;
        ">

                    <strong style="font-size: 14px;">
                        <i class="bi bi-map"></i>
                        Shipment Journey Map
                    </strong>

                    <span style="
                color: #999;
                font-size: 10px;
            ">
                        Estimated route
                    </span>

                </div>


                <div
                    id="shipment-map"

                    data-origin-latitude="{{ $trackingMap['origin']['latitude'] }}"
                    data-origin-longitude="{{ $trackingMap['origin']['longitude'] }}"

                    data-destination-latitude="{{ $trackingMap['destination']['latitude'] ?? '' }}"
                    data-destination-longitude="{{ $trackingMap['destination']['longitude'] ?? '' }}"

                    data-origin-label="{{ $trackingMap['origin']['label'] }}"
                    data-destination-label="{{ $trackingMap['destination']['label'] ?? '' }}"

                    data-progress="{{ $trackingMap['progress'] }}"

                    data-stage="{{ $trackingMap['stage_label'] }}"

                    data-has-destination="{{ $trackingMap['destination'] ? '1' : '0' }}"

                    style="
                width: 100%;
                height: 360px;
                border: 1px solid #e5e5e5;
                border-radius: 10px;
                z-index: 1;
                background: #f3f3f3;
            ">
                </div>


                {{-- MAP LEGEND --}}

                <div style="
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            margin-top: 14px;
            font-size: 11px;
            color: #666;
        ">

                    <span>
                        <i class="bi bi-circle-fill" style="color: #198754;"></i>
                        Distribution Center
                    </span>

                    <span>
                        <i class="bi bi-circle-fill" style="color: #dc3545;"></i>
                        Package Position (Estimated)
                    </span>

                    <span>
                        <i class="bi bi-circle-fill" style="color: #0d6efd;"></i>
                        Destination
                    </span>

                </div>

            </div>


            {{-- COURIER DETAILS --}}

            <div style="
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    ">

                <div style="margin-bottom: 13px;">

                    <span style="
                display: block;
                margin-bottom: 5px;
                color: #999;
                font-size: 10px;
                font-weight: 700;
                letter-spacing: .8px;
            ">
                        COURIER
                    </span>

                    <strong style="font-size: 13px;">
                        {{ $trackingMap['courier'] }}
                    </strong>

                </div>


                <div style="margin-bottom: 13px;">

                    <span style="
                display: block;
                margin-bottom: 5px;
                color: #999;
                font-size: 10px;
                font-weight: 700;
                letter-spacing: .8px;
            ">
                        TRACKING NUMBER
                    </span>

                    <strong style="font-size: 13px;">
                        {{ $trackingMap['tracking_number'] ?? '-' }}
                    </strong>

                </div>


                <div>

                    <span style="
                display: block;
                margin-bottom: 5px;
                color: #999;
                font-size: 10px;
                font-weight: 700;
                letter-spacing: .8px;
            ">
                        ESTIMATED DELIVERY
                    </span>

                    <strong style="font-size: 13px;">

                        @if($trackingMap['estimated_start'] && $trackingMap['estimated_end'])

                        {{ $trackingMap['estimated_start'] }}
                        -
                        {{ $trackingMap['estimated_end'] }}

                        @else

                        Not available

                        @endif

                    </strong>

                </div>

            </div>


            {{-- ESTIMATION DISCLAIMER --}}

            <div style="
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-top: 22px;
        padding: 14px 16px;
        border: 1px solid #e8e8e8;
        border-radius: 8px;
        background: #fafafa;
    ">

                <i class="bi bi-info-circle" style="
            color: #666;
            font-size: 16px;
        "></i>

                <p style="
            margin: 0;
            color: #777;
            font-size: 11px;
            line-height: 1.7;
        ">
                    This map displays an estimated package journey based on
                    shipment status and delivery destination. The package
                    position is simulated and does not represent live courier GPS
                    or the actual road route.
                </p>

            </div>

        </section>

        @endif

        {{-- ================================================= --}}
        {{-- PAYMENT --}}
        {{-- ================================================= --}}

        <section class="detail-card">

            <div class="detail-card-heading">

                <div class="detail-heading-icon">
                    <i class="bi bi-credit-card"></i>
                </div>

                <div>

                    <h2>
                        Payment
                    </h2>

                    <p>
                        Payment information for this order.
                    </p>

                </div>

            </div>


            @if($latestPayment)

            <div class="detail-info-grid">

                <div>

                    <span>
                        METHOD
                    </span>

                    <strong>
                        {{ ucwords(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $latestPayment
                                                    ->payment_method
                                            )
                                        ) }}
                    </strong>

                </div>


                <div>

                    <span>
                        STATUS
                    </span>

                    <strong>
                        {{ ucfirst(
                                            $latestPayment->status
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
                        PAID AT
                    </span>

                    <strong>
                        {{ $latestPayment->paid_at
                                            ?->format(
                                                'd M Y, H:i'
                                            )
                                            ?? '-' }}
                    </strong>

                </div>

            </div>


            @if(
            $latestPayment->proof_image
            && file_exists(
            public_path(
            $latestPayment
            ->proof_image
            )
            )
            )

            <div class="detail-proof">

                <span>
                    PAYMENT PROOF
                </span>

                <img
                    src="{{ asset(
                                            $latestPayment
                                                ->proof_image
                                        ) }}"
                    alt="Payment proof">

            </div>

            @endif


            @if(
            $latestPayment->status
            === 'rejected'
            && $latestPayment
            ->rejection_reason
            )

            <div class="detail-rejection">

                <strong>
                    Rejection Reason
                </strong>

                <p>
                    {{ $latestPayment
                                            ->rejection_reason }}
                </p>

            </div>

            @endif

            @else

            <div class="no-payment">

                <i class="bi bi-credit-card"></i>

                <div>

                    <strong>
                        No Payment Submitted
                    </strong>

                    <p>
                        Complete your payment to
                        continue processing this order.
                    </p>

                </div>

            </div>

            @endif

        </section>

    </div>


    {{-- ================================================= --}}
    {{-- RIGHT SIDEBAR --}}
    {{-- ================================================= --}}

    <aside class="order-detail-sidebar">

        <section class="detail-summary-card">

            <h2>
                Order Summary
            </h2>


            <div class="detail-summary-row">

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


            <div class="detail-summary-row">

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


            <div class="detail-summary-row">

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


            @if($order->voucher)

            <div class="voucher-info">

                <span>
                    VOUCHER
                </span>

                <strong>
                    {{ $order->voucher->code }}
                </strong>

            </div>

            @endif


            <div class="detail-summary-divider"></div>


            <div class="detail-summary-total">

                <span>
                    TOTAL
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


            {{-- ACTION --}}

            @if(
            $order->status
            === 'pending_payment'
            )

            <a
                href="{{ route(
                                    'payment.show',
                                    $order
                                ) }}"
                class="detail-primary-button">
                PAY NOW

                <i class="bi bi-arrow-right"></i>
            </a>

            @elseif(
            $order->status
            === 'waiting_verification'
            )

            <a
                href="{{ route(
                                    'payment.show',
                                    $order
                                ) }}"
                class="detail-secondary-button">
                VIEW PAYMENT

                <i class="bi bi-arrow-right"></i>
            </a>

            @endif


            <a
                href="{{ route('orders.index') }}"
                class="detail-back-button">
                <i class="bi bi-arrow-left"></i>

                MY ORDERS
            </a>

        </section>

    </aside>

</div>

@endif

</div>

</section>


{{-- ================================================= --}}
{{-- LEAFLET SIMULATED SHIPMENT TRACKING --}}
{{-- ================================================= --}}

@if($order->shipped_at)

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const mapElement = document.getElementById('shipment-map');

        if (!mapElement || typeof L === 'undefined') {
            return;
        }

        // =============================================
        // MAP DATA FROM BLADE
        // =============================================

        const origin = {
            lat: Number(mapElement.dataset.originLatitude),
            lng: Number(mapElement.dataset.originLongitude),
            label: mapElement.dataset.originLabel || 'LIFESTYLE - Banda Aceh'
        };

        const destination = {
            lat: Number(mapElement.dataset.destinationLatitude),
            lng: Number(mapElement.dataset.destinationLongitude),
            label: mapElement.dataset.destinationLabel || 'Customer Destination'
        };

        const progress = Math.max(
            0,
            Math.min(100, Number(mapElement.dataset.progress) || 15)
        );

        const hasDestination =
            mapElement.dataset.hasDestination === '1' &&
            Number.isFinite(destination.lat) &&
            Number.isFinite(destination.lng) &&
            destination.lat >= -90 &&
            destination.lat <= 90 &&
            destination.lng >= -180 &&
            destination.lng <= 180;

        if (
            !Number.isFinite(origin.lat) ||
            !Number.isFinite(origin.lng)
        ) {
            mapElement.textContent = 'Map coordinates are unavailable.';
            return;
        }

        // =============================================
        // CREATE MAP
        // =============================================

        const map = L.map(mapElement, {
            scrollWheelZoom: false
        });

        L.tileLayer(
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }
        ).addTo(map);

        L.control.scale({
            metric: true,
            imperial: false
        }).addTo(map);

        // =============================================
        // CUSTOM MARKER ICONS
        // =============================================

        const originIcon = L.divIcon({
            className: 'lifestyle-map-marker',
            html: `
                <div style="
                    width: 22px;
                    height: 22px;
                    border: 3px solid white;
                    border-radius: 50%;
                    background: #198754;
                    box-shadow: 0 2px 8px rgba(0,0,0,.3);
                "></div>
            `,
            iconSize: [22, 22],
            iconAnchor: [11, 11]
        });

        const destinationIcon = L.divIcon({
            className: 'lifestyle-map-marker',
            html: `
                <div style="
                    width: 22px;
                    height: 22px;
                    border: 3px solid white;
                    border-radius: 50%;
                    background: #0d6efd;
                    box-shadow: 0 2px 8px rgba(0,0,0,.3);
                "></div>
            `,
            iconSize: [22, 22],
            iconAnchor: [11, 11]
        });

        const packageIcon = L.divIcon({
            className: 'lifestyle-map-marker',
            html: `
                <div style="
                    display: flex;
                    width: 34px;
                    height: 34px;
                    align-items: center;
                    justify-content: center;
                    border: 3px solid white;
                    border-radius: 50%;
                    background: #dc3545;
                    color: white;
                    font-size: 15px;
                    box-shadow: 0 2px 10px rgba(0,0,0,.35);
                ">
                    <i class="bi bi-box-seam"></i>
                </div>
            `,
            iconSize: [34, 34],
            iconAnchor: [17, 17]
        });

        // =============================================
        // SAFE POPUP CONTENT
        // =============================================

        function createPopup(title, description) {

            const container = document.createElement('div');

            const heading = document.createElement('strong');
            heading.textContent = title;

            const text = document.createElement('div');
            text.textContent = description;

            text.style.marginTop = '5px';

            container.appendChild(heading);
            container.appendChild(text);

            return container;
        }

        // =============================================
        // ORIGIN MARKER
        // =============================================

        const originPoint = [origin.lat, origin.lng];

        L.marker(originPoint, {
            icon: originIcon
        })
        .addTo(map)
        .bindPopup(
            createPopup(
                'LIFESTYLE Distribution Center',
                'Pusat pengiriman Banda Aceh'
            )
        );

        // =============================================
        // DESTINATION VALIDATION
        // =============================================

        if (!hasDestination) {

            map.setView(originPoint, 11);

            const notice = L.control({
                position: 'topright'
            });

            notice.onAdd = function () {

                const element = L.DomUtil.create('div');

                element.textContent =
                    'Koordinat tujuan belum tersedia dalam dataset wilayah.';

                element.style.background = 'white';
                element.style.padding = '10px 12px';
                element.style.border = '1px solid #ddd';
                element.style.borderRadius = '6px';
                element.style.fontSize = '11px';
                element.style.maxWidth = '190px';

                return element;
            };

            notice.addTo(map);

            setTimeout(function () {
                map.invalidateSize();
            }, 200);

            return;
        }

        const destinationPoint = [
            destination.lat,
            destination.lng
        ];

        // =============================================
        // DESTINATION MARKER
        // =============================================

        L.marker(destinationPoint, {
            icon: destinationIcon
        })
        .addTo(map)
        .bindPopup(
            createPopup(
                'Delivery Destination',
                destination.label
            )
        );

        // =============================================
        // ILLUSTRATIVE ROUTE LINE
        // =============================================

        const routeCoordinates = [
            originPoint,
            destinationPoint
        ];

        const routeLine = L.polyline(
            routeCoordinates,
            {
                color: '#64748b',
                weight: 4,
                opacity: 0.85,
                dashArray: '9 9'
            }
        ).addTo(map);

        // =============================================
        // SIMULATED PACKAGE POSITION
        // =============================================

        const ratio = progress / 100;

        const packageLatitude =
            origin.lat +
            (destination.lat - origin.lat) * ratio;

        const packageLongitude =
            origin.lng +
            (destination.lng - origin.lng) * ratio;

        const packagePosition = [
            packageLatitude,
            packageLongitude
        ];

        L.marker(packagePosition, {
            icon: packageIcon,
            zIndexOffset: 1000
        })
        .addTo(map)
        .bindPopup(
            createPopup(
                'Estimated Package Position',
                `Shipment progress: ${progress}%`
            )
        )
        .openPopup();

        // =============================================
        // FIT MAP TO ROUTE
        // =============================================

        map.fitBounds(
            routeLine.getBounds(),
            {
                padding: [40, 40],
                maxZoom: 10
            }
        );

        setTimeout(function () {
            map.invalidateSize();
        }, 200);

    });
</script>

@endif


@endsection