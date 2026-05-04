import api from '../../bootstrap';
import type { Auth } from '@/models';

document.addEventListener('DOMContentLoaded', () => {
    const error = document.getElementById('login-error') as HTMLElement;
    const form = document.getElementById('login-form') as HTMLFormElement;
    const email = document.getElementById('login-email') as HTMLInputElement;
    const password = document.getElementById('login-password') as HTMLInputElement;
    const button = document.getElementById('login-send') as HTMLButtonElement;
    const inputs: HTMLInputElement[] = [email, password,];

    const checkFill = () => {
        button.disabled = inputs.some(input => input.value.trim() === '');
    }
     const handleLogin = async (event: Event): Promise<void> => {
            event.preventDefault();
            button.disabled = true;
            try {
                await api.get('/sanctum/csrf-cookie');
                await api.post<Auth>('/api/login', {
                    email: email.value,
                    password: password.value,
                });
                window.location.href = '/';
            } catch (e) {
                error.classList.remove('d-none');
                button.disabled = false;
                console.error(e);
            }
    }

    inputs.forEach(input => {
        input.addEventListener('input', checkFill)
    });
    checkFill();

    if (form && email && password) {
        form.addEventListener('submit', handleLogin);
    }
});
