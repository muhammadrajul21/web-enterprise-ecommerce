<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        {{ $product ? 'Edit Product' : 'Add Product' }}
    </title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link
        rel="stylesheet"
        href="{{ asset('css/admin/orders.css') }}">

    <link
        rel="stylesheet"
        href="{{ asset('css/admin/products.css') }}">

</head>

<body>

    <div class="admin-layout">

        {{-- SIDEBAR --}}
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


                <a
                    href="{{ route('admin.products.index') }}"
                    class="active">

                    <i class="bi bi-box-seam"></i>

                    Products

                </a>

                <a href="{{ route('admin.variants.index') }}">

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


        {{-- MAIN --}}
        <main class="admin-main">


            <div class="admin-header">

                <div>

                    <span class="page-label">
                        ADMIN
                    </span>

                    <h1>

                        {{ $product
                        ? 'Edit Product'
                        : 'Add Product' }}

                    </h1>

                    <p>

                        {{ $product
                        ? 'Update product information and catalog settings.'
                        : 'Create a new product for the catalog.' }}

                    </p>

                </div>


                <div class="header-icon">

                    <i class="bi bi-box-seam"></i>

                </div>

            </div>


            @if ($errors->any())

            <div class="product-error">

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
                action="{{ $product
                ? route(
                    'admin.products.update',
                    $product
                )
                : route(
                    'admin.products.store'
                ) }}"
                method="POST"
                enctype="multipart/form-data">

                @csrf

                @if ($product)
                @method('PUT')
                @endif


                <section class="product-form-card">

                    <div class="product-form-header">

                        <div>

                            <h2>
                                Product Information
                            </h2>

                            <p>
                                Complete the information below.
                            </p>

                        </div>

                    </div>


                    <div class="product-form-body">


                        {{-- NAME --}}
                        <div class="form-group form-full">

                            <label for="name">
                                Product Name
                                <span>*</span>
                            </label>

                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old(
                                'name',
                                $product?->name
                            ) }}"
                                placeholder="Example: Oversized T-Shirt"
                                required>

                            @error('name')

                            <small class="field-error">
                                {{ $message }}
                            </small>

                            @enderror

                        </div>


                        {{-- SEGMENT --}}
                        <div class="form-group">

                            <label for="segment_id">
                                Segment
                            </label>

                            <select
                                id="segment_id"
                                name="segment_id">

                                <option value="">
                                    No Segment
                                </option>

                                @foreach ($segments as $segment)

                                <option
                                    value="{{ $segment->id }}"
                                    {{ (string) old(
                                        'segment_id',
                                        $product?->segment_id
                                    )
                                    ===
                                    (string) $segment->id
                                        ? 'selected'
                                        : '' }}>

                                    {{ $segment->name }}

                                </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- CATEGORY --}}
                        <div class="form-group">

                            <label for="category_id">

                                Category
                                <span>*</span>

                            </label>

                            <select
                                id="category_id"
                                name="category_id"
                                required>

                                <option value="">
                                    Select Category
                                </option>

                                @foreach ($categories as $category)

                                <option
                                    value="{{ $category->id }}"
                                    {{ (string) old(
                                        'category_id',
                                        $product?->category_id
                                    )
                                    ===
                                    (string) $category->id
                                        ? 'selected'
                                        : '' }}>

                                    {{ $category->name }}

                                </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- STATUS --}}
                        <div class="form-group">

                            <label for="status">

                                Status
                                <span>*</span>

                            </label>

                            <select
                                id="status"
                                name="status"
                                required>

                                <option
                                    value="draft"
                                    {{ old(
                                    'status',
                                    $product?->status ?? 'draft'
                                ) === 'draft'
                                    ? 'selected'
                                    : '' }}>

                                    Draft

                                </option>


                                <option
                                    value="active"
                                    {{ old(
                                    'status',
                                    $product?->status
                                ) === 'active'
                                    ? 'selected'
                                    : '' }}>

                                    Active

                                </option>


                                <option
                                    value="inactive"
                                    {{ old(
                                    'status',
                                    $product?->status
                                ) === 'inactive'
                                    ? 'selected'
                                    : '' }}>

                                    Inactive

                                </option>

                            </select>

                        </div>


                        {{-- FEATURED --}}
                        <div class="form-group">

                            <label>
                                Featured Product
                            </label>

                            <label class="checkbox-option">

                                <input
                                    type="checkbox"
                                    name="is_featured"
                                    value="1"
                                    {{ old(
                                    'is_featured',
                                    $product?->is_featured
                                )
                                    ? 'checked'
                                    : '' }}>

                                <span>
                                    Display as featured product
                                </span>

                            </label>

                        </div>


                        {{-- DESCRIPTION --}}
                        <div class="form-group form-full">

                            <label for="description">
                                Description
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                rows="6"
                                placeholder="Product description...">{{ old(
                                'description',
                                $product?->description
                            ) }}</textarea>

                        </div>


                        {{-- COLLECTIONS --}}
                        <div class="form-group form-full">

                            <label>
                                Collections
                            </label>

                            @php

                            $selectedCollections =
                            old(
                            'collections',
                            $product
                            ? $product
                            ->collections
                            ->pluck('id')
                            ->all()
                            : []
                            );

                            @endphp


                            <div class="collection-options">

                                @forelse (
                                $collections
                                as $collection
                                )

                                <label class="collection-option">

                                    <input
                                        type="checkbox"
                                        name="collections[]"
                                        value="{{ $collection->id }}"
                                        {{ in_array(
                                            $collection->id,
                                            $selectedCollections
                                        )
                                            ? 'checked'
                                            : '' }}>

                                    <span>
                                        {{ $collection->name }}
                                    </span>

                                </label>

                                @empty

                                <span class="table-muted">
                                    No active collections.
                                </span>

                                @endforelse

                            </div>

                        </div>


                        {{-- IMAGE --}}
                        <div class="form-group form-full">

                            <label for="image">
                                Primary Product Image
                            </label>


                            @if (
                            $product
                            &&
                            $product
                            ->images
                            ->where(
                            'is_primary',
                            true
                            )
                            ->first()
                            )

                            @php

                            $currentImage =
                            $product
                            ->images
                            ->where(
                            'is_primary',
                            true
                            )
                            ->first();

                            @endphp


                            <div class="current-product-image">

                                <img
                                    src="{{ asset(
                                        $currentImage->image_path
                                    ) }}"
                                    alt="{{ $product->name }}">

                                <div>

                                    <strong>
                                        Current Image
                                    </strong>

                                    <span>
                                        Upload a new image to replace it.
                                    </span>

                                </div>

                            </div>

                            @endif


                            <input
                                id="image"
                                type="file"
                                name="image"
                                accept=".jpg,.jpeg,.png,.webp">


                            <small class="form-help">

                                JPG, JPEG, PNG or WEBP.
                                Maximum 2 MB.

                            </small>

                        </div>

                    </div>


                    <div class="product-form-footer">

                        <a
                            href="{{ route(
                            'admin.products.index'
                        ) }}"
                            class="form-cancel-button">

                            CANCEL

                        </a>


                        <button
                            type="submit"
                            class="form-save-button">

                            <i class="bi bi-check-lg"></i>

                            {{ $product
                            ? 'UPDATE PRODUCT'
                            : 'SAVE PRODUCT' }}

                        </button>

                    </div>

                </section>

            </form>

        </main>

    </div>

</body>

</html>