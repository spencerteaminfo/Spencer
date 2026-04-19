{{-- <form method="POST" action="{{ route('api.password.update') }}">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    <div class="mb-3">
        <label>Email Address</label>
        <input type="email" name="email" value="{{ $email ?? old('email') }}" required>
    </div>

    <div class="mb-3">
        <label>New Password</label>
        <input type="password" name="password" required>
    </div>

    <div class="mb-3">
        <label>Confirm Password</label>
        <input type="password" name="password_confirmation" required>
    </div>

    <button type="submit">Update Password</button>
</form> --}}


<!doctype html>
<html lang="en">
<x-head title="Reset Password"></x-head>
<body class="bg-light" data-bs-theme="{{ $activeTheme }}">
    <div class="card shadow-sm border-0 rounded-4 p-4 mb-4 w-50" style="position:fixed; left: 50%; top: 50%;transform: translate(-50%, -50%);">
        <h2 class="h3 fw-bold mb-4 text-secondary">{{__('auth.reset_password')}}</h2>
        <form method="POST" action="{{ route('api.password.update') }}" class="d-flex flex-column gap-2">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div>
                <input type="email" name="email" value="{{ $email ?? old('email') }}" required class="form-control rounded-3" placeholder="{{__('auth.enter_email')}}">
            </div>

            <div>
                <input type="password" name="password" required class="form-control rounded-3" placeholder="{{__('auth.enter_new_pass')}}">
            </div>

            <div>
                <input type="password" name="password_confirmation" required class="form-control rounded-3" placeholder="{{__('auth.confirm_new_pass')}}">
            </div>
            <button type="submit" id="save-changes" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">{{__('auth.submit.update_pass')}}</button>
        </form>
    </div>
</body>
