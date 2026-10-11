<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login | LIFESTYLE</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/auth/login.css') }}"
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
                EVERYDAY ESSENTIALS
            </span>

            <h1>
                STYLE FOR
                <br>
                EVERY DAY.
            </h1>

            <p>
                Discover lifestyle essentials designed for
                comfort, simplicity, and everyday confidence.
            </p>

        </div>

        <div class="visual-footer">
            © {{ date('Y') }} LIFESTYLE
        </div>

    </section>


    {{-- RIGHT SIDE --}}
    <section class="auth-form-section">

        <div class="auth-form-wrapper">

            <a
                href="{{ url('/') }}"
                class="back-home"
            >
                <i class="bi bi-arrow-left"></i>
                Back to store
            </a>


            <div class="auth-heading">

                <span>
                    WELCOME BACK
                </span>

                <h2>
                    Sign in to your account
                </h2>

                <p>
                    Enter your account details to continue shopping.
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
                            Unable to sign in
                        </strong>

                        @foreach ($errors->all() as $error)
                            <p>
                                {{ $error }}
                            </p>
                        @endforeach

                    </div>

                </div>

            @endif


            <form
                action="{{ route('login.process') }}"
                method="POST"
                class="auth-form"
            >

                @csrf


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
                            autofocus
                        >

                    </div>

                </div>


                {{-- PASSWORD --}}
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
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            id="passwordToggle"
                            aria-label="Show password"
                        >
                            <i
                                class="bi bi-eye"
                                id="passwordIcon"
                            ></i>
                        </button>

                    </div>

                </div>


                {{-- REMEMBER --}}
                <div class="form-options">

                    <label class="remember">

                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                            {{ old('remember') ? 'checked' : '' }}
                        >

                        <span class="custom-checkbox">
                            <i class="bi bi-check"></i>
                        </span>

                        Remember me

                    </label>

                </div>


                {{-- LOGIN BUTTON --}}
                <button
                    type="submit"
                    class="login-button"
                >
                    SIGN IN

                    <i class="bi bi-arrow-right"></i>
                </button>

            </form>


            <div class="auth-divider">

                <span>
                    NEW TO LIFESTYLE?
                </span>

            </div>


            <a
                href="{{ route('register') }}"
                class="register-button"
            >
                CREATE AN ACCOUNT
            </a>


            <p class="auth-terms">
                By continuing, you agree to use this website
                according to our store policies.
            </p>

        </div>

    </section>

</div>


<script>
    const passwordInput =
        document.getElementById('password');

    const passwordToggle =
        document.getElementById('passwordToggle');

    const passwordIcon =
        document.getElementById('passwordIcon');


    passwordToggle.addEventListener('click', function () {

        const isPassword =
            passwordInput.type === 'password';

        passwordInput.type =
            isPassword ? 'text' : 'password';

        passwordIcon.classList.toggle(
            'bi-eye',
            !isPassword
        );

        passwordIcon.classList.toggle(
            'bi-eye-slash',
            isPassword
        );

        passwordToggle.setAttribute(
            'aria-label',
            isPassword
                ? 'Hide password'
                : 'Show password'
        );

    });
</script>

</body>

</html>