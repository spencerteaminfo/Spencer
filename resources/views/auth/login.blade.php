<!DOCTYPE html>
<html lang="en">
<head>
    <x-head title="Login">@vite(['resources/js/utils/auth.ts'])</x-head>
</head>
<body class="bg-light" data-bs-theme="{{ $activeTheme }}">
<x-basic-header/>
<main>
    <div class="d-flex justify-content-center align-items-center flex-column auth-container">
        <div id="loginErrorAlert" class="alert alert-danger w-100 mb-3 d-none auth-login-alert" role="alert"></div>
        <div class="card shadow-sm p-4">
            <div id="login-box">
                <h2 class="text-center mb-4">{{__('auth.login')}}</h2>
                <form id="login-form" class="d-flex flex-column gap-3">
                    <div>
                        <label for="email" class="form-label">{{__('auth.email')}}</label>
                        <input type="email" name="email" id="email" placeholder="{{__('auth.email_placeholder')}}" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror">
                        @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="form-label">{{__('auth.password')}}</label>
                        <input type="password" name="password" id="password" placeholder="{{__('auth.password_placeholder')}}" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-2">{{__('auth.submit.login')}}</button>
                </form>

                <div class="mt-3 auth-actions">
                    <a href="/register" class="btn w-100">{{__('auth.no_account')}}</a>
                    <a href="{{ route('password.request') }}" class="d-block text-center text-decoration-none text-muted small mt-2">{{__('auth.forgot_password')}}</a>
                </div>
            </div>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
