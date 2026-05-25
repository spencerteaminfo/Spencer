import api from '../../bootstrap';
import type { Auth } from '@/models';

document.addEventListener('DOMContentLoaded', () => {
    const errorEl = document.getElementById('register-error') as HTMLElement | null;
    const form = document.getElementById('register-form') as HTMLFormElement | null;
    const inputEmail = document.getElementById('register-email') as HTMLInputElement | null;
    const inputPassword = document.getElementById('register-password') as HTMLInputElement | null;
    const inputPasswordRepeat = document.getElementById('register-password-repeat') as HTMLInputElement | null;
    const button = document.getElementById('register-send') as HTMLButtonElement | null;
    
    const reqs = {
        length: document.getElementById('req-length'),
        upper: document.getElementById('req-upper'),
        number: document.getElementById('req-number'),
        match: document.getElementById('req-match')
    };

    const inputs = [inputEmail, inputPassword, inputPasswordRepeat].filter((i): i is HTMLInputElement => Boolean(i));

    const updateStatus = (el: HTMLElement | null, isValid: boolean) => {
        if (el) {
            el.classList.toggle('text-success', isValid);
            el.classList.toggle('text-danger', !isValid);
        }
    };

    const validate = (): boolean => {
        if (!inputEmail || !inputPassword || !inputPasswordRepeat || !button) {
            return false;
        }

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

        if (!inputEmail || !inputPassword || !inputPasswordRepeat || !button) {
            return;
        }

        button.disabled = true;
        if (errorEl) errorEl.classList.add('d-none');

        try {
            await api.get('/sanctum/csrf-cookie');

            const payload = {
                email: inputEmail.value.trim(),
                password: inputPassword.value,
                password_confirmation: inputPasswordRepeat.value
            };

            const { data } = await api.post<Auth>('/api/register', payload);
            if (data) {
                window.location.href = '/';
            }
        } catch (err: any) {
            button.disabled = false;
            if (errorEl) errorEl.classList.remove('d-none');

            if (err.response?.data?.errors) {
                const firstError = Object.values(err.response.data.errors)[0] as any[];
                if (errorEl) errorEl.textContent = firstError[0];
            } else if (err.response?.data?.message) {
                if (errorEl) errorEl.textContent = err.response.data.message;
            } else {
                if (errorEl) errorEl.textContent = 'Něco se nepovedlo. Zkuste to znovu.';
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