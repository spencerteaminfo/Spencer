<!doctype html>
<html lang="{{__('password.language')}}">
<x-head title="{{__('password.reset_title')}}"></x-head>
<body class="bg-light" data-bs-theme="{{ $activeTheme }}">
    <div class="card shadow-sm border-0 rounded-4 p-4 mb-4 w-50" style="position:fixed; left: 50%; top: 50%;transform: translate(-50%, -50%);">
        <h2 class="h3 fw-bold mb-2 text-secondary">{{__('password.reset_title')}}</h2>
        <p class="text-muted small mb-4">{{__('password.reset_description')}}</p>
        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif
        <form method="POST" action="{{ route('password.update') }}" class="d-flex flex-column gap-2">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div>
                <input type="email" name="email" value="{{ $email ?? old('email') }}" required class="form-control rounded-3" placeholder="{{__('password.email_label')}}">
            </div>

            <div>
                <input type="password" name="password" required class="form-control rounded-3" placeholder="{{__('password.new_password')}}">
            </div>

            <div>
                <input type="password" name="password_confirmation" required class="form-control rounded-3" placeholder="{{__('password.confirm_new_password')}}">
            </div>
            <button type="submit" id="save-changes" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">{{__('password.update_button')}}</button>
        </form>
    </div>
</body>
