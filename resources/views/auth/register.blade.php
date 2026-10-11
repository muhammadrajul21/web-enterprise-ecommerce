<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Register | LIFESTYLE</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/auth/login.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/auth/register.css') }}"
    >
</head>

<body>

<div class="auth-page">

    {{-- LEFT SIDE --}}
    <section class="auth-visual">

        <a
            href="{{ url('/') }}"
            class="brand"
        >
            LIFESTYLE
        </a>


        <div class="visual-content">

            <span class="visual-label">
                JOIN LIFESTYLE
            </span>

            <h1>
                YOUR STYLE
                <br>
                STARTS HERE.
            </h1>

            <p>
                Create your account and discover everyday
                essentials designed for comfort and confidence.
            </p>

        </div>


        <div class="visual-footer">
            © {{ date('Y') }} LIFESTYLE
        </div>

    </section>


    {{-- RIGHT SIDE --}}
    <section class="auth-form-section register-section">

        <div class="auth-form-wrapper register-wrapper">

            <a
                href="{{ route('login') }}"
                class="back-home"
            >
                <i class="bi bi-arrow-left"></i>
                Back to sign in
            </a>


            <div class="auth-heading">

                <span>
                    CREATE ACCOUNT
                </span>

                <h2>
                    Join LIFESTYLE
                </h2>

                <p>
                    Create your customer account to start shopping.
                </p>

            </div>


            {{-- ERRORS --}}
            @if ($errors->any())

                <div class="auth-alert">

                    <div class="alert-icon">
                        <i class="bi bi-exclamation-circle"></i>
                    </div>

                    <div>

                        <strong>
                            Registration failed
                        </strong>

                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach

                    </div>

                </div>

            @endif


            <form
                action="{{ route('register.process') }}"
                method="POST"
                class="auth-form register-form"
            >

                @csrf


                {{-- NAME --}}
                <div class="form-group">

                    <label for="name">
                        FULL NAME
                    </label>

                    <div class="input-wrapper">

                        <i class="bi bi-person"></i>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Enter your full name"
                            autocomplete="name"
                            required
                            autofocus
                        >

                    </div>

                </div>


                {{-- EMAIL --}}
                <div class="form-group">

                    <label for="email">
                        EMAIL ADDRESS
                    </label>

                    <div class="input-wrapper">

                        <i class="bi bi-envelope"></i>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Enter your email"
                            autocomplete="email"
                            required
                        >

                    </div>

                </div>


                {{-- PHONE --}}
                <div class="form-group">

                    <label for="phone">
                        PHONE NUMBER
                    </label>

                    <div class="input-wrapper">

                        <i class="bi bi-telephone"></i>

                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            value="{{ old('phone') }}"
                            placeholder="Example: 081234567890"
                            autocomplete="tel"
                        >

                    </div>

                </div>


                {{-- PASSWORD --}}
                <div class="register-password-grid">

                    <div class="form-group">

                        <label for="password">
                            PASSWORD
                        </label>

                        <div class="input-wrapper">

                            <i class="bi bi-lock"></i>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Enter password"
                                autocomplete="new-password"
                                required
                            >

                            <button
                                type="button"
                                class="password-toggle"
                                data-target="password"
                                aria-label="Show password"
                            >
                                <i class="bi bi-eye"></i>
                            </button>

                        </div>

                    </div>


                    <div class="form-group">

                        <label for="password_confirmation">
                            CONFIRM PASSWORD
                        </label>

                        <div class="input-wrapper">

                            <i class="bi bi-shield-lock"></i>

                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="Confirm password"
                                autocomplete="new-password"
                                required
                            >

                            <button
                                type="button"
                                class="password-toggle"
                                data-target="password_confirmation"
                                aria-label="Show password"
                            >
                                <i class="bi bi-eye"></i>
                            </button>

                        </div>

                    </div>

                </div>


                {{-- SUBMIT --}}
                <button
                    type="submit"
                    class="login-button"
                >
                    CREATE ACCOUNT

                    <i class="bi bi-arrow-right"></i>
                </button>

            </form>


            <div class="auth-divider">

                <span>
                    ALREADY HAVE AN ACCOUNT?
                </span>

            </div>


            <a
                href="{{ route('login') }}"
                class="register-button"
            >
                SIGN IN
            </a>


            <p class="auth-terms">
                By creating an account, you agree to use this
                website according to our store policies.
            </p>

        </div>

    </section>

</div>


<script>
    const passwordButtons =
        document.querySelectorAll('.password-toggle');

    passwordButtons.forEach(function (button) {

        button.addEventListener('click', function () {

            const targetId =
                button.getAttribute('data-target');

            const input =
                document.getElementById(targetId);

            const icon =
                button.querySelector('i');

            const isPassword =
                input.type === 'password';


            input.type =
                isPassword ? 'text' : 'password';


            icon.classList.toggle(
                'bi-eye',
                !isPassword
            );

            icon.classList.toggle(
                'bi-eye-slash',
                isPassword
            );


            button.setAttribute(
                'aria-label',
                isPassword
                    ? 'Hide password'
                    : 'Show password'
            );

        });

    });
</script>

</body>

</html>