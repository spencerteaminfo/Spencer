<!doctype html>
<html lang="en">
<x-head title="Reset Password"></x-head>
<body class="bg-light" data-bs-theme="{{ $activeTheme }}">

    <div class="card shadow-sm border-0 rounded-4 p-4 mb-4 w-50" style="position:fixed; left: 50%; top: 50%;transform: translate(-50%, -50%);">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        <h2 class="h3 fw-bold mb-4 text-secondary">{{__('auth.reset_password')}}</h2>
        <form method="POST" action="{{ route('api.password.email') }}" class="d-flex gap-2 flex-column">
            @csrf
            <input type="email" name="email" required placeholder="{{__('auth.email_placeholder')}}" class="form-control rounded-3">
            <button type="submit" id="save-changes" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">{{__('auth.submit.send_link')}}</button>
        </form>
    </div>
</body>
