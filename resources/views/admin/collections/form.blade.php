<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        {{ $collection ? 'Edit Collection' : 'Add Collection' }}
    </title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link
        rel="stylesheet"
        href="{{ asset('css/admin/orders.css') }}">

    <link
        rel="stylesheet"
        href="{{ asset('css/admin/collections.css') }}">

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

            <a href="{{ route('admin.variants.index') }}">
                <i class="bi bi-boxes"></i>
                Variants
            </a>

            <a href="{{ route('admin.categories.index') }}">
                <i class="bi bi-tags"></i>
                Categories
            </a>

            <a
                href="{{ route('admin.collections.index') }}"
                class="active">

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

                    {{ $collection
                        ? 'Edit Collection'
                        : 'Add Collection' }}

                </h1>

                <p>

                    {{ $collection
                        ? 'Update collection information.'
                        : 'Create a new product collection.' }}

                </p>

            </div>


            <div class="header-icon">

                <i class="bi bi-collection"></i>

            </div>

        </div>


        @if ($errors->any())

            <div class="collection-form-error">

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
            action="{{ $collection
                ? route(
                    'admin.collections.update',
                    $collection
                )
                : route(
                    'admin.collections.store'
                ) }}"
            method="POST">

            @csrf

            @if ($collection)
                @method('PUT')
            @endif


            <section class="collection-form-card">

                <div class="collection-form-header">

                    <h2>
                        Collection Information
                    </h2>

                    <p>
                        Complete the collection information below.
                    </p>

                </div>


                <div class="collection-form-body">


                    <div class="collection-form-group">

                        <label for="name">

                            Collection Name
                            <span>*</span>

                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old(
                                'name',
                                $collection?->name
                            ) }}"
                            placeholder="Example: Weekend Essentials"
                            required>

                        @error('name')

                            <small class="collection-field-error">
                                {{ $message }}
                            </small>

                        @enderror

                    </div>


                    <div class="collection-form-group">

                        <label>
                            Status
                        </label>

                        <label class="collection-checkbox">

                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                {{ old(
                                    'is_active',
                                    $collection
                                        ? $collection->is_active
                                        : true
                                )
                                    ? 'checked'
                                    : '' }}>

                            <span>
                                Active Collection
                            </span>

                        </label>

                    </div>


                    <div class="collection-form-group collection-full">

                        <label for="description">
                            Description
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="6"
                            placeholder="Collection description...">{{ old(
                                'description',
                                $collection?->description
                            ) }}</textarea>

                    </div>


                    <div class="collection-form-group collection-full">

                        <div class="slug-info">

                            <i class="bi bi-info-circle"></i>

                            <div>

                                <strong>
                                    Collection Slug
                                </strong>

                                <span>
                                    Slug dibuat otomatis berdasarkan nama collection.
                                </span>

                                @if ($collection)

                                    <code>
                                        {{ $collection->slug }}
                                    </code>

                                @endif

                            </div>

                        </div>

                    </div>

                </div>


                <div class="collection-form-footer">

                    <a
                        href="{{ route(
                            'admin.collections.index'
                        ) }}"
                        class="collection-cancel">

                        CANCEL

                    </a>


                    <button
                        type="submit"
                        class="collection-save">

                        <i class="bi bi-check-lg"></i>

                        {{ $collection
                            ? 'UPDATE COLLECTION'
                            : 'SAVE COLLECTION' }}

                    </button>

                </div>

            </section>

        </form>

    </main>

</div>

</body>

</html>