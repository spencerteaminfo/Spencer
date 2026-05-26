<!doctype html>
<html lang="{{__('password.language')}}">
<x-head title="{{__('password.request_title')}}">@vite(['resources/js/settings/forgotPassword.ts'])</x-head>
<body class="bg-light" data-bs-theme="{{ $activeTheme }}">
    <x-basic-header/>

    <div class="card shadow-sm border-0 rounded-4 p-4 mb-4 w-50" style="position:fixed; left: 50%; top: 50%;transform: translate(-50%, -50%);">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @error('email')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror
        <h2 class="h3 fw-bold mb-2 text-secondary">{{__('password.request_title')}}</h2>
        <p class="text-muted small mb-4">{{__('password.request_description')}}</p>
        <form method="POST" action="{{ route('password.email') }}" class="d-flex gap-2 flex-column">
            @csrf
            <input type="email" name="email" required placeholder="{{__('password.email_label')}}" class="form-control rounded-3" value="{{ old('email') }}">
            <button type="submit" id="save-changes" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">{{__('password.submit_button')}}</button>
        </form>
    </div>
</body>
</html>
