<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica', sans-serif; background-color: #f4f6f8; margin: 0; padding: 20px; }
        .container { max-w-md; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .header { text-align: center; margin-bottom: 20px; }
        .logo { font-size: 24px; font-weight: 900; color: #2563eb; }
        .content { color: #334155; line-height: 1.6; font-size: 14px; }
        .otp-box { background: #eff6ff; border: 1px dashed #60a5fa; padding: 15px; text-align: center; font-size: 28px; font-weight: 900; color: #1d4ed8; letter-spacing: 5px; margin: 20px 0; border-radius: 8px; }
        .footer { margin-top: 30px; font-size: 11px; color: #94a3b8; text-align: center; border-top: 1px solid #f1f5f9; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Pondasikita</div>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $userName }}</strong>,</p>
            <p>Kami menerima permintaan untuk mengubah kata sandi akun toko Anda. Untuk memastikan keamanan akun Anda, silakan gunakan kode One-Time Password (OTP) berikut:</p>
            
            <div class="otp-box">{{ $otpCode }}</div>
            
            <p><em>Kode ini hanya berlaku selama 5 menit. Jangan pernah memberikan kode ini kepada siapapun, termasuk pihak Pondasikita.</em></p>
            <p>Jika Anda tidak meminta perubahan kata sandi ini, abaikan email ini dan pastikan akun Anda tetap aman.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Pondasikita. All rights reserved.
        </div>
    </div>
</body>
</html>