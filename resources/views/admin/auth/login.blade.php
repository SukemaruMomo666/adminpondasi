<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Access - Pondasikita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0f111a; /* Background sangat gelap */
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }
        .login-card {
            background-color: #1c1f2c;
            border: 1px solid #2a2e3f;
            border-radius: 12px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        }
        .login-card h3 { color: #fff; font-weight: 700; margin-bottom: 5px; text-align: center; }
        .login-card p { color: #8b92a5; text-align: center; font-size: 14px; margin-bottom: 30px; }
        
        .form-control {
            background-color: #0f111a;
            border: 1px solid #2a2e3f;
            color: #fff;
            padding: 12px 15px;
        }
        .form-control:focus {
            background-color: #0f111a;
            border-color: #4f46e5;
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25);
        }
        .form-label { color: #8b92a5; font-size: 13px; font-weight: 600; text-transform: uppercase; }
        
        .btn-login {
            background-color: #4f46e5;
            color: white;
            border: none;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .btn-login:hover { background-color: #4338ca; }
    </style>
</head>
<body>

    <div class="login-card">
        <h3>System Access</h3>
        <p>Restricted Area. Authorized Personnel Only.</p>

        @if(session('error'))
            <div class="alert alert-danger" style="font-size: 14px; padding: 10px;">
                {!! session('error') !!}
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email Identification</label>
                <input type="email" name="email" class="form-control" placeholder="admin@pondasikita.com" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <span class="text-danger" style="font-size: 12px;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="form-label">Passcode</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-login">AUTHORIZE ACCESS</button>
        </form>
    </div>

</body>
</html>