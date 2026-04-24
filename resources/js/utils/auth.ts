import api from '../bootstrap';

document.addEventListener("DOMContentLoaded", () => {
    const registerForm = document.getElementById("register") as HTMLFormElement | null;
    const loginForm = document.getElementById("login-form") as HTMLFormElement | null;
    const emailInput = document.getElementById("email") as HTMLInputElement | null;
    const passwordInput = document.getElementById("password") as HTMLInputElement | null;

    function updateStatus(elId: string, isValid: boolean) {
        const el = document.getElementById(elId);
        if (el) {
            el.classList.toggle("text-success", isValid);
            el.classList.toggle("text-danger", !isValid);
        }
    }

    if (registerForm && emailInput && passwordInput) {
        const passwordRepeat = document.getElementById("password_repeat") as HTMLInputElement | null;
        const submitBtn = document.getElementById("submit-btn") as HTMLButtonElement | null;

        const validateRegister = () => {
            if (!passwordRepeat || !submitBtn) return;
            const pass = passwordInput.value;
            const repeat = passwordRepeat.value;

            const checks = {
                length: pass.length >= 8,
                upper: /[A-Z]/.test(pass),
                number: /[0-9]/.test(pass),
                match: pass === repeat && pass.length > 0
            };

            updateStatus("req-length", checks.length);
            updateStatus("req-upper", checks.upper);
            updateStatus("req-number", checks.number);
            updateStatus("req-match", checks.match);

            const isValid = Object.values(checks).every(Boolean);
            submitBtn.disabled = !isValid;
            submitBtn.classList.toggle("btn-primary", isValid);
            submitBtn.classList.toggle("btn-secondary", !isValid);
        };

        passwordInput.addEventListener("input", validateRegister);
        if (passwordRepeat) passwordRepeat.addEventListener("input", validateRegister);

        registerForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            try {
                await api.get('/sanctum/csrf-cookie');
                await api.post("/api/register", {
                    email: emailInput.value,
                    password: passwordInput.value,
                    password_confirmation: passwordRepeat?.value || ""
                });
                window.location.href = "/";
            } catch (e: any) {
                console.log(e);
                alert("Registrace selhala.");
            }
        });
    }

    if (loginForm && emailInput && passwordInput) {
        const loginErrorAlert = document.getElementById("loginErrorAlert");
        const submitBtn = loginForm.querySelector('button[type="submit"]') as HTMLButtonElement | null;
        let errorAlertTimer: number | null = null;

        const validateLogin = () => {
            if (!submitBtn) return;
            const isEmailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value);
            const isPasswordFilled = passwordInput.value.length > 0;
            const isValid = isEmailValid && isPasswordFilled;

            submitBtn.disabled = !isValid;
            submitBtn.classList.toggle("btn-primary", isValid);
            submitBtn.classList.toggle("btn-secondary", !isValid);
        };

        emailInput.addEventListener("input", validateLogin);
        passwordInput.addEventListener("input", validateLogin);

        loginForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            if (loginErrorAlert) {
                loginErrorAlert.classList.add("d-none");
                loginErrorAlert.textContent = "";
            }
            try {
                await api.get('/sanctum/csrf-cookie');
                await api.post("/api/login", {
                    email: emailInput.value,
                    password: passwordInput.value
                });
                window.location.href = "/";
            } catch (e: any) {
                console.log(e);
            }
        });
    }
});
