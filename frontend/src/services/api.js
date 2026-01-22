import axios from 'axios';

// Create axios instance with base URL pointing to Laravel backend
const apiClient = axios.create({
    baseURL: 'http://localhost:8000/api', // Your Laravel API URL
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
    withCredentials: false, // Set to true if using sessions/cookies
});

// Request interceptor (optional)
apiClient.interceptors.request.use(
    config => {
        // You can add authentication tokens here later
        // const token = localStorage.getItem('token');
        // if (token) {
        //     config.headers.Authorization = `Bearer ${token}`;
        // }
        return config;
    },
    error => {
        return Promise.reject(error);
    }
);

// Response interceptor (optional)
apiClient.interceptors.response.use(
    response => response,
    error => {
        // Handle common errors
        if (error.response) {
            switch (error.response.status) {
                case 401:
                    console.error('Unauthorized! Redirect to login.');
                    // router.push('/login');
                    break;
                case 403:
                    console.error('Forbidden!');
                    break;
                case 404:
                    console.error('Not Found!');
                    break;
                case 422:
                    console.error('Validation Error:', error.response.data.errors);
                    break;
                case 500:
                    console.error('Server Error!');
                    break;
            }
        } else if (error.request) {
            console.error('No response received:', error.request);
        } else {
            console.error('Request setup error:', error.message);
        }
        return Promise.reject(error);
    }
);

// API methods
export default {
    // Test connection
    testConnection() {
        return apiClient.get('/test');
    },

    // Health check
    checkHealth() {
        return apiClient.get('/health');
    },

    // You'll add more methods here as you create endpoints
    // Example:
    // getUsers() {
    //     return apiClient.get('/users');
    // },
    
    // createUser(data) {
    //     return apiClient.post('/users', data);
    // }
};