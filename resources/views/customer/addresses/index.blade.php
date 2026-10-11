@extends('layouts.store')

@section('title', 'My Addresses')

@section('content')

<section class="addresses-page">

    <div class="addresses-container">

        {{-- =========================
            HEADER
        ========================= --}}
        <div class="addresses-header">

            <div>
                <span class="addresses-eyebrow">
                    MY ACCOUNT
                </span>

                <h1>
                    Addresses
                </h1>

                <p>
                    Manage your saved shipping addresses.
                </p>
            </div>

            <div class="addresses-header-icon">
                <i class="bi bi-geo-alt"></i>
            </div>

        </div>


        {{-- =========================
            ALERT
        ========================= --}}
        @if(session('success'))

            <div class="addresses-alert addresses-alert-success">

                <i class="bi bi-check-circle"></i>

                <span>
                    {{ session('success') }}
                </span>

            </div>

        @endif


        @if($errors->any())

            <div class="addresses-alert addresses-alert-error">

                <i class="bi bi-exclamation-circle"></i>

                <div>

                    @foreach($errors->all() as $error)

                        <div>
                            {{ $error }}
                        </div>

                    @endforeach

                </div>

            </div>

        @endif


        <div class="addresses-layout">

            {{-- =========================
                SIDEBAR
            ========================= --}}
            <aside class="addresses-sidebar">

                <div class="addresses-user-card">

                    <div class="addresses-avatar">

                        {{ strtoupper(
                            substr(
                                auth()->user()->name,
                                0,
                                1
                            )
                        ) }}

                    </div>

                    <div class="addresses-user-info">

                        <strong>
                            {{ auth()->user()->name }}
                        </strong>

                        <span>
                            {{ auth()->user()->email }}
                        </span>

                    </div>

                </div>


                <nav class="addresses-menu">

                    <a href="{{ route('customer.account') }}">

                        <i class="bi bi-person"></i>

                        Profile

                    </a>


                    <a href="{{ route('orders.index') }}">

                        <i class="bi bi-bag-check"></i>

                        My Orders

                    </a>


                    <a
                        href="{{ route('customer.addresses.index') }}"
                        class="active"
                    >

                        <i class="bi bi-geo-alt"></i>

                        Addresses

                    </a>

                </nav>


                <form
                    action="{{ route('logout') }}"
                    method="POST"
                    class="addresses-logout-form"
                >

                    @csrf

                    <button type="submit">

                        <i class="bi bi-box-arrow-right"></i>

                        Logout

                    </button>

                </form>

            </aside>


            {{-- =========================
                MAIN
            ========================= --}}
            <main class="addresses-main">

                {{-- ADD ADDRESS --}}
                <section class="addresses-card">

                    <div class="addresses-card-heading">

                        <div class="addresses-card-icon">
                            <i class="bi bi-plus-lg"></i>
                        </div>

                        <div>

                            <h2>
                                Add New Address
                            </h2>

                            <p>
                                Save a new address for faster checkout.
                            </p>

                        </div>

                    </div>


                    <form
                        action="{{ route(
                            'customer.addresses.store'
                        ) }}"
                        method="POST"
                        class="address-form"
                    >

                        @csrf


                        <div class="address-field">

                            <label for="label">
                                ADDRESS LABEL
                            </label>

                            <input
                                type="text"
                                name="label"
                                id="label"
                                value="{{ old('label') }}"
                                maxlength="50"
                                placeholder="Home, Office, Apartment..."
                            >

                        </div>


                        <div class="address-field">

                            <label for="recipient_name">
                                RECIPIENT NAME
                            </label>

                            <input
                                type="text"
                                name="recipient_name"
                                id="recipient_name"
                                value="{{ old(
                                    'recipient_name',
                                    auth()->user()->name
                                ) }}"
                                maxlength="255"
                                required
                            >

                        </div>


                        <div class="address-field">

                            <label for="phone">
                                PHONE NUMBER
                            </label>

                            <input
                                type="text"
                                name="phone"
                                id="phone"
                                value="{{ old(
                                    'phone',
                                    auth()->user()->phone
                                ) }}"
                                maxlength="20"
                                required
                            >

                        </div>


                        <div class="address-field address-field-full">

                            <label for="address">
                                FULL ADDRESS
                            </label>

                            <textarea
                                name="address"
                                id="address"
                                rows="4"
                                required
                            >{{ old('address') }}</textarea>

                        </div>


                        <div class="address-field">

                            <label for="city">
                                CITY
                            </label>

                            <input
                                type="text"
                                name="city"
                                id="city"
                                value="{{ old('city') }}"
                                maxlength="100"
                                required
                            >

                        </div>


                        <div class="address-field">

                            <label for="province">
                                PROVINCE
                            </label>

                            <input
                                type="text"
                                name="province"
                                id="province"
                                value="{{ old('province') }}"
                                maxlength="100"
                                required
                            >

                        </div>


                        <div class="address-field">

                            <label for="postal_code">
                                POSTAL CODE
                            </label>

                            <input
                                type="text"
                                name="postal_code"
                                id="postal_code"
                                value="{{ old('postal_code') }}"
                                maxlength="10"
                                required
                            >

                        </div>


                        <label class="address-default-check">

                            <input
                                type="checkbox"
                                name="is_default"
                                value="1"
                                {{ old('is_default')
                                    ? 'checked'
                                    : ''
                                }}
                            >

                            <span>
                                Set as default address
                            </span>

                        </label>


                        <button
                            type="submit"
                            class="address-save-button"
                        >

                            SAVE ADDRESS

                            <i class="bi bi-arrow-right"></i>

                        </button>

                    </form>

                </section>


                {{-- =========================
                    SAVED ADDRESSES
                ========================= --}}
                <section class="addresses-card">

                    <div class="addresses-card-heading">

                        <div class="addresses-card-icon">
                            <i class="bi bi-geo"></i>
                        </div>

                        <div>

                            <h2>
                                Saved Addresses
                            </h2>

                            <p>
                                Select, edit, or remove your saved addresses.
                            </p>

                        </div>

                    </div>


                    @if($addresses->isEmpty())

                        <div class="addresses-empty">

                            <i class="bi bi-geo-alt"></i>

                            <strong>
                                No Addresses Yet
                            </strong>

                            <p>
                                Add your first shipping address
                                using the form above.
                            </p>

                        </div>

                    @else

                        <div class="saved-addresses">

                            @foreach($addresses as $address)

                                <article
                                    class="
                                        saved-address-card
                                        {{ $address->is_default
                                            ? 'default'
                                            : ''
                                        }}
                                    "
                                >

                                    {{-- HEADER --}}
                                    <div class="saved-address-header">

                                        <div>

                                            <span class="saved-address-label">

                                                {{ $address->label
                                                    ?: 'Address'
                                                }}

                                            </span>


                                            @if($address->is_default)

                                                <span class="default-badge">
                                                    DEFAULT
                                                </span>

                                            @endif

                                        </div>


                                        <i class="bi bi-geo-alt"></i>

                                    </div>


                                    {{-- INFORMATION --}}
                                    <div class="saved-address-content">

                                        <strong>
                                            {{ $address->recipient_name }}
                                        </strong>

                                        <span>
                                            {{ $address->phone }}
                                        </span>

                                        <p>
                                            {{ $address->address }}
                                            <br>

                                            {{ $address->city }},
                                            {{ $address->province }}
                                            {{ $address->postal_code }}
                                        </p>

                                    </div>


                                    {{-- ACTIONS --}}
                                    <div class="saved-address-actions">

                                        @if(!$address->is_default)

                                            <form
                                                action="{{ route(
                                                    'customer.addresses.default',
                                                    $address
                                                ) }}"
                                                method="POST"
                                            >

                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="address-action-button"
                                                >

                                                    <i class="bi bi-check-circle"></i>

                                                    SET DEFAULT

                                                </button>

                                            </form>

                                        @endif


                                        <details class="address-edit-details">

                                            <summary
                                                class="address-action-button"
                                            >

                                                <i class="bi bi-pencil"></i>

                                                EDIT

                                            </summary>


                                            <div class="address-edit-panel">

                                                <form
                                                    action="{{ route(
                                                        'customer.addresses.update',
                                                        $address
                                                    ) }}"
                                                    method="POST"
                                                    class="address-edit-form"
                                                >

                                                    @csrf
                                                    @method('PUT')


                                                    <div class="address-field">

                                                        <label>
                                                            ADDRESS LABEL
                                                        </label>

                                                        <input
                                                            type="text"
                                                            name="label"
                                                            value="{{ $address->label }}"
                                                            maxlength="50"
                                                        >

                                                    </div>


                                                    <div class="address-field">

                                                        <label>
                                                            RECIPIENT NAME
                                                        </label>

                                                        <input
                                                            type="text"
                                                            name="recipient_name"
                                                            value="{{ $address->recipient_name }}"
                                                            maxlength="255"
                                                            required
                                                        >

                                                    </div>


                                                    <div class="address-field">

                                                        <label>
                                                            PHONE NUMBER
                                                        </label>

                                                        <input
                                                            type="text"
                                                            name="phone"
                                                            value="{{ $address->phone }}"
                                                            maxlength="20"
                                                            required
                                                        >

                                                    </div>


                                                    <div
                                                        class="
                                                            address-field
                                                            address-field-full
                                                        "
                                                    >

                                                        <label>
                                                            FULL ADDRESS
                                                        </label>

                                                        <textarea
                                                            name="address"
                                                            rows="3"
                                                            required
                                                        >{{ $address->address }}</textarea>

                                                    </div>


                                                    <div class="address-field">

                                                        <label>
                                                            CITY
                                                        </label>

                                                        <input
                                                            type="text"
                                                            name="city"
                                                            value="{{ $address->city }}"
                                                            maxlength="100"
                                                            required
                                                        >

                                                    </div>


                                                    <div class="address-field">

                                                        <label>
                                                            PROVINCE
                                                        </label>

                                                        <input
                                                            type="text"
                                                            name="province"
                                                            value="{{ $address->province }}"
                                                            maxlength="100"
                                                            required
                                                        >

                                                    </div>


                                                    <div class="address-field">

                                                        <label>
                                                            POSTAL CODE
                                                        </label>

                                                        <input
                                                            type="text"
                                                            name="postal_code"
                                                            value="{{ $address->postal_code }}"
                                                            maxlength="10"
                                                            required
                                                        >

                                                    </div>


                                                    @if(!$address->is_default)

                                                        <label
                                                            class="
                                                                address-default-check
                                                            "
                                                        >

                                                            <input
                                                                type="checkbox"
                                                                name="is_default"
                                                                value="1"
                                                            >

                                                            <span>
                                                                Set as default address
                                                            </span>

                                                        </label>

                                                    @endif


                                                    <button
                                                        type="submit"
                                                        class="address-save-edit"
                                                    >

                                                        SAVE CHANGES

                                                        <i class="bi bi-check"></i>

                                                    </button>

                                                </form>

                                            </div>

                                        </details>


                                        <form
                                            action="{{ route(
                                                'customer.addresses.destroy',
                                                $address
                                            ) }}"
                                            method="POST"
                                            onsubmit="
                                                return confirm(
                                                    'Hapus alamat ini?'
                                                );
                                            "
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="
                                                    address-action-button
                                                    address-delete-button
                                                "
                                            >

                                                <i class="bi bi-trash"></i>

                                                DELETE

                                            </button>

                                        </form>

                                    </div>

                                </article>

                            @endforeach

                        </div>

                    @endif

                </section>

            </main>

        </div>

    </div>

</section>

@endsection