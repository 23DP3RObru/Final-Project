<template>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h1 class="display-4">Vue.js + Laravel Demo</h1>
                <p class="lead">Frontend connected to Laravel Backend API</p>
            </div>
        </div>

        <div class="row">
            <!-- API Test Card -->
            <div class="col-md-6 mb-4">
                <div class="card custom-card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">API Connection Test</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Test the connection between Vue.js frontend and Laravel backend.</p>
                        
                        <div class="mb-3">
                            <button @click="testApi" class="btn btn-primary me-2" :disabled="testing">
                                {{ testing ? 'Testing...' : 'Test API Connection' }}
                            </button>
                            <button @click="checkHealth" class="btn btn-outline-primary" :disabled="checkingHealth">
                                {{ checkingHealth ? 'Checking...' : 'Check Health' }}
                            </button>
                        </div>

                        <!-- Test Results -->
                        <div v-if="apiResponse" class="mt-3">
                            <h6>API Response:</h6>
                            <div class="alert alert-success">
                                <pre class="mb-0">{{ JSON.stringify(apiResponse, null, 2) }}</pre>
                            </div>
                        </div>

                        <div v-if="healthResponse" class="mt-3">
                            <h6>Health Check:</h6>
                            <div class="alert alert-info">
                                <pre class="mb-0">{{ JSON.stringify(healthResponse, null, 2) }}</pre>
                            </div>
                        </div>

                        <div v-if="errorMessage" class="mt-3">
                            <h6>Error:</h6>
                            <div class="alert alert-danger">
                                {{ errorMessage }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Connection Status Card -->
            <div class="col-md-6 mb-4">
                <div class="card custom-card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Connection Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <div class="status-indicator" :class="connectionStatusClass"></div>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ connectionStatusText }}</h6>
                                <small class="text-muted">Backend: http://localhost:8000</small>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h6>API Endpoints:</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                GET /api/test
                                <span class="badge bg-primary rounded-pill">Test</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                GET /api/health
                                <span class="badge bg-info rounded-pill">Health</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                GET /api/items
                                <span class="badge bg-secondary rounded-pill">CRUD</span>
                            </li>
                        </ul>

                        <div class="mt-4">
                            <h6>Instructions:</h6>
                            <ol class="small">
                                <li>Click "Test API Connection" to verify backend is reachable</li>
                                <li>Click "Check Health" for backend status</li>
                                <li>Check console for detailed logs</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Database Operations Card -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card custom-card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Database Operations</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Test database operations through Laravel API (requires database setup).</p>
                        
                        <div class="alert alert-warning">
                            <small>
                                <strong>Note:</strong> Database operations require Laravel models and migrations to be set up first.
                                <br>Run these commands in your Laravel backend:
                                <code class="d-block mt-2">php artisan make:model Item -mfc</code>
                                <code class="d-block">php artisan migrate</code>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import api from '@/services/api.js';

// Reactive variables
const testing = ref(false);
const checkingHealth = ref(false);
const apiResponse = ref(null);
const healthResponse = ref(null);
const errorMessage = ref('');
const lastTestTime = ref(null);

// Computed properties
const connectionStatusClass = computed(() => {
    if (apiResponse.value) return 'status-connected';
    if (errorMessage.value) return 'status-error';
    return 'status-unknown';
});

const connectionStatusText = computed(() => {
    if (apiResponse.value) return 'Connected';
    if (errorMessage.value) return 'Connection Failed';
    return 'Not Tested';
});

// Methods
const testApi = async () => {
    testing.value = true;
    errorMessage.value = '';
    apiResponse.value = null;
    
    try {
        const response = await api.testConnection();
        apiResponse.value = response.data;
        lastTestTime.value = new Date().toLocaleTimeString();
        console.log('API Test Success:', response.data);
    } catch (error) {
        errorMessage.value = `Failed to connect to API: ${error.message}`;
        console.error('API Test Error:', error);
        
        if (error.response) {
            console.error('Response data:', error.response.data);
            console.error('Response status:', error.response.status);
        } else if (error.request) {
            console.error('No response received');
        }
    } finally {
        testing.value = false;
    }
};

const checkHealth = async () => {
    checkingHealth.value = true;
    errorMessage.value = '';
    healthResponse.value = null;
    
    try {
        const response = await api.checkHealth();
        healthResponse.value = response.data;
        console.log('Health Check Success:', response.data);
    } catch (error) {
        errorMessage.value = `Health check failed: ${error.message}`;
        console.error('Health Check Error:', error);
    } finally {
        checkingHealth.value = false;
    }
};

// Optional: Auto-test on component mount
// onMounted(() => {
//     testApi();
// });
</script>

<style scoped>
.status-indicator {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: inline-block;
}

.status-connected {
    background-color: #28a745;
    box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
}

.status-error {
    background-color: #dc3545;
    box-shadow: 0 0 10px rgba(220, 53, 69, 0.5);
}

.status-unknown {
    background-color: #6c757d;
    box-shadow: 0 0 10px rgba(108, 117, 125, 0.5);
}

pre {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    font-size: 0.9em;
}
</style>