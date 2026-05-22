import axios from 'axios';

const api = axios.create({
    baseURL: 'http://127.0.0.1:8000',
    withCredentials: true,
    withXSRFToken: true,
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
});

export default api;
