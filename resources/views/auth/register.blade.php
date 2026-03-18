<!DOCTYPE html>
<html lang="en">
<x-head title="Register">@vite(['resources/js/auth.ts'])</x-head>
<body class="bg-light" data-bs-theme="{{ $activeTheme }}">
<x-basicHeader/>
<main>
    <div class="d-flex flex-column justify-content-center align-items-center auth-container">
        <div class="card shadow-sm p-4">
            <div id="register-box">
                <h2 class="text-center mb-4">Register</h2>
                <form id="register" method="POST" action="/register" class="d-flex flex-column gap-3">
                    @csrf
                    <div>
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" placeholder="tomik.bobik@centrum.cz" class="form-control">
                    </div>
                    <div>
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" placeholder="hesl0" class="form-control">
                    </div>
                    <div>
                        <label for="password_repeat" class="form-label">Repeat password</label>
                        <input type="password" name="password_confirmation" id="password_repeat" placeholder="hesl0" class="form-control">
                    </div>
                    <button type="submit" id="submit-btn" class="btn btn-secondary w-100 mt-2" disabled>Submit</button>

                    <ul id="password-requirements" class="list-unstyled small mt-1">
                        <li id="req-length" class="text-danger">Minimálně 8 znaků</li>
                        <li id="req-upper" class="text-danger">Alespoň jedno velké písmeno</li>
                        <li id="req-number" class="text-danger">Alespoň jedno číslo</li>
                        <li id="req-match" class="text-danger">Hesla se shodují</li>
                    </ul>
                </form>

                <div class="text-center mt-3">
                    <a href="/login" class="text-decoration-none">
                        <h4 class="h6 text-muted">Already have an account? Login</h4>
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
