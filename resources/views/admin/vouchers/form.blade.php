<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        {{ $voucher ? 'Edit Voucher' : 'Add Voucher' }}
    </title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link
        rel="stylesheet"
        href="{{ asset('css/admin/orders.css') }}">

    <link
        rel="stylesheet"
        href="{{ asset('css/admin/vouchers.css') }}">

</head>

<body>

    <div class="admin-layout">

        <aside class="admin-sidebar">

            <div class="sidebar-brand">
                <h2>LIFESTYLE</h2>
                <span>ADMIN PANEL</span>
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


        <main class="admin-main">

            <div class="admin-header">

                <div>

                    <span class="page-label">
                        ADMIN
                    </span>

                    <h1>

                        {{ $voucher
                        ? 'Edit Voucher'
                        : 'Add Voucher' }}

                    </h1>

                    <p>

                        {{ $voucher
                        ? 'Update voucher configuration.'
                        : 'Create a new discount voucher.' }}

                    </p>

                </div>

                <div class="header-icon">

                    <i class="bi bi-ticket-perforated"></i>

                </div>

            </div>


            @if ($errors->any())

            <div class="voucher-form-error">

                <strong>
                    Please check your input.
                </strong>

                <ul>

                    @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                    @endforeach

                </ul>

            </div>

            @endif


            <form
                action="{{ $voucher
                ? route(
                    'admin.vouchers.update',
                    $voucher
                )
                : route(
                    'admin.vouchers.store'
                ) }}"
                method="POST">

                @csrf

                @if ($voucher)
                @method('PUT')
                @endif


                <section class="voucher-form-card">

                    <div class="voucher-form-header">

                        <h2>
                            Voucher Information
                        </h2>

                        <p>
                            Complete the voucher configuration below.
                        </p>

                    </div>


                    <div class="voucher-form-body">


                        <div class="voucher-form-group">

                            <label for="code">

                                Voucher Code
                                <span>*</span>

                            </label>

                            <input
                                id="code"
                                type="text"
                                name="code"
                                value="{{ old(
                                'code',
                                $voucher?->code
                            ) }}"
                                placeholder="Example: SAVE20"
                                required>

                            <small>
                                Code will be stored in uppercase.
                            </small>

                        </div>


                        <div class="voucher-form-group">

                            <label for="name">

                                Voucher Name
                                <span>*</span>

                            </label>

                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old(
                                'name',
                                $voucher?->name
                            ) }}"
                                placeholder="Example: Weekend Discount"
                                required>

                        </div>


                        <div class="voucher-form-group">

                            <label for="discount_type">

                                Discount Type
                                <span>*</span>

                            </label>

                            <select
                                id="discount_type"
                                name="discount_type"
                                required>

                                <option value="">
                                    Select discount type
                                </option>

                                <option
                                    value="percentage"
                                    {{ old(
                                    'discount_type',
                                    $voucher?->discount_type
                                ) === 'percentage'
                                    ? 'selected'
                                    : '' }}>

                                    Percentage (%)

                                </option>

                                <option
                                    value="fixed"
                                    {{ old(
                                    'discount_type',
                                    $voucher?->discount_type
                                ) === 'fixed'
                                    ? 'selected'
                                    : '' }}>

                                    Fixed Amount (Rp)

                                </option>

                            </select>

                        </div>


                        <div class="voucher-form-group">

                            <label for="discount_value">

                                Discount Value
                                <span>*</span>

                            </label>

                            <input
                                id="discount_value"
                                type="number"
                                name="discount_value"
                                min="0"
                                step="0.01"
                                value="{{ old(
                                'discount_value',
                                $voucher?->discount_value
                            ) }}"
                                placeholder="Example: 20"
                                required>

                            <small>
                                Percentage uses 0–100. Fixed uses Rupiah value.
                            </small>

                        </div>


                        <div class="voucher-form-group">

                            <label for="min_order_amount">
                                Minimum Order Amount
                            </label>

                            <div class="money-input">

                                <span>
                                    Rp
                                </span>

                                <input
                                    id="min_order_amount"
                                    type="number"
                                    name="min_order_amount"
                                    min="0"
                                    step="1"
                                    value="{{ old(
                                    'min_order_amount',
                                    $voucher?->min_order_amount ?? 0
                                ) }}"
                                    placeholder="0">

                            </div>

                        </div>


                        <div class="voucher-form-group">

                            <label for="max_discount_amount">
                                Maximum Discount Amount
                            </label>

                            <div class="money-input">

                                <span>
                                    Rp
                                </span>

                                <input
                                    id="max_discount_amount"
                                    type="number"
                                    name="max_discount_amount"
                                    min="0"
                                    step="1"
                                    value="{{ old(
                                    'max_discount_amount',
                                    $voucher?->max_discount_amount
                                ) }}"
                                    placeholder="Optional">

                            </div>

                            <small>
                                Useful for limiting percentage discounts.
                            </small>

                        </div>


                        <div class="voucher-form-group">

                            <label for="usage_limit">
                                Usage Limit
                            </label>

                            <input
                                id="usage_limit"
                                type="number"
                                name="usage_limit"
                                min="1"
                                step="1"
                                value="{{ old(
                                'usage_limit',
                                $voucher?->usage_limit
                            ) }}"
                                placeholder="Unlimited if empty">

                        </div>


                        <div class="voucher-form-group">

                            <label>
                                Used Count
                            </label>

                            <div class="voucher-readonly">

                                {{ $voucher
                                ? $voucher->used_count
                                : 0 }}

                            </div>

                            <small>
                                Updated automatically by the system.
                            </small>

                        </div>


                        <div class="voucher-form-group">

                            <label for="starts_at">
                                Start Date & Time
                            </label>

                            <input
                                id="starts_at"
                                type="datetime-local"
                                name="starts_at"
                                value="{{ old(
                                'starts_at',
                                $voucher?->starts_at
                                    ? $voucher->starts_at->format('Y-m-d\TH:i')
                                    : ''
                            ) }}">

                        </div>


                        <div class="voucher-form-group">

                            <label for="expires_at">
                                Expiry Date & Time
                            </label>

                            <input
                                id="expires_at"
                                type="datetime-local"
                                name="expires_at"
                                value="{{ old(
                                'expires_at',
                                $voucher?->expires_at
                                    ? $voucher->expires_at->format('Y-m-d\TH:i')
                                    : ''
                            ) }}">

                        </div>


                        <div class="voucher-form-group voucher-full">

                            <label>
                                Status
                            </label>

                            <label class="voucher-checkbox">

                                <input
                                    type="checkbox"
                                    name="is_active"
                                    value="1"
                                    {{ old(
                                    'is_active',
                                    $voucher
                                        ? $voucher->is_active
                                        : true
                                )
                                    ? 'checked'
                                    : '' }}>

                                <span>
                                    Active Voucher
                                </span>

                            </label>

                            <small>
                                Inactive vouchers cannot be used during checkout.
                            </small>

                        </div>


                    </div>


                    <div class="voucher-form-footer">

                        <a
                            href="{{ route(
                            'admin.vouchers.index'
                        ) }}"
                            class="voucher-cancel">

                            CANCEL

                        </a>


                        <button
                            type="submit"
                            class="voucher-save">

                            <i class="bi bi-check-lg"></i>

                            {{ $voucher
                            ? 'UPDATE VOUCHER'
                            : 'SAVE VOUCHER' }}

                        </button>

                    </div>

                </section>

            </form>

        </main>

    </div>

</body>

</html>