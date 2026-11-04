
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan Aktivitas E-Commerce LIFESTYLE</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #eeeeee;
            color: #222222;
            font-family: "Times New Roman", serif;
            font-size: 11pt;
            line-height: 1.65;
        }

        .print-toolbar {
            max-width: 900px;
            margin: 22px auto;
            display: flex;
            justify-content: space-between;
            gap: 12px;
        }

        .print-toolbar a,
        .print-toolbar button {
            padding: 10px 18px;
            border: 1px solid #222;
            background: #fff;
            color: #222;
            font-family: Arial, sans-serif;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .print-toolbar .print-button {
            background: #222;
            color: #fff;
        }

        .report-page {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto 35px;
            padding: 20mm 19mm;
            background: #fff;
            box-shadow: 0 2px 18px rgba(0, 0, 0, .12);
        }

        .report-header {
            text-align: center;
            padding-bottom: 18px;
            border-bottom: 3px double #222;
        }

        .brand {
            margin: 0;
            font-family: Arial, sans-serif;
            font-size: 27px;
            font-weight: 800;
            letter-spacing: 5px;
        }

        .brand-subtitle {
            margin: 2px 0 15px;
            font-family: Arial, sans-serif;
            font-size: 10px;
            letter-spacing: 2px;
        }

        .report-title {
            margin: 12px 0 4px;
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .report-subtitle {
            margin: 0;
            font-size: 11pt;
        }

        .document-label {
            display: inline-block;
            margin-top: 12px;
            padding: 4px 12px;
            border: 1px solid #555;
            font-family: Arial, sans-serif;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .identity-section {
            margin-top: 25px;
        }

        .identity-table {
            width: 100%;
            border-collapse: collapse;
        }

        .identity-table td {
            padding: 3px 0;
            vertical-align: top;
            font-size: 10.5pt;
        }

        .identity-table td:first-child {
            width: 165px;
        }

        .identity-table td:nth-child(2) {
            width: 15px;
        }

        .section {
            margin-top: 24px;
        }

        .section-title {
            margin: 0 0 11px;
            padding-bottom: 6px;
            border-bottom: 1px solid #555;
            font-size: 12pt;
            font-weight: 700;
            text-transform: uppercase;
        }

        .section p {
            margin: 7px 0;
            text-align: justify;
        }

        .report-paragraph {
            text-indent: 30px;
        }

        .summary-text {
            margin-top: 10px;
            padding: 12px 15px;
            border-left: 3px solid #333;
            background: #f7f7f7;
        }

        .summary-text p {
            margin: 0;
        }

        table.data-table {
            width: 100%;
            margin-top: 12px;
            border-collapse: collapse;
        }

        .data-table th {
            padding: 8px 7px;
            border: 1px solid #777;
            background: #eeeeee;
            text-align: left;
            font-size: 9.5pt;
        }

        .data-table td {
            padding: 7px;
            border: 1px solid #999;
            vertical-align: top;
            font-size: 9.5pt;
        }

        .data-table tfoot td {
            font-weight: 700;
            background: #f4f4f4;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .number {
            white-space: nowrap;
        }

        .method-list {
            margin: 8px 0;
            padding-left: 23px;
        }

        .method-list li {
            margin-bottom: 4px;
        }

        .note {
            margin-top: 10px !important;
            color: #555;
            font-size: 9.5pt;
            font-style: italic;
        }

        .conclusion {
            margin-top: 20px;
        }

        .appendix {
            page-break-before: always;
        }

        .appendix-heading {
            margin-bottom: 20px;
            text-align: center;
        }

        .appendix-heading h2 {
            margin: 0;
            font-size: 15pt;
            text-transform: uppercase;
        }

        .appendix-heading p {
            margin: 5px 0;
            font-size: 10pt;
        }

        .appendix + .appendix {
            page-break-before: always;
        }

        .small-text {
            font-size: 9pt !important;
        }

        .report-signature {
            display: flex;
            justify-content: flex-end;
            margin-top: 38px;
            page-break-inside: avoid;
        }

        .signature-content {
            width: 230px;
            text-align: center;
        }

        .signature-space {
            height: 75px;
        }

        .signature-name {
            display: inline-block;
            min-width: 180px;
            padding-bottom: 3px;
            border-bottom: 1px solid #222;
            font-weight: 700;
        }

        .signature-role {
            margin-top: 3px;
            font-size: 10pt;
        }

        .report-footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #aaa;
            text-align: center;
            font-size: 9pt;
            color: #555;
        }

        .empty-message {
            padding: 15px;
            text-align: center;
            color: #666;
            font-style: italic;
        }

        .keep-together {
            page-break-inside: avoid;
        }

        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        @media print {
            body {
                background: #fff;
            }

            .print-toolbar {
                display: none !important;
            }

            .report-page {
                width: auto;
                min-height: 0;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            .data-table thead {
                display: table-header-group;
            }

            .data-table tr {
                page-break-inside: avoid;
            }

            .section-title {
                page-break-after: avoid;
            }
        }

        @media screen and (max-width: 750px) {
            .print-toolbar {
                margin: 15px;
                flex-direction: column;
            }

            .report-page {
                width: calc(100% - 20px);
                min-height: 0;
                padding: 25px 18px;
            }

            .data-table {
                font-size: 9px;
            }
        }
    </style>
</head>

@php
    $periodLabel = 'Seluruh periode data';

    if ($startDate && $endDate) {
        $periodLabel = \Carbon\Carbon::parse($startDate)->format('d/m/Y')
            . ' s.d. '
            . \Carbon\Carbon::parse($endDate)->format('d/m/Y');
    } elseif ($startDate) {
        $periodLabel = 'Mulai ' .
            \Carbon\Carbon::parse($startDate)->format('d/m/Y')
            . ' hingga tanggal laporan';
    } elseif ($endDate) {
        $periodLabel = 'Sampai ' .
            \Carbon\Carbon::parse($endDate)->format('d/m/Y');
    }

    $orderStatusLabels = [
        'pending_payment' => 'Menunggu Pembayaran',
        'waiting_verification' => 'Menunggu Verifikasi',
        'processing' => 'Diproses',
        'packing' => 'Dikemas',
        'shipped' => 'Dikirim',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    $paymentStatusLabels = [
        'verified' => 'Terverifikasi',
        'pending' => 'Menunggu Verifikasi',
        'rejected' => 'Ditolak',
    ];

    $paymentMethodLabels = [
        'bank_transfer' => 'Transfer Bank',
        'e_wallet' => 'E-Wallet',
        'cash' => 'Tunai',
        'cod' => 'Cash on Delivery',
    ];
@endphp

<body>

    {{-- TOOLBAR --}}
    <div class="print-toolbar">

        <a href="{{ route('owner.reports.index', array_filter([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $status,
        ])) }}">
            KEMBALI KE REPORTS
        </a>

        <button
            type="button"
            class="print-button"
            onclick="window.print()">
            CETAK / SIMPAN PDF
        </button>

    </div>


    <main class="report-page">

        {{-- ================================================= --}}
        {{-- HEADER --}}
        {{-- ================================================= --}}

        <header class="report-header">

            <h1 class="brand">LIFESTYLE</h1>

            <p class="brand-subtitle">
                E-COMMERCE BUSINESS
            </p>

            <h2 class="report-title">
                Laporan Aktivitas E-Commerce
            </h2>

            <p class="report-subtitle">
                Laporan Penjualan, Pesanan, dan Pembayaran
            </p>

            <span class="document-label">
                DOKUMEN LAPORAN RESMI
            </span>

        </header>


        {{-- ================================================= --}}
        {{-- IDENTITAS LAPORAN --}}
        {{-- ================================================= --}}

        <section class="identity-section">

            <h3 class="section-title">
                Identitas Laporan
            </h3>

            <table class="identity-table">

                <tr>
                    <td>Nama Usaha</td>
                    <td>:</td>
                    <td><strong>LIFESTYLE E-Commerce</strong></td>
                </tr>

                <tr>
                    <td>Jenis Laporan</td>
                    <td>:</td>
                    <td>Laporan Aktivitas Penjualan dan Pembayaran</td>
                </tr>

                <tr>
                    <td>Periode Laporan</td>
                    <td>:</td>
                    <td>{{ $periodLabel }}</td>
                </tr>

                <tr>
                    <td>Filter Status Pesanan</td>
                    <td>:</td>
                    <td>
                        {{ $status
                            ? ($orderStatusLabels[$status] ?? $status)
                            : 'Seluruh Status'
                        }}
                    </td>
                </tr>

                <tr>
                    <td>Tanggal Pembuatan</td>
                    <td>:</td>
                    <td>
                        {{ $generatedAt->format('d/m/Y H:i') }}
                    </td>
                </tr>

                <tr>
                    <td>Disusun Oleh</td>
                    <td>:</td>
                    <td>{{ $reportPreparedBy }}</td>
                </tr>

                <tr>
                    <td>Jabatan</td>
                    <td>:</td>
                    <td>Owner LIFESTYLE</td>
                </tr>

            </table>

        </section>


        {{-- ================================================= --}}
        {{-- PENDAHULUAN --}}
        {{-- ================================================= --}}

        <section class="section">

            <h3 class="section-title">
                I. Pendahuluan
            </h3>

            <p class="report-paragraph">
                Laporan Aktivitas E-Commerce LIFESTYLE disusun sebagai
                bentuk dokumentasi dan pertanggungjawaban atas kegiatan
                transaksi yang tercatat di dalam sistem selama periode
                {{ $periodLabel }}.
            </p>

            <p class="report-paragraph">
                Laporan ini memberikan gambaran mengenai aktivitas
                pemesanan pelanggan, perkembangan status pesanan,
                pencatatan pembayaran, serta proses verifikasi pembayaran
                yang dilaksanakan dalam operasional sistem e-commerce
                LIFESTYLE.
            </p>

            <p class="report-paragraph">
                Seluruh informasi dan angka yang disajikan berasal
                dari catatan transaksi yang tersimpan di dalam database
                aplikasi. Laporan ini disusun oleh Owner sebagai bahan
                dokumentasi, pemantauan, dan evaluasi aktivitas sistem.
            </p>

        </section>


        {{-- ================================================= --}}
        {{-- RINGKASAN PESANAN --}}
        {{-- ================================================= --}}

        <section class="section">

            <h3 class="section-title">
                II. Rekapitulasi Aktivitas Pemesanan
            </h3>

            <p class="report-paragraph">
                Berdasarkan catatan transaksi dalam periode laporan,
                sistem LIFESTYLE mencatat sebanyak
                <strong>{{ number_format($totalOrders, 0, ',', '.') }}
                pesanan</strong>.
                Dari keseluruhan pesanan tersebut, terdapat
                <strong>{{ number_format($completedOrders, 0, ',', '.') }}
                pesanan selesai</strong> dan
                <strong>{{ number_format($cancelledOrders, 0, ',', '.') }}
                pesanan dibatalkan</strong>.
            </p>

            <p>
                Rincian status pesanan yang tercatat pada periode
                pelaporan disajikan sebagai berikut.
            </p>

            <table class="data-table">

                <thead>
                    <tr>
                        <th width="8%">No.</th>
                        <th>Status Pesanan</th>
                        <th width="25%" class="text-center">
                            Jumlah
                        </th>
                    </tr>
                </thead>

                <tbody>

                    @php
                        $number = 1;
                    @endphp

                    @foreach($orderStatusLabels as $key => $label)

                        @php
                            $statusTotal =
                                $orderStatusSummary->get($key)->total ?? 0;
                        @endphp

                        <tr>
                            <td class="text-center">
                                {{ $number++ }}
                            </td>

                            <td>{{ $label }}</td>

                            <td class="text-center">
                                {{ number_format($statusTotal, 0, ',', '.') }}
                            </td>
                        </tr>

                    @endforeach

                    <tr>
                        <td colspan="2">
                            <strong>Total Pesanan</strong>
                        </td>

                        <td class="text-center">
                            <strong>
                                {{ number_format($totalOrders, 0, ',', '.') }}
                            </strong>
                        </td>
                    </tr>

                </tbody>

            </table>

        </section>


        {{-- ================================================= --}}
        {{-- AKTIVITAS PEMBAYARAN --}}
        {{-- ================================================= --}}

        <section class="section">

            <h3 class="section-title">
                III. Rekapitulasi Aktivitas Pembayaran
            </h3>

            <p class="report-paragraph">
                Selain aktivitas pemesanan, sistem juga mencatat
                proses pembayaran yang dilakukan pelanggan.
                Pembayaran dikelompokkan berdasarkan status verifikasi
                yang tersimpan pada sistem.
            </p>

            <p>
                Selama periode laporan terdapat
                <strong>
                    {{ number_format($totalPaymentRecords, 0, ',', '.') }}
                    catatan pembayaran
                </strong>, dengan rincian status sebagai berikut.
            </p>

            <table class="data-table">

                <thead>
                    <tr>
                        <th width="8%">No.</th>
                        <th>Status Pembayaran</th>
                        <th width="19%" class="text-center">
                            Jumlah
                        </th>
                        <th width="30%" class="text-right">
                            Nominal Tercatat
                        </th>
                    </tr>
                </thead>

                <tbody>

                    @php
                        $paymentNumber = 1;
                    @endphp

                    @foreach($paymentStatusLabels as $key => $label)

                        @php
                            $paymentSummary =
                                $paymentStatusSummary->get($key);

                            $paymentCount =
                                $paymentSummary->total ?? 0;

                            $paymentAmount =
                                $paymentSummary->amount ?? 0;
                        @endphp

                        <tr>
                            <td class="text-center">
                                {{ $paymentNumber++ }}
                            </td>

                            <td>{{ $label }}</td>

                            <td class="text-center">
                                {{ number_format($paymentCount, 0, ',', '.') }}
                            </td>

                            <td class="text-right number">
                                Rp {{ number_format(
                                    (float) $paymentAmount,
                                    0,
                                    ',',
                                    '.'
                                ) }}
                            </td>
                        </tr>

                    @endforeach

                </tbody>

            </table>

            <div class="summary-text">

                <p>
                    Berdasarkan data pembayaran, terdapat
                    <strong>
                        {{ number_format($verifiedPaymentCount, 0, ',', '.') }}
                        pembayaran yang telah terverifikasi
                    </strong>
                    dengan total nominal sebesar
                    <strong>
                        Rp {{ number_format(
                            $totalRevenue,
                            0,
                            ',',
                            '.'
                        ) }}
                    </strong>.
                    Nilai tersebut merupakan akumulasi pembayaran
                    terverifikasi yang tercatat di dalam sistem,
                    bukan perhitungan laba atau keuntungan usaha.
                </p>

            </div>

        </section>


        {{-- ================================================= --}}
        {{-- METODE PEMBAYARAN --}}
        {{-- ================================================= --}}

        <section class="section">

            <h3 class="section-title">
                IV. Rekapitulasi Metode Pembayaran
            </h3>

            <p class="report-paragraph">
                Pembayaran yang telah terverifikasi juga dikelompokkan
                berdasarkan metode pembayaran yang digunakan pelanggan.
                Rekapitulasi ini memberikan gambaran mengenai metode
                transaksi yang tercatat dalam sistem.
            </p>

            <table class="data-table">

                <thead>
                    <tr>
                        <th width="8%">No.</th>
                        <th>Metode Pembayaran</th>
                        <th width="18%" class="text-center">
                            Transaksi
                        </th>
                        <th width="30%" class="text-right">
                            Nominal
                        </th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($paymentMethodSummary as $index => $method)

                        <tr>
                            <td class="text-center">
                                {{ $index + 1 }}
                            </td>

                            <td>
                                {{ $paymentMethodLabels[$method->payment_method]
                                    ?? ucwords(str_replace(
                                        '_',
                                        ' ',
                                        $method->payment_method
                                    ))
                                }}
                            </td>

                            <td class="text-center">
                                {{ number_format($method->total, 0, ',', '.') }}
                            </td>

                            <td class="text-right number">
                                Rp {{ number_format(
                                    (float) $method->amount,
                                    0,
                                    ',',
                                    '.'
                                ) }}
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="4" class="empty-message">
                                Tidak terdapat pembayaran terverifikasi
                                pada periode laporan.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </section>


        {{-- ================================================= --}}
        {{-- CATATAN PENYUSUNAN DATA --}}
        {{-- ================================================= --}}

        <section class="section">

            <h3 class="section-title">
                V. Keterangan dan Dasar Penyusunan
            </h3>

            <p>
                Penyusunan laporan ini menggunakan ketentuan pencatatan
                sebagai berikut:
            </p>

            <ol class="method-list">

                <li>
                    Data pesanan dikelompokkan berdasarkan tanggal
                    pembuatan pesanan di dalam sistem.
                </li>

                <li>
                    Data pembayaran dikelompokkan berdasarkan tanggal
                    verifikasi pembayaran, waktu pembayaran, atau
                    tanggal pencatatan apabila informasi tanggal
                    sebelumnya tidak tersedia.
                </li>

                <li>
                    Pembayaran terverifikasi merupakan pembayaran
                    yang telah memperoleh status verified di dalam sistem.
                </li>

                <li>
                    Status pesanan dan pembayaran menggambarkan
                    kondisi yang tercatat pada saat laporan dibuat.
                </li>

                <li>
                    Nilai pembayaran dalam laporan tidak dikurangi
                    biaya operasional dan tidak dinyatakan sebagai
                    laba bersih.
                </li>

            </ol>

        </section>


        {{-- ================================================= --}}
        {{-- KESIMPULAN --}}
        {{-- ================================================= --}}

        <section class="section conclusion">

            <h3 class="section-title">
                VI. Kesimpulan
            </h3>

            <p class="report-paragraph">
                Berdasarkan hasil rekapitulasi, kegiatan operasional
                sistem e-commerce LIFESTYLE selama periode laporan
                telah menghasilkan catatan aktivitas pemesanan dan
                pembayaran sebagaimana diuraikan dalam dokumen ini.
            </p>

            <p class="report-paragraph">
                Data tersebut diharapkan dapat menjadi bahan
                pemantauan bagi Owner dalam melihat perkembangan
                transaksi, mengetahui status pesanan, serta mengevaluasi
                proses pembayaran yang berlangsung pada sistem.
            </p>

            <p class="report-paragraph">
                Demikian laporan aktivitas E-Commerce LIFESTYLE ini
                disusun berdasarkan data yang tersedia di dalam sistem
                dan dapat digunakan sebagaimana mestinya.
            </p>

        </section>


        {{-- ================================================= --}}
        {{-- LAMPIRAN I - RINCIAN PESANAN --}}
        {{-- ================================================= --}}

        <section class="section appendix">

            <div class="appendix-heading">

                <h2>Lampiran I</h2>

                <p>
                    Rincian Data Pesanan
                </p>

                <p>
                    Periode: {{ $periodLabel }}
                </p>

            </div>

            <table class="data-table">

                <thead>
                    <tr>
                        <th width="5%">No.</th>
                        <th>Nomor Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th class="text-right">Total Pesanan</th>
                        <th>Pembayaran</th>
                        <th>Status Pesanan</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($orders as $index => $order)

                        @php
                            $latestPayment = $order->payments->first();
                        @endphp

                        <tr>

                            <td class="text-center">
                                {{ $index + 1 }}
                            </td>

                            <td>
                                <strong>
                                    {{ $order->order_code }}
                                </strong>
                            </td>

                            <td>
                                {{ $order->user?->name ?? '-' }}
                                <span class="small-text">
                                    {{ $order->user?->email ?? '' }}
                                </span>
                            </td>

                            <td>
                                {{ $order->created_at->format('d/m/Y') }}
                            </td>

                            <td class="text-right number">
                                Rp {{ number_format(
                                    $order->total_amount,
                                    0,
                                    ',',
                                    '.'
                                ) }}
                            </td>

                            <td>
                                {{ $latestPayment
                                    ? ($paymentStatusLabels[$latestPayment->status]
                                        ?? ucfirst($latestPayment->status))
                                    : 'Belum ada'
                                }}
                            </td>

                            <td>
                                {{ $orderStatusLabels[$order->status]
                                    ?? ucfirst($order->status)
                                }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="empty-message">
                                Tidak terdapat data pesanan pada periode ini.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

            <p class="note">
                Catatan: nominal pada rincian pesanan merupakan total
                pesanan, bukan seluruhnya pembayaran yang telah terverifikasi.
            </p>

        </section>


        {{-- ================================================= --}}
        {{-- LAMPIRAN II - RINCIAN PEMBAYARAN --}}
        {{-- ================================================= --}}

        <section class="section appendix">

            <div class="appendix-heading">

                <h2>Lampiran II</h2>

                <p>
                    Rincian Aktivitas Pembayaran
                </p>

                <p>
                    Periode: {{ $periodLabel }}
                </p>

            </div>

            <table class="data-table">

                <thead>
                    <tr>
                        <th width="5%">No.</th>
                        <th>Nomor Pesanan</th>
                        <th>Tanggal Aktivitas</th>
                        <th>Metode</th>
                        <th class="text-right">Nominal</th>
                        <th>Status</th>
                        <th>Diproses Oleh</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($payments as $index => $payment)

                        @php
                            $paymentActivityDate =
                                $payment->verified_at
                                ?? $payment->paid_at
                                ?? $payment->created_at;
                        @endphp

                        <tr>

                            <td class="text-center">
                                {{ $index + 1 }}
                            </td>

                            <td>
                                {{ $payment->order?->order_code ?? '-' }}
                            </td>

                            <td>
                                {{ $paymentActivityDate
                                    ? $paymentActivityDate->format('d/m/Y H:i')
                                    : '-'
                                }}
                            </td>

                            <td>
                                {{ $paymentMethodLabels[$payment->payment_method]
                                    ?? ucwords(str_replace(
                                        '_',
                                        ' ',
                                        $payment->payment_method
                                    ))
                                }}
                            </td>

                            <td class="text-right number">
                                Rp {{ number_format(
                                    (float) $payment->amount,
                                    0,
                                    ',',
                                    '.'
                                ) }}
                            </td>

                            <td>
                                {{ $paymentStatusLabels[$payment->status]
                                    ?? ucfirst($payment->status)
                                }}
                            </td>

                            <td>
                                {{ $payment->verifier?->name ?? '-' }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="empty-message">
                                Tidak terdapat catatan pembayaran
                                pada periode laporan.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

            <p class="note">
                Nama pada kolom Diproses Oleh merupakan pengguna yang
                tercatat memproses status pembayaran di sistem.
            </p>

        </section>


        {{-- ================================================= --}}
        {{-- PENGESAHAN --}}
        {{-- ================================================= --}}

        <section class="report-signature">

            <div class="signature-content">

                <p>
                    Dibuat pada:
                    {{ $generatedAt->format('d/m/Y') }}
                </p>

                <p>
                    Owner LIFESTYLE,
                </p>

                <div class="signature-space"></div>

                <div class="signature-name">
                    {{ $reportPreparedBy }}
                </div>

                <div class="signature-role">
                    Owner
                </div>

            </div>

        </section>


        {{-- FOOTER --}}

        <footer class="report-footer">

            LIFESTYLE E-Commerce
            <br>

            Dokumen ini dihasilkan berdasarkan catatan aktivitas
            yang tersimpan di dalam sistem.

        </footer>

    </main>

</body>
</html>
