
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan Aktivitas E-Commerce | LIFESTYLE</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link
        rel="stylesheet"
        href="{{ asset('css/owner/dashboard.css') }}">

    <link
        rel="stylesheet"
        href="{{ asset('css/owner/reports.css') }}">

    <style>
        /* =========================================
           REPORT PAGE
        ========================================= */

        .activity-report-page {
            width: 100%;
            max-width: 1500px;
            margin: 0 auto;
            padding: 0 0 40px;
            color: #202020;
        }

        .activity-report-page * {
            box-sizing: border-box;
        }

        .report-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
            margin-bottom: 30px;
        }

        .report-heading-content {
            min-width: 0;
        }

        .report-eyebrow {
            display: inline-block;
            margin-bottom: 9px;
            color: #777;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .report-heading h1 {
            margin: 0 0 9px;
            color: #151515;
            font-size: 29px;
            font-weight: 800;
            letter-spacing: -0.7px;
        }

        .report-heading p {
            margin: 0;
            color: #777;
            font-size: 14px;
            line-height: 1.7;
        }

        .report-heading-icon {
            display: flex;
            width: 58px;
            height: 58px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border: 1px solid #e7e7e7;
            border-radius: 14px;
            background: #fff;
            color: #171717;
            font-size: 25px;
        }

        /* =========================================
           ALERT
        ========================================= */

        .activity-report-alert {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 22px;
            padding: 15px 17px;
            border: 1px solid #f0d5d5;
            border-radius: 9px;
            background: #fff8f8;
            color: #a52d2d;
            font-size: 13px;
        }

        /* =========================================
           FILTER CARD
        ========================================= */

        .activity-filter-card {
            margin-bottom: 27px;
            padding: 25px;
            border: 1px solid #e9e9e9;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 3px 14px rgba(0, 0, 0, 0.025);
        }

        .activity-filter-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 23px;
        }

        .activity-filter-heading h2 {
            margin: 0 0 6px;
            color: #202020;
            font-size: 17px;
            font-weight: 750;
        }

        .activity-filter-heading p {
            margin: 0;
            color: #858585;
            font-size: 12px;
            line-height: 1.6;
        }

        .activity-filter-heading > i {
            color: #333;
            font-size: 21px;
        }

        .activity-filter-form {
            display: grid;
            grid-template-columns: repeat(3, minmax(140px, 1fr));
            gap: 17px;
            align-items: end;
        }

        .activity-filter-field {
            display: flex;
            flex-direction: column;
            gap: 9px;
            min-width: 0;
        }

        .activity-filter-field label {
            color: #555;
            font-size: 10px;
            font-weight: 750;
            letter-spacing: 1px;
        }

        .activity-filter-field input,
        .activity-filter-field select {
            width: 100%;
            min-height: 44px;
            padding: 0 13px;
            border: 1px solid #dedede;
            border-radius: 7px;
            outline: none;
            background: #fff;
            color: #292929;
            font-family: inherit;
            font-size: 13px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .activity-filter-field input:focus,
        .activity-filter-field select:focus {
            border-color: #444;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.055);
        }

        .activity-filter-actions {
            display: flex;
            flex-wrap: wrap;
            grid-column: 1 / -1;
            gap: 9px;
            padding-top: 3px;
        }

        .activity-button {
            display: inline-flex;
            min-height: 42px;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 0 17px;
            border: 1px solid #171717;
            border-radius: 6px;
            background: #171717;
            color: #fff;
            font-family: inherit;
            font-size: 11px;
            font-weight: 750;
            letter-spacing: 0.5px;
            text-decoration: none;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .activity-button:hover {
            background: #383838;
            color: #fff;
        }

        .activity-button.secondary {
            border-color: #dedede;
            background: #fff;
            color: #444;
        }

        .activity-button.secondary:hover {
            border-color: #999;
            background: #f7f7f7;
            color: #111;
        }

        .activity-button.print {
            margin-left: auto;
            border-color: #171717;
            background: #fff;
            color: #171717;
        }

        .activity-button.print:hover {
            background: #171717;
            color: #fff;
        }

        /* =========================================
           ACTIVITY STATISTICS
        ========================================= */

        .activity-stat-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 15px;
            margin-bottom: 28px;
        }

        .activity-stat-card {
            display: flex;
            min-width: 0;
            align-items: flex-start;
            gap: 14px;
            padding: 20px;
            border: 1px solid #eaeaea;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.02);
        }

        .activity-stat-icon {
            display: flex;
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
            background: #f2f2f2;
            color: #242424;
            font-size: 19px;
        }

        .activity-stat-content {
            min-width: 0;
        }

        .activity-stat-content span {
            display: block;
            margin-bottom: 8px;
            color: #777;
            font-size: 9px;
            font-weight: 750;
            letter-spacing: 0.8px;
            line-height: 1.5;
        }

        .activity-stat-content strong {
            display: block;
            color: #171717;
            font-size: 26px;
            font-weight: 800;
            line-height: 1.2;
        }

        .activity-stat-content p {
            margin: 6px 0 0;
            color: #999;
            font-size: 11px;
            line-height: 1.5;
        }

        /* =========================================
           ORDER TABLE
        ========================================= */

        .activity-table-card {
            overflow: hidden;
            border: 1px solid #e9e9e9;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 3px 14px rgba(0, 0, 0, 0.025);
        }

        .activity-table-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 23px 25px;
            border-bottom: 1px solid #ededed;
        }

        .activity-table-heading h2 {
            margin: 0 0 6px;
            color: #202020;
            font-size: 17px;
            font-weight: 750;
        }

        .activity-table-heading p {
            margin: 0;
            color: #858585;
            font-size: 12px;
        }

        .activity-table-heading > i {
            color: #555;
            font-size: 21px;
        }

        .activity-table-scroll {
            width: 100%;
            overflow-x: auto;
        }

        .activity-table {
            width: 100%;
            min-width: 780px;
            border-collapse: collapse;
            text-align: left;
        }

        .activity-table thead {
            background: #f8f8f8;
        }

        .activity-table th {
            padding: 15px 17px;
            color: #777;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.8px;
            white-space: nowrap;
        }

        .activity-table td {
            padding: 17px;
            border-top: 1px solid #eeeeee;
            color: #444;
            font-size: 12px;
            vertical-align: middle;
        }

        .activity-table tbody tr {
            transition: background 0.15s;
        }

        .activity-table tbody tr:hover {
            background: #fafafa;
        }

        .activity-order-code {
            color: #191919;
            font-size: 12px;
            font-weight: 750;
        }

        .activity-customer-name {
            display: block;
            color: #252525;
            font-weight: 700;
        }

        .activity-table-subtext {
            display: block;
            margin-top: 5px;
            color: #999;
            font-size: 10px;
        }

        .activity-order-amount {
            color: #202020;
            font-weight: 750;
            white-space: nowrap;
        }

        /* =========================================
           STATUS BADGES
        ========================================= */

        .activity-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 9px;
            border-radius: 20px;
            background: #f1f1f1;
            color: #555;
            font-size: 9px;
            font-weight: 750;
            white-space: nowrap;
        }

        .activity-badge.status-completed,
        .activity-badge.payment-verified {
            background: #e8f4eb;
            color: #287342;
        }

        .activity-badge.status-cancelled,
        .activity-badge.payment-rejected {
            background: #fbeaea;
            color: #a63b3b;
        }

        .activity-badge.status-shipped,
        .activity-badge.status-processing,
        .activity-badge.status-packing {
            background: #eaf0fa;
            color: #3d6196;
        }

        .activity-badge.status-pending_payment,
        .activity-badge.status-waiting_verification,
        .activity-badge.payment-pending {
            background: #fff4df;
            color: #966b19;
        }

        .activity-empty {
            padding: 55px 20px !important;
            text-align: center !important;
        }

        .activity-empty i {
            display: block;
            margin-bottom: 13px;
            color: #aaa;
            font-size: 34px;
        }

        .activity-empty strong {
            display: block;
            margin-bottom: 7px;
            color: #444;
            font-size: 13px;
        }

        .activity-empty span {
            color: #999;
            font-size: 11px;
        }

        /* =========================================
           CUSTOM PAGINATION
           No Laravel SVG pagination
        ========================================= */

        .activity-pagination {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 18px 23px;
            border-top: 1px solid #ededed;
        }

        .activity-pagination-info {
            color: #888;
            font-size: 11px;
        }

        .activity-pagination-links {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
        }

        .activity-page-link {
            display: inline-flex;
            min-width: 34px;
            height: 34px;
            align-items: center;
            justify-content: center;
            padding: 0 9px;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
            background: #fff;
            color: #444;
            font-size: 11px;
            font-weight: 650;
            text-decoration: none;
            transition: 0.2s;
        }

        .activity-page-link:hover {
            border-color: #222;
            color: #111;
        }

        .activity-page-link.active {
            border-color: #171717;
            background: #171717;
            color: #fff;
        }

        .activity-page-link.disabled {
            background: #f8f8f8;
            color: #bbb;
            cursor: not-allowed;
            pointer-events: none;
        }

        .activity-page-link i {
            font-size: 12px;
        }

        /* =========================================
           RESPONSIVE
        ========================================= */

        @media (max-width: 1100px) {
            .activity-stat-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 700px) {
            .report-heading h1 {
                font-size: 23px;
            }

            .report-heading-icon {
                width: 46px;
                height: 46px;
                font-size: 20px;
            }

            .activity-filter-card {
                padding: 19px;
            }

            .activity-filter-form {
                grid-template-columns: 1fr;
            }

            .activity-filter-actions {
                flex-direction: column;
            }

            .activity-button,
            .activity-button.print {
                width: 100%;
                margin-left: 0;
            }

            .activity-stat-grid {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .activity-stat-card {
                gap: 10px;
                padding: 14px;
            }

            .activity-stat-icon {
                width: 34px;
                height: 34px;
                font-size: 15px;
            }

            .activity-stat-content strong {
                font-size: 21px;
            }

            .activity-stat-content span {
                font-size: 8px;
            }

            .activity-table-heading {
                padding: 18px;
            }

            .activity-pagination {
                align-items: flex-start;
                flex-direction: column;
            }

            .activity-pagination-links {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="owner-layout">

        {{-- SIDEBAR --}}
        <aside class="owner-sidebar">

            <div class="sidebar-brand">
                <h2>LIFESTYLE</h2>
                <span>OWNER PANEL</span>
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

            <div class="sidebar-footer">

                <div class="owner-user">

                    <div class="owner-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                    <div>
                        <strong>{{ auth()->user()->name }}</strong>
                        <span>Owner</span>
                    </div>

                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf

                    <button type="submit" class="logout-button">
                        <i class="bi bi-box-arrow-right"></i>
                        Logout
                    </button>
                </form>

            </div>

        </aside>


        {{-- MAIN CONTENT --}}
        <main class="owner-main">

            <div class="activity-report-page">

                {{-- PAGE HEADER --}}
                <header class="report-heading">

                    <div class="report-heading-content">

                        <span class="report-eyebrow">
                            OWNER REPORT CENTER
                        </span>

                        <h1>
                            Laporan Aktivitas E-Commerce
                        </h1>

                        <p>
                            Dokumentasi aktivitas pesanan dan pembayaran yang tercatat
                            dalam sistem LIFESTYLE.
                        </p>

                    </div>

                    <div class="report-heading-icon">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>

                </header>


                {{-- VALIDATION MESSAGE --}}
                @if($errors->any())

                    <div class="activity-report-alert">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>

                @endif


                {{-- FILTER REPORT --}}
                <section class="activity-filter-card">

                    <div class="activity-filter-heading">

                        <div>
                            <h2>Filter Laporan</h2>
                            <p>
                                Tentukan periode dan status pesanan yang ingin
                                dimasukkan ke dalam laporan.
                            </p>
                        </div>

                        <i class="bi bi-funnel"></i>

                    </div>


                    <form
                        action="{{ route('owner.reports.index') }}"
                        method="GET"
                        class="activity-filter-form">

                        <div class="activity-filter-field">

                            <label for="start_date">
                                TANGGAL MULAI
                            </label>

                            <input
                                type="date"
                                id="start_date"
                                name="start_date"
                                value="{{ $startDate }}">

                        </div>


                        <div class="activity-filter-field">

                            <label for="end_date">
                                TANGGAL AKHIR
                            </label>

                            <input
                                type="date"
                                id="end_date"
                                name="end_date"
                                value="{{ $endDate }}">

                        </div>


                        <div class="activity-filter-field">

                            <label for="status">
                                STATUS PESANAN
                            </label>

                            <select id="status" name="status">

                                <option value="">
                                    Semua Status
                                </option>

                                <option
                                    value="pending_payment"
                                    @selected($status === 'pending_payment')>
                                    Menunggu Pembayaran
                                </option>

                                <option
                                    value="waiting_verification"
                                    @selected($status === 'waiting_verification')>
                                    Menunggu Verifikasi
                                </option>

                                <option
                                    value="processing"
                                    @selected($status === 'processing')>
                                    Diproses
                                </option>

                                <option
                                    value="packing"
                                    @selected($status === 'packing')>
                                    Dikemas
                                </option>

                                <option
                                    value="shipped"
                                    @selected($status === 'shipped')>
                                    Dikirim
                                </option>

                                <option
                                    value="completed"
                                    @selected($status === 'completed')>
                                    Selesai
                                </option>

                                <option
                                    value="cancelled"
                                    @selected($status === 'cancelled')>
                                    Dibatalkan
                                </option>

                            </select>

                        </div>


                        <div class="activity-filter-actions">

                            <button type="submit" class="activity-button">
                                <i class="bi bi-funnel"></i>
                                TERAPKAN FILTER
                            </button>

                            <a
                                href="{{ route('owner.reports.index') }}"
                                class="activity-button secondary">
                                <i class="bi bi-arrow-counterclockwise"></i>
                                RESET
                            </a>

                            <a
                                href="{{ route('owner.reports.print', array_filter([
                                    'start_date' => $startDate,
                                    'end_date' => $endDate,
                                    'status' => $status,
                                ])) }}"
                                target="_blank"
                                class="activity-button print">
                                <i class="bi bi-printer"></i>
                                CETAK / SIMPAN PDF
                            </a>

                        </div>

                    </form>

                </section>


                {{-- ACTIVITY SUMMARY --}}
                <section class="activity-stat-grid">

                    <div class="activity-stat-card">

                        <div class="activity-stat-icon">
                            <i class="bi bi-bag"></i>
                        </div>

                        <div class="activity-stat-content">
                            <span>TOTAL PESANAN</span>
                            <strong>{{ $totalOrders }}</strong>
                            <p>Pesanan tercatat dalam periode ini</p>
                        </div>

                    </div>


                    <div class="activity-stat-card">

                        <div class="activity-stat-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>

                        <div class="activity-stat-content">
                            <span>PESANAN SELESAI</span>
                            <strong>{{ $completedOrders }}</strong>
                            <p>Pesanan berstatus completed</p>
                        </div>

                    </div>


                    <div class="activity-stat-card">

                        <div class="activity-stat-icon">
                            <i class="bi bi-x-circle"></i>
                        </div>

                        <div class="activity-stat-content">
                            <span>PESANAN DIBATALKAN</span>
                            <strong>{{ $cancelledOrders }}</strong>
                            <p>Pesanan berstatus cancelled</p>
                        </div>

                    </div>


                    <div class="activity-stat-card">

                        <div class="activity-stat-icon">
                            <i class="bi bi-credit-card"></i>
                        </div>

                        <div class="activity-stat-content">
                            <span>TOTAL AKTIVITAS PEMBAYARAN</span>
                            <strong>{{ $totalPaymentRecords }}</strong>
                            <p>Catatan pembayaran dalam laporan</p>
                        </div>

                    </div>


                    <div class="activity-stat-card">

                        <div class="activity-stat-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>

                        <div class="activity-stat-content">
                            <span>PEMBAYARAN TERVERIFIKASI</span>
                            <strong>{{ $verifiedPaymentCount }}</strong>
                            <p>Pembayaran berstatus verified</p>
                        </div>

                    </div>


                    <div class="activity-stat-card">

                        <div class="activity-stat-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>

                        <div class="activity-stat-content">
                            <span>MENUNGGU VERIFIKASI</span>
                            <strong>{{ $pendingPaymentCount }}</strong>
                            <p>Pembayaran berstatus pending</p>
                        </div>

                    </div>


                    <div class="activity-stat-card">

                        <div class="activity-stat-icon">
                            <i class="bi bi-exclamation-circle"></i>
                        </div>

                        <div class="activity-stat-content">
                            <span>PEMBAYARAN DITOLAK</span>
                            <strong>{{ $rejectedPaymentCount }}</strong>
                            <p>Pembayaran berstatus rejected</p>
                        </div>

                    </div>

                </section>


                {{-- ORDER ACTIVITY TABLE --}}
                <section class="activity-table-card">

                    <div class="activity-table-heading">

                        <div>
                            <h2>Riwayat Aktivitas Pesanan</h2>

                            <p>
                                Daftar transaksi pesanan pelanggan yang tercatat
                                dalam sistem.
                            </p>
                        </div>

                        <i class="bi bi-table"></i>

                    </div>


                    <div class="activity-table-scroll">

                        <table class="activity-table">

                            <thead>
                                <tr>
                                    <th>KODE PESANAN</th>
                                    <th>PELANGGAN</th>
                                    <th>TANGGAL PESANAN</th>
                                    <th>NILAI PESANAN</th>
                                    <th>STATUS PEMBAYARAN</th>
                                    <th>STATUS PESANAN</th>
                                </tr>
                            </thead>


                            <tbody>

                                @forelse($orders as $order)

                                    @php
                                        $latestPayment = $order->payments
                                            ->sortByDesc('created_at')
                                            ->first();

                                        $statusLabels = [
                                            'pending_payment' => 'Menunggu Pembayaran',
                                            'waiting_verification' => 'Menunggu Verifikasi',
                                            'processing' => 'Diproses',
                                            'packing' => 'Dikemas',
                                            'shipped' => 'Dikirim',
                                            'completed' => 'Selesai',
                                            'cancelled' => 'Dibatalkan',
                                        ];

                                        $paymentLabels = [
                                            'pending' => 'Pending',
                                            'verified' => 'Terverifikasi',
                                            'rejected' => 'Ditolak',
                                        ];

                                        $orderStatusLabel = $statusLabels[$order->status]
                                            ?? ucwords(str_replace('_', ' ', $order->status));

                                        $paymentStatusLabel = $latestPayment
                                            ? ($paymentLabels[$latestPayment->status]
                                                ?? ucfirst($latestPayment->status))
                                            : 'Belum Ada';
                                    @endphp


                                    <tr>

                                        <td>
                                            <strong class="activity-order-code">
                                                {{ $order->order_code }}
                                            </strong>
                                        </td>


                                        <td>
                                            <strong class="activity-customer-name">
                                                {{ $order->user?->name ?? '-' }}
                                            </strong>

                                            <span class="activity-table-subtext">
                                                {{ $order->user?->email ?? '-' }}
                                            </span>
                                        </td>


                                        <td>
                                            {{ $order->created_at->format('d M Y') }}

                                            <span class="activity-table-subtext">
                                                {{ $order->created_at->format('H:i') }}
                                            </span>
                                        </td>


                                        <td>
                                            <strong class="activity-order-amount">
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

                                                <span class="activity-badge payment-{{ $latestPayment->status }}">
                                                    {{ $paymentStatusLabel }}
                                                </span>

                                            @else

                                                <span class="activity-badge">
                                                    Belum Ada
                                                </span>

                                            @endif
                                        </td>


                                        <td>
                                            <span class="activity-badge status-{{ $order->status }}">
                                                {{ $orderStatusLabel }}
                                            </span>
                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="6" class="activity-empty">

                                            <i class="bi bi-inbox"></i>

                                            <strong>
                                                Tidak Ada Data Pesanan
                                            </strong>

                                            <span>
                                                Belum terdapat pesanan yang sesuai
                                                dengan filter laporan.
                                            </span>

                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>


                    {{-- CUSTOM PAGINATION --}}
                    @if($orders->hasPages())

                        @php
                            $currentPage = $orders->currentPage();
                            $lastPage = $orders->lastPage();

                            $paginationStart = max(1, $currentPage - 2);
                            $paginationEnd = min($lastPage, $currentPage + 2);
                        @endphp

                        <div class="activity-pagination">

                            <div class="activity-pagination-info">

                                Menampilkan
                                {{ $orders->firstItem() ?? 0 }}
                                sampai
                                {{ $orders->lastItem() ?? 0 }}
                                dari
                                {{ $orders->total() }}
                                data pesanan

                            </div>


                            <div class="activity-pagination-links">

                                {{-- PREVIOUS --}}
                                @if($orders->onFirstPage())

                                    <span class="activity-page-link disabled">
                                        <i class="bi bi-chevron-left"></i>
                                        Sebelumnya
                                    </span>

                                @else

                                    <a
                                        href="{{ request()->fullUrlWithQuery([
                                            'page' => $currentPage - 1
                                        ]) }}"
                                        class="activity-page-link">
                                        <i class="bi bi-chevron-left"></i>
                                        Sebelumnya
                                    </a>

                                @endif


                                {{-- FIRST PAGE --}}
                                @if($paginationStart > 1)

                                    <a
                                        href="{{ request()->fullUrlWithQuery(['page' => 1]) }}"
                                        class="activity-page-link">
                                        1
                                    </a>

                                    @if($paginationStart > 2)
                                        <span class="activity-pagination-info">...</span>
                                    @endif

                                @endif


                                {{-- PAGE NUMBERS --}}
                                @for($page = $paginationStart; $page <= $paginationEnd; $page++)

                                    <a
                                        href="{{ request()->fullUrlWithQuery(['page' => $page]) }}"
                                        class="activity-page-link {{ $page === $currentPage ? 'active' : '' }}">
                                        {{ $page }}
                                    </a>

                                @endfor


                                {{-- LAST PAGE --}}
                                @if($paginationEnd < $lastPage)

                                    @if($paginationEnd < $lastPage - 1)
                                        <span class="activity-pagination-info">...</span>
                                    @endif

                                    <a
                                        href="{{ request()->fullUrlWithQuery([
                                            'page' => $lastPage
                                        ]) }}"
                                        class="activity-page-link">
                                        {{ $lastPage }}
                                    </a>

                                @endif


                                {{-- NEXT --}}
                                @if($orders->hasMorePages())

                                    <a
                                        href="{{ request()->fullUrlWithQuery([
                                            'page' => $currentPage + 1
                                        ]) }}"
                                        class="activity-page-link">
                                        Selanjutnya
                                        <i class="bi bi-chevron-right"></i>
                                    </a>

                                @else

                                    <span class="activity-page-link disabled">
                                        Selanjutnya
                                        <i class="bi bi-chevron-right"></i>
                                    </span>

                                @endif

                            </div>

                        </div>

                    @endif

                </section>

            </div>

        </main>

    </div>

</body>

</html>
