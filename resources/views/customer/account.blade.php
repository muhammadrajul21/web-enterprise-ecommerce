@extends('layouts.store')

@section('title', 'My Account')

@section('content')

<section class="account-page">

    <div class="account-container">

        {{-- HEADER --}}
        <div class="account-header">

            <div>

                <span class="account-eyebrow">
                    MY ACCOUNT
                </span>

                <h1>
                    Profile
                </h1>

                <p>
                    Manage your personal information
                    and account details.
                </p>

            </div>


            <div class="account-header-icon">
                <i class="bi bi-person"></i>
            </div>

        </div>


        {{-- SUCCESS --}}
        @if(session('success'))

            <div class="account-alert account-alert-success">

                <i class="bi bi-check-circle"></i>

                <span>
                    {{ session('success') }}
                </span>

            </div>

        @endif


        {{-- VALIDATION ERROR --}}
        @if($errors->any())

            <div class="account-alert account-alert-error">

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


        <div class="account-layout">

            {{-- SIDEBAR --}}
            <aside class="account-sidebar">

                <div class="account-user-card">

                    <div class="account-avatar">

                        {{ strtoupper(
                            substr(
                                $user->name,
                                0,
                                1
                            )
                        ) }}

                    </div>


                    <div class="account-user-info">

                        <strong>
                            {{ $user->name }}
                        </strong>

                        <span>
                            {{ $user->email }}
                        </span>

                    </div>

                </div>


                <nav class="account-menu">

                    <a
                        href="{{ route('customer.account') }}"
                        class="active"
                    >
                        <i class="bi bi-person"></i>
                        Profile
                    </a>


                    <a href="{{ route('orders.index') }}">

                        <i class="bi bi-bag-check"></i>
                        My Orders

                    </a>


                    <span class="account-menu-disabled">

                        <i class="bi bi-geo-alt"></i>
                        Addresses

                    </span>

                </nav>


                <form
                    action="{{ route('logout') }}"
                    method="POST"
                    class="account-logout-form"
                >

                    @csrf

                    <button type="submit">

                        <i class="bi bi-box-arrow-right"></i>
                        Logout

                    </button>

                </form>

            </aside>


            {{-- MAIN --}}
            <main class="account-main">

                {{-- PERSONAL INFO --}}
                <section class="account-card">

                    <div class="account-card-heading">

                        <div class="account-card-icon">
                            <i class="bi bi-person-vcard"></i>
                        </div>


                        <div>

                            <h2>
                                Personal Information
                            </h2>

                            <p>
                                Update your name, email address,
                                and phone number.
                            </p>

                        </div>

                    </div>


                    <form
                        action="{{ route(
                            'customer.account.update'
                        ) }}"
                        method="POST"
                        class="account-form"
                    >

                        @csrf
                        @method('PUT')


                        <div class="account-field">

                            <label for="name">
                                FULL NAME
                            </label>

                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old(
                                    'name',
                                    $user->name
                                ) }}"
                                maxlength="255"
                                required
                            >

                            @error('name')

                                <span class="field-error">
                                    {{ $message }}
                                </span>

                            @enderror

                        </div>


                        <div class="account-field">

                            <label for="email">
                                EMAIL ADDRESS
                            </label>

                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old(
                                    'email',
                                    $user->email
                                ) }}"
                                maxlength="255"
                                required
                            >

                            @error('email')

                                <span class="field-error">
                                    {{ $message }}
                                </span>

                            @enderror

                        </div>


                        <div class="account-field">

                            <label for="phone">
                                PHONE NUMBER
                            </label>

                            <input
                                type="text"
                                name="phone"
                                id="phone"
                                value="{{ old(
                                    'phone',
                                    $user->phone
                                ) }}"
                                maxlength="20"
                                placeholder="Example: 081234567890"
                            >

                            @error('phone')

                                <span class="field-error">
                                    {{ $message }}
                                </span>

                            @enderror

                        </div>


                        <button
                            type="submit"
                            class="account-save-button"
                        >

                            SAVE CHANGES

                            <i class="bi bi-arrow-right"></i>

                        </button>

                    </form>

                </section>


                {{-- ACCOUNT INFO --}}
                <section class="account-card">

                    <div class="account-card-heading">

                        <div class="account-card-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>


                        <div>

                            <h2>
                                Account Information
                            </h2>

                            <p>
                                Information related to your account.
                            </p>

                        </div>

                    </div>


                    <div class="account-info-grid">

                        <div>

                            <span>
                                ACCOUNT STATUS
                            </span>

                            <strong
                                class="
                                    account-status
                                    account-status-{{ $user->status }}
                                "
                            >
                                {{ strtoupper($user->status) }}
                            </strong>

                        </div>


                        <div>

                            <span>
                                ACCOUNT TYPE
                            </span>

                            <strong>
                                Customer
                            </strong>

                        </div>


                        <div>

                            <span>
                                MEMBER SINCE
                            </span>

                            <strong>

                                {{ $user->created_at
                                    ? $user->created_at
                                        ->format('d M Y')
                                    : '-'
                                }}

                            </strong>

                        </div>


                        <div>

                            <span>
                                USER ID
                            </span>

                            <strong>
                                #{{ $user->id }}
                            </strong>

                        </div>

                    </div>

                </section>

            </main>

        </div>

    </div>

</section>

@endsection