<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        {{ $category ? 'Edit Category' : 'Add Category' }}
    </title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link
        rel="stylesheet"
        href="{{ asset('css/admin/orders.css') }}">

    <link
        rel="stylesheet"
        href="{{ asset('css/admin/categories.css') }}">

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

                <a href="{{ route('admin.vouchers.index') }}">
                    <i class="bi bi-ticket-perforated"></i>
                    Vouchers
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

                    {{ $category
                        ? 'Edit Category'
                        : 'Add Category' }}

                </h1>

                <p>

                    {{ $category
                        ? 'Update category information.'
                        : 'Create a new product category.' }}

                </p>

            </div>


            <div class="header-icon">

                <i class="bi bi-tags"></i>

            </div>

        </div>


        @if ($errors->any())

            <div class="category-form-error">

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
            action="{{ $category
                ? route(
                    'admin.categories.update',
                    $category
                )
                : route(
                    'admin.categories.store'
                ) }}"
            method="POST">

            @csrf

            @if ($category)

                @method('PUT')

            @endif


            <section class="category-form-card">

                <div class="category-form-header">

                    <h2>
                        Category Information
                    </h2>

                    <p>
                        Complete the category information below.
                    </p>

                </div>


                <div class="category-form-body">


                    {{-- NAME --}}
                    <div class="category-form-group">

                        <label for="name">

                            Category Name
                            <span>*</span>

                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old(
                                'name',
                                $category?->name
                            ) }}"
                            placeholder="Example: Hoodies"
                            required>

                        @error('name')

                            <small class="category-field-error">
                                {{ $message }}
                            </small>

                        @enderror

                    </div>


                    {{-- STATUS --}}
                    <div class="category-form-group">

                        <label>
                            Status
                        </label>

                        <label class="category-checkbox">

                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                {{ old(
                                    'is_active',
                                    $category
                                        ? $category->is_active
                                        : true
                                )
                                    ? 'checked'
                                    : '' }}>

                            <span>
                                Active Category
                            </span>

                        </label>

                        <small class="category-help">

                            Inactive categories will not be shown
                            in active category selections.

                        </small>

                    </div>


                    {{-- DESCRIPTION --}}
                    <div class="category-form-group category-full">

                        <label for="description">
                            Description
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="6"
                            placeholder="Category description...">{{ old(
                                'description',
                                $category?->description
                            ) }}</textarea>

                    </div>


                    {{-- SLUG INFO --}}
                    <div class="category-form-group category-full">

                        <div class="slug-info">

                            <i class="bi bi-info-circle"></i>

                            <div>

                                <strong>
                                    Category Slug
                                </strong>

                                <span>

                                    Slug akan dibuat otomatis
                                    berdasarkan nama category.

                                </span>

                                @if ($category)

                                    <code>
                                        {{ $category->slug }}
                                    </code>

                                @endif

                            </div>

                        </div>

                    </div>

                </div>


                <div class="category-form-footer">

                    <a
                        href="{{ route(
                            'admin.categories.index'
                        ) }}"
                        class="category-cancel">

                        CANCEL

                    </a>


                    <button
                        type="submit"
                        class="category-save">

                        <i class="bi bi-check-lg"></i>

                        {{ $category
                            ? 'UPDATE CATEGORY'
                            : 'SAVE CATEGORY' }}

                    </button>

                </div>

            </section>

        </form>

    </main>

</div>

</body>

</html>