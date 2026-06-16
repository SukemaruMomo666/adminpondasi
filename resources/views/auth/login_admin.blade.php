<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Perbaikan Path Asset --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/login_admin.css') }}"> 
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <a href="{{ url('/') }}" class="brand-link">Pondasikita</a>
                <h2>Admin Panel Login</h2>
                <p>Silakan masuk untuk mengelola website.</p>
            </div>

            {{-- Info Kredensial (Bisa dihapus saat rilis ke publik) --}}
            <div style="background-color: #e2e3e5; color: #383d41; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 13px; text-align: center;">
                <strong>Hint Login (Dari Seeder):</strong><br>
                User: <code>superadmin</code> | Pass: <code>rahasiaAdmin123</code>
            </div>

            {{-- Menampilkan Alert Error dari Controller Laravel --}}
            @if(session('error'))
                <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                    <i class="mdi mdi-alert-circle-outline" style="font-size: 1.2rem;"></i>
                    <span>{!! session('error') !!}</span>
                </div>
            @endif

            {{-- Form Login Admin --}}
            <form action="{{ route('admin.login.process') }}" method="POST">
                @csrf {{-- Wajib di Laravel untuk keamanan --}}
                
                <div class="form-group">
                    <i class="mdi mdi-account-outline input-icon"></i>
                    {{-- Default value di-set ke 'superadmin' sesuai seeder --}}
                    <input type="text" name="username" class="form-control with-icon" placeholder="Username atau Email" value="{{ old('username', 'superadmin') }}" required>
                </div>
                
                <div class="form-group">
                    <i class="mdi mdi-lock-outline input-icon"></i>
                    {{-- Default value di-set ke 'rahasiaAdmin123' --}}
                    <input type="password" name="password" id="password-field" class="form-control with-icon" placeholder="Password" value="rahasiaAdmin123" required>
                    <button type="button" id="password-toggle-btn" class="password-toggle">
                        <i class="mdi mdi-eye-outline"></i>
                    </button>
                </div>
                
                <div class="form-group d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                        <label class="form-check-label" for="rememberMe" style="font-size: 0.9rem;">
                            Ingat Saya
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-login">Masuk</button>
            </form>
            
            <div class="login-footer">
                <p>&copy; {{ date('Y') }} Pondasikita. All Rights Reserved.</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordField = document.getElementById('password-field');
            const toggleBtn = document.getElementById('password-toggle-btn');
            const icon = toggleBtn.querySelector('i');

            toggleBtn.addEventListener('click', function() {
                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                    icon.classList.remove('mdi-eye-outline');
                    icon.classList.add('mdi-eye-off-outline');
                } else {
                    passwordField.type = 'password';
                    icon.classList.remove('mdi-eye-off-outline');
                    icon.classList.add('mdi-eye-outline');
                }
            });
        });
    </script>
</body>
</html>