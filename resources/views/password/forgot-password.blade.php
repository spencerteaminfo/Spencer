@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif


<form method="POST" action="{{ route('api.password.email') }}">
    @csrf
    <input type="email" name="email" required placeholder="Enter your email">
    <button type="submit">Send Reset Link</button>
</form>