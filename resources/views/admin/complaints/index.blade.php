
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Order Complaints | LIFESTYLE Admin</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f6f8;
            color: #202124;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button,
        input,
        select,
        textarea {
            font: inherit;
        }

        .admin-layout {
            min-height: 100vh;
        }

        .admin-sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 245px;
            padding: 28px 18px;
            background: #17191d;
            color: #fff;
            overflow-y: auto;
        }

        .brand {
            padding: 0 12px 26px;
            border-bottom: 1px solid #34363b;
        }

        .brand h2 {
            margin: 0 0 7px;
            font-size: 22px;
            letter-spacing: 2px;
        }

        .brand p {
            margin: 0;
            color: #a9abb1;
            font-size: 12px;
        }

        .nav-label {
            margin: 27px 12px 12px;
            color: #92959c;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1.3px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 5px;
            padding: 12px;
            border-radius: 7px;
            color: #d6d7da;
            font-size: 13px;
        }

        .nav-link i {
            font-size: 17px;
        }

        .nav-link:hover,
        .nav-link.active {
            background: #30343b;
            color: #fff;
        }

        .admin-main {
            margin-left: 245px;
            padding: 34px;
            max-width: 1700px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 28px;
        }

        .eyebrow {
            color: #777b83;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1.5px;
        }

        .page-header h1 {
            margin: 9px 0;
            font-size: 29px;
        }

        .page-header p {
            margin: 0;
            color: #747982;
            font-size: 14px;
            line-height: 1.6;
        }

        .button {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            padding: 11px 15px;
            border: 0;
            border-radius: 7px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
        }

        .button-dark {
            background: #22252b;
            color: #fff;
        }

        .button-light {
            background: #eceef1;
            color: #25282d;
        }

        .button:hover {
            opacity: .86;
        }

        .alert {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 7px;
            font-size: 13px;
            line-height: 1.5;
        }

        .alert-success {
            background: #e8f5ec;
            color: #23683c;
        }

        .alert-error {
            background: #fff0ef;
            color: #a12b26;
        }

        .filter-card,
        .complaint-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .025);
        }

        .filter-card {
            margin-bottom: 22px;
            padding: 20px;
        }

        .filter-card h2 {
            margin: 0 0 16px;
            font-size: 15px;
        }

        .filter-form {
            display: flex;
            align-items: end;
            flex-wrap: wrap;
            gap: 12px;
        }

        .field {
            display: flex;
            flex: 1;
            flex-direction: column;
            gap: 7px;
            min-width: 180px;
        }

        .field label {
            color: #555a62;
            font-size: 12px;
            font-weight: bold;
        }

        .field select,
        .field input,
        .field textarea {
            width: 100%;
            padding: 10px 11px;
            border: 1px solid #d9dce1;
            border-radius: 6px;
            background: #fff;
            color: #26282d;
            font-size: 13px;
        }

        .field textarea {
            min-height: 95px;
            resize: vertical;
            line-height: 1.5;
        }

        .complaint-list {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .complaint-card {
            overflow: hidden;
        }

        .complaint-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
            padding: 19px 21px;
            border-bottom: 1px solid #eceef1;
            background: #fcfcfd;
        }

        .order-code {
            font-size: 15px;
            font-weight: bold;
        }

        .muted {
            color: #737780;
            font-size: 12px;
            line-height: 1.7;
        }

        .badge {
            display: inline-block;
            padding: 6px 9px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            white-space: nowrap;
        }

        .badge-open {
            background: #fff0d8;
            color: #8b5700;
        }

        .badge-investigating {
            background: #e5efff;
            color: #245db2;
        }

        .badge-resolved {
            background: #e3f5e9;
            color: #226c3e;
        }

        .badge-rejected {
            background: #fce8e8;
            color: #a22b2b;
        }

        .complaint-body {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(270px, .85fr);
            gap: 25px;
            padding: 22px;
        }

        .section-label {
            margin: 0 0 12px;
            color: #777b83;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .detail-value {
            margin-bottom: 17px;
            font-size: 13px;
            line-height: 1.7;
            overflow-wrap: anywhere;
        }

        .detail-value strong {
            display: block;
            margin-bottom: 3px;
            font-size: 12px;
        }

        .message-box {
            padding: 13px;
            border: 1px solid #eceef1;
            border-radius: 7px;
            background: #fafbfc;
            font-size: 13px;
            line-height: 1.7;
            overflow-wrap: anywhere;
            white-space: pre-line;
        }

        .admin-form {
            display: flex;
            flex-direction: column;
            gap: 13px;
        }

        .admin-form h3 {
            margin: 0 0 3px;
            font-size: 15px;
        }

        .admin-response {
            padding: 12px;
            border-left: 3px solid #b8bdc5;
            background: #f7f8fa;
            font-size: 12px;
            line-height: 1.7;
            white-space: pre-line;
            overflow-wrap: anywhere;
        }

        .empty-state {
            padding: 55px 20px;
            text-align: center;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
        }

        .empty-state i {
            color: #9da2ab;
            font-size: 35px;
        }

        .empty-state h3 {
            margin: 15px 0 7px;
            font-size: 17px;
        }

        .empty-state p {
            margin: 0;
            color: #737780;
            font-size: 13px;
        }

        .pagination {
            margin-top: 22px;
        }

        .pagination nav p {
            font-size: 12px;
            color: #737780;
        }

        @media (max-width: 950px) {
            .admin-sidebar {
                position: static;
                width: 100%;
            }

            .admin-main {
                margin-left: 0;
                padding: 22px 16px;
            }

            .complaint-body {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 550px) {
            .page-header {
                flex-direction: column;
            }

            .complaint-header {
                flex-direction: column;
            }

            .filter-form {
                align-items: stretch;
                flex-direction: column;
            }

            .field {
                min-width: 0;
            }
        }
    </style>
</head>

<body>
<div class="admin-layout">

    <aside class="admin-sidebar">
        <div class="brand">
            <h2>LIFESTYLE</h2>
            <p>Administration Panel</p>
        </div>

        <div class="nav-label">MAIN MENU</div>

        <a href="{{ route('admin.dashboard') }}" class="nav-link">
            <i class="bi bi-grid"></i>
            Dashboard
        </a>

        <a href="{{ route('admin.orders.index') }}" class="nav-link">
            <i class="bi bi-bag"></i>
            Order Management
        </a>

        <a href="{{ route('admin.complaints.index') }}" class="nav-link active">
            <i class="bi bi-chat-square-text"></i>
            Order Complaints
        </a>

        <a href="{{ route('admin.payments.index') }}" class="nav-link">
            <i class="bi bi-credit-card"></i>
            Payments
        </a>

        <a href="{{ route('admin.products.index') }}" class="nav-link">
            <i class="bi bi-box-seam"></i>
            Products
        </a>

        <a href="{{ route('admin.vouchers.index') }}" class="nav-link">
            <i class="bi bi-ticket-perforated"></i>
            Vouchers
        </a>
    </aside>

    <main class="admin-main">

        <header class="page-header">
            <div>
                <div class="eyebrow">CUSTOMER SERVICE</div>
                <h1>Order Complaints</h1>
                <p>
                    Tinjau laporan pelanggan, catat tindak lanjut,
                    dan perbarui status penyelesaian masalah pesanan.
                </p>
            </div>

            <a href="{{ route('admin.orders.index') }}" class="button button-light">
                <i class="bi bi-arrow-left"></i>
                Semua Pesanan
            </a>
        </header>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <i class="bi bi-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="filter-card">
            <h2>
                <i class="bi bi-funnel"></i>
                Filter Komplain
            </h2>

            <form
                action="{{ route('admin.complaints.index') }}"
                method="GET"
                class="filter-form">

                <div class="field">
                    <label for="status">Status Penanganan</label>

                    <select name="status" id="status">
                        <option value="">Semua Status</option>
                        <option value="open" @selected(request('status') === 'open')>
                            Open
                        </option>
                        <option value="investigating" @selected(request('status') === 'investigating')>
                            Investigating
                        </option>
                        <option value="resolved" @selected(request('status') === 'resolved')>
                            Resolved
                        </option>
                        <option value="rejected" @selected(request('status') === 'rejected')>
                            Rejected
                        </option>
                    </select>
                </div>

                <button type="submit" class="button button-dark">
                    <i class="bi bi-search"></i>
                    Terapkan Filter
                </button>

                <a
                    href="{{ route('admin.complaints.index') }}"
                    class="button button-light">
                    Reset
                </a>
            </form>
        </section>

        @if($complaints->count() > 0)

            <div class="complaint-list">

                @foreach($complaints as $complaint)

                    @php
                        $statusLabels = [
                            'open' => 'Open',
                            'investigating' => 'Sedang Diselidiki',
                            'resolved' => 'Selesai',
                            'rejected' => 'Ditolak',
                        ];

                        $categoryLabels = [
                            'parcel_not_received' => 'Paket Belum Diterima',
                            'damaged' => 'Barang Rusak',
                            'wrong_item' => 'Barang Tidak Sesuai',
                            'other' => 'Masalah Lainnya',
                        ];

                        $status = $complaint->status;
                        $statusLabel = $statusLabels[$status] ?? ucfirst($status);
                        $categoryLabel = $categoryLabels[$complaint->category]
                            ?? ucfirst(str_replace('_', ' ', $complaint->category));
                    @endphp

                    <article class="complaint-card">

                        <div class="complaint-header">
                            <div>
                                <div class="order-code">
                                    Komplain #{{ $complaint->id }}
                                </div>

                                <div class="muted">
                                    Pesanan:
                                    @if($complaint->order)
                                        <a
                                            href="{{ route('admin.orders.show', $complaint->order) }}"
                                            style="text-decoration: underline; font-weight: bold;">
                                            {{ $complaint->order->order_code }}
                                        </a>
                                    @else
                                        Pesanan tidak tersedia
                                    @endif
                                </div>

                                <div class="muted">
                                    Dikirim pada:
                                    {{ $complaint->created_at?->format('d M Y, H:i') ?? '-' }}
                                </div>
                            </div>

                            <span class="badge badge-{{ $status }}">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        <div class="complaint-body">

                            <section>
                                <h3 class="section-label">INFORMASI LAPORAN</h3>

                                <div class="detail-value">
                                    <strong>Customer</strong>
                                    {{ $complaint->user?->name ?? 'Pengguna tidak tersedia' }}
                                </div>

                                <div class="detail-value">
                                    <strong>Kategori</strong>
                                    {{ $categoryLabel }}
                                </div>

                                <div class="detail-value">
                                    <strong>Isi Komplain</strong>

                                    <div class="message-box">{{ $complaint->message }}</div>
                                </div>

                                @if($complaint->order)
                                    <div class="detail-value">
                                        <strong>Status Pesanan</strong>
                                        {{ ucfirst(str_replace('_', ' ', $complaint->order->status)) }}
                                    </div>
                                @endif

                                @if($complaint->handledBy)
                                    <div class="detail-value">
                                        <strong>Ditangani oleh</strong>
                                        {{ $complaint->handledBy->name }}
                                    </div>
                                @endif

                                @if($complaint->resolved_at)
                                    <div class="detail-value">
                                        <strong>Ditutup pada</strong>
                                        {{ $complaint->resolved_at->format('d M Y, H:i') }}
                                    </div>
                                @endif
                            </section>

                            <section>
                                <form
                                    action="{{ route('admin.complaints.update', $complaint) }}"
                                    method="POST"
                                    class="admin-form">

                                    @csrf
                                    @method('PUT')

                                    <h3>Tindak Lanjut Admin</h3>

                                    <div class="field">
                                        <label for="status-{{ $complaint->id }}">
                                            Status Komplain
                                        </label>

                                        <select
                                            name="status"
                                            id="status-{{ $complaint->id }}"
                                            required>

                                            <option value="open" @selected($status === 'open')>
                                                Open — Belum Ditangani
                                            </option>

                                            <option value="investigating" @selected($status === 'investigating')>
                                                Investigating — Sedang Diperiksa
                                            </option>

                                            <option value="resolved" @selected($status === 'resolved')>
                                                Resolved — Masalah Selesai
                                            </option>

                                            <option value="rejected" @selected($status === 'rejected')>
                                                Rejected — Laporan Ditolak
                                            </option>
                                        </select>
                                    </div>

                                    <div class="field">
                                        <label for="response-{{ $complaint->id }}">
                                            Tanggapan Admin
                                        </label>

                                        <textarea
                                            name="admin_response"
                                            id="response-{{ $complaint->id }}"
                                            maxlength="5000"
                                            placeholder="Catat hasil pemeriksaan atau tindak lanjut ke Staff/Kurir...">{{ old('admin_response', $complaint->admin_response) }}</textarea>
                                    </div>

                                    <button type="submit" class="button button-dark">
                                        <i class="bi bi-save"></i>
                                        Simpan Tindak Lanjut
                                    </button>

                                </form>

                                @if($complaint->admin_response)
                                    <div style="margin-top: 22px;">
                                        <h3 class="section-label">TANGGAPAN TERSIMPAN</h3>

                                        <div class="admin-response">{{ $complaint->admin_response }}</div>
                                    </div>
                                @endif
                            </section>

                        </div>
                    </article>

                @endforeach

            </div>

            <div class="pagination">
                {{ $complaints->links() }}
            </div>

        @else

            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h3>Belum Ada Komplain</h3>
                <p>
                    Belum ada laporan yang cocok dengan filter yang dipilih.
                </p>
            </div>

        @endif

    </main>
</div>
</body>
</html>
