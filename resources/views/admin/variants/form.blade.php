<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        {{ $variant ? 'Edit Variant' : 'Add Variant' }}
    </title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link
        rel="stylesheet"
        href="{{ asset('css/admin/orders.css') }}">

    <link
        rel="stylesheet"
        href="{{ asset('css/admin/variants.css') }}">

</head>

<body>

    <div class="admin-layout">

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

                <a href="{{ route('admin.products.index') }}">

                    <i class="bi bi-box-seam"></i>

                    Products

                </a>

                <a
                    href="{{ route('admin.variants.index') }}"
                    class="active">

                    <i class="bi bi-boxes"></i>

                    Variants

                </a>

                <a href="{{ route('admin.categories.index') }}">
                    <i class="bi bi-tags"></i>
                    Categories
                </a>

                <a href="{{ route('admin.collections.index') }}">
                    <i class="bi bi-collection"></i>
                    Collections
                </a>

                <a href="{{ route('admin.orders.index') }}">

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


        <main class="admin-main">

            <div class="admin-header">

                <div>

                    <span class="page-label">
                        ADMIN
                    </span>

                    <h1>

                        {{ $variant
                        ? 'Edit Variant'
                        : 'Add Variant' }}

                    </h1>

                    <p>

                        {{ $variant
                        ? 'Update SKU, color, size, price and variant status.'
                        : 'Create a new variant for an existing product.' }}

                    </p>

                </div>


                <div class="header-icon">

                    <i class="bi bi-boxes"></i>

                </div>

            </div>


            @if ($errors->any())

            <div class="variant-error">

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
                action="{{ $variant
                ? route(
                    'admin.variants.update',
                    $variant
                )
                : route(
                    'admin.variants.store'
                ) }}"
                method="POST">

                @csrf

                @if ($variant)

                @method('PUT')

                @endif


                <section class="variant-form-card">

                    <div class="variant-form-header">

                        <h2>
                            Variant Information
                        </h2>

                        <p>
                            Complete the product variant information below.
                        </p>

                    </div>


                    <div class="variant-form-body">


                        {{-- PRODUCT --}}
                        <div class="variant-form-group variant-full">

                            <label for="product_id">

                                Product
                                <span>*</span>

                            </label>

                            <select
                                id="product_id"
                                name="product_id"
                                required>

                                <option value="">
                                    Select Product
                                </option>

                                @foreach ($products as $product)

                                <option
                                    value="{{ $product->id }}"
                                    {{ (string) old(
                                        'product_id',
                                        $variant?->product_id
                                    )
                                    ===
                                    (string) $product->id
                                        ? 'selected'
                                        : '' }}>

                                    {{ $product->name }}

                                </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- SKU --}}
                        <div class="variant-form-group">

                            <label for="sku">

                                SKU
                                <span>*</span>

                            </label>

                            <input
                                id="sku"
                                type="text"
                                name="sku"
                                value="{{ old(
                                'sku',
                                $variant?->sku
                            ) }}"
                                placeholder="Example: HD-BLK-M"
                                required>

                            @error('sku')

                            <small class="variant-field-error">
                                {{ $message }}
                            </small>

                            @enderror

                        </div>


                        {{-- COLOR --}}
                        <div class="variant-form-group">

                            <label for="color">
                                Color
                            </label>

                            <input
                                id="color"
                                type="text"
                                name="color"
                                value="{{ old(
                                'color',
                                $variant?->color
                            ) }}"
                                placeholder="Example: Black">

                        </div>


                        {{-- SIZE --}}
                        <div class="variant-form-group">

                            <label for="size">
                                Size
                            </label>

                            <input
                                id="size"
                                type="text"
                                name="size"
                                value="{{ old(
                                'size',
                                $variant?->size
                            ) }}"
                                placeholder="Example: M">

                        </div>


                        {{-- PRICE --}}
                        <div class="variant-form-group">

                            <label for="price">

                                Price
                                <span>*</span>

                            </label>

                            <div class="price-input">

                                <span>
                                    Rp
                                </span>

                                <input
                                    id="price"
                                    type="number"
                                    name="price"
                                    min="0"
                                    step="1"
                                    value="{{ old(
                                    'price',
                                    $variant?->price
                                ) }}"
                                    placeholder="299000"
                                    required>

                            </div>

                        </div>


                        {{-- STATUS --}}
                        <div class="variant-form-group">

                            <label for="status">

                                Status
                                <span>*</span>

                            </label>

                            <select
                                id="status"
                                name="status"
                                required>

                                <option
                                    value="active"
                                    {{ old(
                                    'status',
                                    $variant?->status ?? 'active'
                                ) === 'active'
                                    ? 'selected'
                                    : '' }}>

                                    Active

                                </option>

                                <option
                                    value="inactive"
                                    {{ old(
                                    'status',
                                    $variant?->status
                                ) === 'inactive'
                                    ? 'selected'
                                    : '' }}>

                                    Inactive

                                </option>

                            </select>

                        </div>


                        {{-- STOCK --}}
                        <div class="variant-form-group">

                            <label>
                                Current Stock
                            </label>

                            <div class="stock-readonly">

                                <strong>

                                    {{ $variant
                                    ? $variant->stock
                                    : 0 }}

                                </strong>

                                <span>
                                    units
                                </span>

                            </div>

                            <small class="variant-help">

                                Stock is managed by Staff Gudang.

                            </small>

                        </div>

                    </div>


                    <div class="variant-form-footer">

                        <a
                            href="{{ route(
                            'admin.variants.index'
                        ) }}"
                            class="variant-cancel">

                            CANCEL

                        </a>


                        <button
                            type="submit"
                            class="variant-save">

                            <i class="bi bi-check-lg"></i>

                            {{ $variant
                            ? 'UPDATE VARIANT'
                            : 'SAVE VARIANT' }}

                        </button>

                    </div>

                </section>

            </form>

        </main>

    </div>

</body>

</html>