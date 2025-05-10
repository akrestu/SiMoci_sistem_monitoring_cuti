@extends('layouts.app')

@section('content')
<div class="modern-login-container">
    <div class="login-background">
        <div class="background-shape shape-1"></div>
        <div class="background-shape shape-2"></div>
        <div class="background-shape shape-3"></div>
    </div>
    
    <div class="login-wrapper d-flex align-items-center justify-content-center">
        <!-- Form Side -->
        <div class="login-card">
            <div class="text-center mb-3">
                <div class="app-logo mb-3">
                    <img src="{{ asset('images/leave.png') }}" alt="Logo" height="50">
                </div>
                <h4 class="fw-bold">Selamat Datang</h4>
                <p class="text-muted small">Masukkan kredensial untuk mengakses dashboard</p>
            </div>
            
            <form id="loginForm" method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-user"></i>
                        </span>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            placeholder="Username"
                            value="{{ old('username') }}" 
                            class="form-control @error('username') is-invalid @enderror" 
                            required 
                            autocomplete="username"
                        >
                        @error('username')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input 
                            type="password" 
                            id="password" 
                            name="password"
                            placeholder="Password"
                            class="form-control @error('password') is-invalid @enderror"
                            required 
                            autocomplete="current-password"
                        >
                        <button type="button" class="btn btn-outline-secondary toggle-password">
                            <i class="fas fa-eye"></i>
                        </button>
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label small" for="remember">
                            Ingat saya
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 py-2 login-button">
                    <span class="button-text">Masuk</span>
                    <div class="button-loader d-none" role="status">
                        <svg class="spinner" viewBox="0 0 50 50">
                            <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
                        </svg>
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </button>
            </form>
            
            <div class="login-footer text-center mt-3">
                <p class="small mb-1">Created with <span class="heart">❤</span> by ak.restu</p>
                <p class="version">v1.0.0</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    :root {
        /* Modern Color Palette - Updated for 2023 */
        --primary: #3b82f6;
        --primary-light: #60a5fa;
        --primary-dark: #2563eb;
        --primary-hover: #1d4ed8;
        --background: #f8fafc;
        --card-bg: #ffffff;
        --text-dark: #1e293b;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
        --input-bg: #f1f5f9;
    }

    /* Base Styles - Modern minimalist design */
    body {
        font-family: 'Inter', sans-serif;
    }
    
    .modern-login-container {
        position: relative;
        min-height: 100vh;
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        overflow: hidden;
    }
    
    .login-background {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        z-index: 0;
    }
    
    .background-shape {
        position: absolute;
        border-radius: 50%;
        opacity: 0.1;
    }
    
    .shape-1 {
        width: 500px;
        height: 500px;
        background: #ffffff;
        top: -150px;
        right: -100px;
        animation: float-slow 8s ease-in-out infinite;
    }
    
    .shape-2 {
        width: 300px;
        height: 300px;
        background: #ffffff;
        bottom: -80px;
        left: -80px;
        animation: float-slow 10s ease-in-out infinite;
    }
    
    .shape-3 {
        width: 200px;
        height: 200px;
        background: #ffffff;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        animation: pulse 12s ease-in-out infinite;
    }
    
    @keyframes float-slow {
        0% {
            transform: translateY(0) scale(1);
        }
        50% {
            transform: translateY(-15px) scale(1.03);
        }
        100% {
            transform: translateY(0) scale(1);
        }
    }
    
    @keyframes pulse {
        0% {
            transform: translate(-50%, -50%) scale(1);
            opacity: 0.05;
        }
        50% {
            transform: translate(-50%, -50%) scale(1.3);
            opacity: 0.1;
        }
        100% {
            transform: translate(-50%, -50%) scale(1);
            opacity: 0.05;
        }
    }
    
    .login-wrapper {
        position: relative;
        z-index: 1;
        width: 100%;
        height: 100vh;
    }
    
    /* Card Styling - Glass morphism effect */
    .login-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        padding: 2rem;
        border-radius: 12px;
        width: 100%;
        max-width: 360px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.2);
        animation: fadeIn 0.6s ease-out;
        transform: translateY(0);
        transition: all 0.3s ease;
    }
    
    .login-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.15), 0 10px 15px -6px rgba(0, 0, 0, 0.1);
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .app-logo {
        display: flex;
        justify-content: center;
        margin-bottom: 1rem;
    }
    
    .login-card h4 {
        color: var(--text-dark);
        margin-bottom: 0.25rem;
    }
    
    /* Form Elements - Clean and modern inputs */
    .input-group {
        transition: all 0.2s ease;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        margin-bottom: 0.5rem;
    }
    
    .input-group:focus-within {
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
    }
    
    .input-group-text {
        background-color: white;
        border: none;
        color: var(--primary);
        padding-left: 1rem;
        border-right: 1px solid #f0f0f0;
    }
    
    .form-control {
        border: none;
        box-shadow: none !important;
        padding: 0.65rem 1rem;
        color: var(--text-dark);
        background-color: white;
        font-size: 0.9rem;
    }
    
    .form-control::placeholder {
        color: #a0aec0;
        font-size: 0.85rem;
    }
    
    .toggle-password {
        background: white;
        border: none;
        color: var(--text-muted);
        border-left: 1px solid #f0f0f0;
    }
    
    .toggle-password:hover {
        color: var(--primary);
    }
    
    .form-check-input {
        width: 0.9rem;
        height: 0.9rem;
    }
    
    .form-check-input:checked {
        background-color: var(--primary);
        border-color: var(--primary);
    }
    
    .form-check-label {
        color: var(--text-muted);
    }
    
    /* Button Styles - More polished with micro-interactions */
    .login-button {
        background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
        border: none;
        font-weight: 500;
        border-radius: 8px;
        position: relative;
        transition: all 0.3s ease;
        padding: 0.65rem 1rem;
        letter-spacing: 0.01em;
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
        overflow: hidden;
    }
    
    .login-button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: all 0.6s;
    }
    
    .login-button:hover {
        background: linear-gradient(90deg, var(--primary-dark) 0%, var(--primary-dark) 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 10px rgba(37, 99, 235, 0.3);
    }
    
    .login-button:hover::before {
        left: 100%;
    }
    
    .login-button:active {
        transform: translateY(0);
        box-shadow: 0 3px 5px rgba(37, 99, 235, 0.2);
    }
    
    .login-button.loading .button-text {
        visibility: hidden;
        opacity: 0;
    }
    
    .login-button.loading .button-loader {
        display: inline-block !important;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    
    .spinner {
        animation: rotate 2s linear infinite;
        width: 20px;
        height: 20px;
    }
    
    .path {
        stroke: #ffffff;
        stroke-linecap: round;
        animation: dash 1.5s ease-in-out infinite;
    }
    
    @keyframes rotate {
        100% {
            transform: rotate(360deg);
        }
    }
    
    @keyframes dash {
        0% {
            stroke-dasharray: 1, 150;
            stroke-dashoffset: 0;
        }
        50% {
            stroke-dasharray: 90, 150;
            stroke-dashoffset: -35;
        }
        100% {
            stroke-dasharray: 90, 150;
            stroke-dashoffset: -124;
        }
    }
    
    /* Footer */
    .login-footer {
        color: var(--text-muted);
        font-size: 0.8rem;
    }
    
    .login-footer .heart {
        color: #f43f5e;
        display: inline-block;
        animation: heartbeat 1.5s infinite ease-in-out;
    }
    
    @keyframes heartbeat {
        0% { transform: scale(1); }
        15% { transform: scale(1.2); }
        30% { transform: scale(1); }
        45% { transform: scale(1.2); }
        60% { transform: scale(1); }
    }
    
    .version {
        font-size: 0.7rem;
        opacity: 0.6;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 576px) {
        .login-card {
            padding: 1.75rem;
            margin: 1rem;
            max-width: 90%;
        }
    }
    
    @media (max-width: 400px) {
        .login-card {
            padding: 1.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        const togglePassword = document.querySelector('.toggle-password');
        const passwordInput = document.getElementById('password');
        
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle icon
                const icon = this.querySelector('i');
                if (type === 'password') {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                } else {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            });
        }
        
        // Login form submission with custom spinner
        const loginForm = document.getElementById('loginForm');
        const loginButton = document.querySelector('.login-button');
        
        if (loginForm && loginButton) {
            loginForm.addEventListener('submit', function(e) {
                // Add loading state to button
                loginButton.classList.add('loading');
                // Disable button to prevent multiple submissions
                loginButton.disabled = true;
                
                // Show spinner
                const buttonLoader = loginButton.querySelector('.button-loader');
                if (buttonLoader) {
                    buttonLoader.classList.remove('d-none');
                }
            });
        }
        
        // Add subtle animation to inputs on focus
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.closest('.input-group').classList.add('focused');
            });
            
            input.addEventListener('blur', () => {
                input.closest('.input-group').classList.remove('focused');
            });
        });
    });
</script>
@endpush