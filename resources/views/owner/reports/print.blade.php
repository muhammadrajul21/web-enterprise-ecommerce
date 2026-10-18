<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Sales & Order Report - Lifestyle</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;

            background: #f4f4f4;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            color: #111111;
        }

        .print-toolbar {
            width: 100%;
            max-width: 1200px;

            margin: 24px auto 0;

            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .back-button,
        .print-button {
            height: 42px;

            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;

            padding: 0 18px;

            border: 1px solid #111111;

            font-size: 11px;
            font-weight: 700;

            text-decoration: none;

            cursor: pointer;
        }

        .back-button {
            background: #ffffff;
            color: #111111;
        }

        .print-button {
            background: #111111;
            color: #ffffff;
        }

        .report-page {
            width: 100%;
            max-width: 1200px;

            margin: 20px auto 40px;

            padding: 45px;

            background: #ffffff;

            box-shadow:
                0 2px 15px
                rgba(0, 0, 0, 0.08);
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            gap: 30px;

            padding-bottom: 25px;

            border-bottom: 2px solid #111111;
        }

        .brand {
            margin: 0 0 7px;

            font-size: 25px;
            letter-spacing: 3px;
        }

        .report-title {
            margin: 0;

            font-size: 16px;
            font-weight: 600;

            color: #555555;
        }

        .report-meta {
            text-align: right;

            font-size: 11px;
            line-height: 1.8;

            color: #555555;
        }

        .report-meta strong {
            color: #111111;
        }

        .filter-info {
            margin-top: 22px;

            display: grid;
            grid-template-columns:
                repeat(3, minmax(0, 1fr));

            gap: 12px;
        }

        .filter-item {
            padding: 12px 14px;

            border: 1px solid #dddddd;
        }

        .filter-item span {
            display: block;

            margin-bottom: 5px;

            font-size: 8px;
            font-weight: 700;

            letter-spacing: 0.7px;

            color: #888888;
        }

        .filter-item strong {
            font-size: 11px;
        }

        .summary-grid {
            margin-top: 25px;

            display: grid;

            grid-template-columns:
                repeat(5, minmax(0, 1fr));

            gap: 10px;
        }

        .summary-card {
            padding: 15px;

            border: 1px solid #dddddd;
        }

        .summary-card span {
            display: block;

            margin-bottom: 8px;

            font-size: 8px;
            font-weight: 700;

            color: #777777;

            letter-spacing: 0.5px;
        }

        .summary-card strong {
            font-size: 15px;
        }

        .table-section {
            margin-top: 30px;
        }

        .section-header {
            margin-bottom: 13px;

            display: flex;
            align-items: end;
            justify-content: space-between;
        }

        .section-header h2 {
            margin: 0 0 4px;

            font-size: 15px;
        }

        .section-header p {
            margin: 0;

            font-size: 9px;

            color: #777777;
        }

        .section-header strong {
            font-size: 10px;
        }

        table {
            width: 100%;

            border-collapse: collapse;
        }

        th {
            padding: 10px 9px;

            border-top: 1px solid #222222;
            border-bottom: 1px solid #222222;

            background: #f5f5f5;

            text-align: left;

            font-size: 8px;
            letter-spacing: 0.4px;
        }

        td {
            padding: 10px 9px;

            border-bottom: 1px solid #dddddd;

            vertical-align: top;

            font-size: 9px;
        }

        .customer-email,
        .date-time {
            display: block;

            margin-top: 3px;

            color: #777777;

            font-size: 8px;
        }

        .status {
            font-size: 8px;
            font-weight: 700;

            text-transform: uppercase;
        }

        .empty-row {
            padding: 35px;

            text-align: center;

            color: #777777;
        }

        .report-footer {
            margin-top: 30px;

            padding-top: 15px;

            border-top: 1px solid #dddddd;

            display: flex;
            justify-content: space-between;
            gap: 20px;

            font-size: 8px;

            color: #777777;
        }

        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        @media print {

            body {
                background: #ffffff;
            }

            .print-toolbar {
                display: none;
            }

            .report-page {
                max-width: none;

                margin: 0;
                padding: 0;

                box-shadow: none;
            }

            thead {
                display: table-header-group;
            }

            tr {
                page-break-inside: avoid;
            }

            .summary-card,
            .filter-item {
                page-break-inside: avoid;
            }
        }

        @media (max-width: 800px) {

            .print-toolbar,
            .report-page {
                width: calc(100% - 30px);
            }

            .report-page {
                padding: 25px;
            }

            .summary-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }

            .filter-info {
                grid-template-columns: 1fr;
            }

            .report-header {
                flex-direction: column;
            }

            .report-meta {
                text-align: left;
            }
        }
    </style>
