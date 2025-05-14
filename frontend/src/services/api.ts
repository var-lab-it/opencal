import axios from 'axios';

const apiClient = axios.create({
    baseURL: import.meta.env.VITE_API_HOST,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
});

apiClient.interceptors.request.use(
    (config) => {
        const token = sessionStorage.getItem('jwtToken');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => Promise.reject(error)
);

apiClient.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response && error.response.status === 401) {
            sessionStorage.removeItem('jwtToken');
            //window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

export default apiClient;
