import api from '../../bootstrap';
import type { Auth } from '@/models';

document.addEventListener('DOMContentLoaded', () => {
    const errorEl = document.getElementById('register-error') as HTMLElement;
    const form = document.getElementById('register-form') as HTMLFormElement;
    const inputEmail = document.getElementById('register-email') as HTMLInputElement;
    const inputPassword = document.getElementById('register-password') as HTMLInputElement;
    const inputPasswordRepeat = document.getElementById('register-password-repeat') as HTMLInputElement;
    const button = document.getElementById('register-send') as HTMLButtonElement;
    const reqs = {
        length: document.getElementById('req-length'),
        upper: document.getElementById('req-upper'),
        number: document.getElementById('req-number'),
        match: document.getElementById('req-match')
    };

    const inputs = [inputEmail, inputPassword, inputPasswordRepeat];

    const updateStatus = (el: HTMLElement | null, isValid: boolean) => {
        if (el) {
            el.classList.toggle('text-success', isValid);
            el.classList.toggle('text-danger', !isValid);
        }
    };

    const validate = (): boolean => {
        const email = inputEmail.value.trim();
        const pass = inputPassword.value;
        const repeat = inputPasswordRepeat.value;

        const checks = {
            length: pass.length >= 8 && pass.length <= 32,
            upper: /[A-Z]/.test(pass),
            number: /[0-9]/.test(pass),
            match: pass === repeat && pass !== '',
            email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
        };

        updateStatus(reqs.length, checks.length);
        updateStatus(reqs.upper, checks.upper);
        updateStatus(reqs.number, checks.number);
        updateStatus(reqs.match, checks.match);

        const isValid = Object.values(checks).every(Boolean);
        button.disabled = !isValid;

        return isValid;
    };

    const handleRegister = async (event: Event): Promise<void> => {
        event.preventDefault();
        button.disabled = true;
        errorEl.classList.add('d-none');

        try {
            await api.get('/sanctum/csrf-cookie');

            const payload = {
                email: inputEmail.value,
                password: inputPassword.value,
                password_confirmation: inputPasswordRepeat.value
            };

            const { data } = await api.post<Auth>('/api/register', payload);
            if (data) {
                window.location.href = '/';
            }
        } catch (err: any) {
            button.disabled = false;
            errorEl.classList.remove('d-none');

            if (err.response?.data?.errors) {
                const firstError = Object.values(err.response.data.errors)[0] as string[];
                errorEl.textContent = firstError[0];
            } else if (err.response?.data?.message) {
                errorEl.textContent = err.response.data.message;
            }
        }
    };

    inputs.forEach(input => {
        input.addEventListener('input', validate);
    });
    validate();
    if (form) {
        form.addEventListener('submit', handleRegister);
    }
});