</head>


<body>

<div class="print-toolbar">

    <a
        href="{{ route('owner.reports.index', array_filter([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $status,
        ])) }}"
        class="back-button"
    >
        <i class="bi bi-arrow-left"></i>

        BACK TO REPORT
    </a>


    <button
        type="button"
        class="print-button"
        onclick="window.print()"
    >
        <i class="bi bi-printer"></i>

        PRINT / SAVE AS PDF
    </button>

</div>


<main class="report-page">

    <header class="report-header">

        <div>

            <h1 class="brand">
                LIFESTYLE
            </h1>

            <p class="report-title">
                Sales & Order Report
            </p>

        </div>


        <div class="report-meta">

            <div>
                <strong>Generated:</strong>

                {{ now()->format('d M Y, H:i') }}
            </div>

            <div>
                <strong>Generated By:</strong>

                {{ auth()->user()->name }}
            </div>

            <div>
                <strong>Role:</strong>

                Owner
            </div>

        </div>

    </header>


    <section class="filter-info">

        <div class="filter-item">

            <span>
                START DATE
            </span>

            <strong>
                {{ $startDate
                    ? \Carbon\Carbon::parse($startDate)->format('d M Y')
                    : 'All Dates' }}
            </strong>

        </div>


        <div class="filter-item">

            <span>
                END DATE
            </span>

            <strong>
                {{ $endDate
                    ? \Carbon\Carbon::parse($endDate)->format('d M Y')
                    : 'All Dates' }}
            </strong>

        </div>


        <div class="filter-item">

            <span>
                ORDER STATUS
            </span>

            <strong>
                {{ $status
                    ? ucwords(str_replace('_', ' ', $status))
                    : 'All Statuses' }}
            </strong>

        </div>

    </section>


    <section class="summary-grid">

        <div class="summary-card">

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

        </div>


        <div class="summary-card">

            <span>
                TOTAL ORDERS
            </span>

            <strong>
                {{ $totalOrders }}
            </strong>

        </div>


        <div class="summary-card">

            <span>
                COMPLETED
            </span>

            <strong>
                {{ $completedOrders }}
            </strong>

        </div>


        <div class="summary-card">

            <span>
                CANCELLED
            </span>

            <strong>
                {{ $cancelledOrders }}
            </strong>

        </div>


        <div class="summary-card">

            <span>
                AVG. ORDER VALUE
            </span>

            <strong>
                Rp {{ number_format(
                    $averageOrderValue,
                    0,
                    ',',
                    '.'
                ) }}
            </strong>

        </div>

    </section>


    <section class="table-section">

        <div class="section-header">

            <div>

                <h2>
                    Transaction Details
                </h2>

                <p>
                    Detailed orders based on the selected report filters.
                </p>

            </div>


            <strong>
                {{ $totalOrders }} ORDER(S)
            </strong>

        </div>


        <table>

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
                            'Waiting Verification',

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
                        <strong>
                            {{ $order->order_code }}
                        </strong>
                    </td>


                    <td>

                        <strong>
                            {{ $order->user?->name ?? '-' }}
                        </strong>

                        <span class="customer-email">
                            {{ $order->user?->email ?? '-' }}
                        </span>

                    </td>


                    <td>

                        {{ $order->created_at
                            ->format('d M Y') }}

                        <span class="date-time">
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

                        {{ $latestPayment
                            ? ucfirst($latestPayment->status)
                            : 'No Payment' }}

                    </td>


                    <td>

                        <span class="status">
                            {{ $statusLabel }}
                        </span>

                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="6"
                        class="empty-row"
                    >
                        No orders found for the selected filters.
                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </section>


    <footer class="report-footer">

        <span>
            LIFESTYLE E-Commerce
        </span>

        <span>
            This report was generated from verified system data.
        </span>

    </footer>

</main>


</body>
</html>