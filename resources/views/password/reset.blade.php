<form method="POST" action="{{ route('api.password.update') }}">
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
</form>