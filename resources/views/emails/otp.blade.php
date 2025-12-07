<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OTP Code</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; color: #333;">
    <div style="max-width:600px; margin:0 auto; padding:20px; border:1px solid #eee; border-radius:8px;">
        <h2 style="margin-top:0;">Halo {{ $name ?? 'Pengguna' }},</h2>
        <p>Terima kasih telah mendaftar di BookingStay. Berikut adalah kode OTP Anda untuk verifikasi:</p>

        <div style="margin:20px 0; text-align:center;">
            <span style="display:inline-block; padding:12px 20px; background:#f5f5f5; border-radius:6px; font-size:20px; letter-spacing:3px;">
                {{ $otp }}
            </span>
        </div>

        <p>Kode ini berlaku selama 5 menit. Jika Anda tidak melakukan permintaan ini, abaikan email ini.</p>

        <p>Salam,<br>Tim BookingStay</p>
    </div>
</body>
</html>
