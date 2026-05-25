<!DOCTYPE html>
<html lang="{{__('auth.language')}}">
<x-head title="{{__('auth.register')}}">@vite(['resources/js/utils/auth/register.ts'])</x-head>
<body class="bg-light" data-bs-theme="{{ $activeTheme }}">
<x-basic-header/>
<main>
    <div class="d-flex flex-column justify-content-center align-items-center auth-container">
        <div id="register-error" class="alert alert-danger w-100 mb-3 d-none auth-login-alert" role="alert">{{__('auth.error')}}</div>
        <div class="card shadow-sm p-4">
            <div id="register-box">
                <h2 class="text-center mb-4">{{__('auth.register')}}</h2>
                <form id="register-form" method="POST" action="/register" class="d-flex flex-column gap-3">
                    <div>
                        <label for="email" class="form-label">{{__('auth.email')}}</label>
                        <input type="email" name="email" id="register-email" placeholder="{{__('auth.email_placeholder')}}" class="form-control">
                    </div>
                    <div>
                        <label for="password" class="form-label">{{__('auth.password')}}</label>
                        <input type="password" name="password" id="register-password" placeholder="{{__('auth.password_placeholder')}}" class="form-control">
                    </div>
                    <div>
                        <label for="password_repeat" class="form-label">{{__('auth.confirm_password')}}</label>
                        <input type="password" name="password_confirmation" id="register-password-repeat" placeholder="{{__('auth.password_placeholder')}}" class="form-control">
                    </div>
                    <button type="submit" id="register-send" class="btn btn-primary w-100 mt-2">{{__('auth.submit.register')}}</button>

                    <ul id="password-requirements" class="list-unstyled small mt-1">
                        <li id="req-length" class="text-danger">{{__('auth.reg_min_char')}}</li>
                        <li id="req-upper" class="text-danger">{{__('auth.reg_min_caps')}}</li>
                        <li id="req-number" class="text-danger">{{__('auth.reg_min_num')}}</li>
                        <li id="req-match" class="text-danger">{{__('auth.reg_matching')}}</li>
                    </ul>
                </form>

                <div class="mt-3 auth-actions">
                    <a href="/login" class="btn w-100">{{__('auth.have_account')}}</a>
                    <a href="{{ route('password.request') }}" class="d-block text-center text-decoration-none text-muted small mt-2">{{__('auth.forgot_password')}}</a>
                </div>
            </div>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
