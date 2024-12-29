import axios from 'axios';

// Export the apiClient instance
export const apiClient = axios.create({
    baseURL: import.meta.env.VITE_API_URL,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    withCredentials: true
});

// We should use the same instance for all requests
export const axiosInstance = apiClient;

// Add request interceptor to include token
apiClient.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Add response interceptor to handle errors
apiClient.interceptors.response.use(
    (response) => response,
    (error) => {
        // Don't treat verification required as an error
        if (error.response?.status === 403 && error.response?.data?.requires_verification) {
            return Promise.reject(error); // Let the service handle it
        }

        console.error('API Error:', error);
        return Promise.reject(error);
    }
);
