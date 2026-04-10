<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <title>Login - SIPANTAU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #0d1f0d 0%, #1B5E20 40%, #2E7D32 70%, #43A047 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
        }

        .login-brand {
            text-align: center;
            margin-bottom: 2rem;
            color: #fff;
        }

        .login-brand .brand-icon {
            width: 120px;
            height: 120px;
            background: transparent;
            border-radius: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            margin-bottom: -20px;
            box-shadow: none;
            overflow: hidden;
        }

        .login-brand .brand-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .login-brand h2 {
            font-weight: 700;
            font-size: 1.75rem;
            letter-spacing: 2px;
            margin: 0;
        }

        .login-brand p {
            font-size: 0.8rem;
            opacity: 0.6;
            margin-top: 0.25rem;
        }

        .login-card {
            background: #fff;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .login-card h5 {
            font-weight: 600;
            color: #2E7D32;
            margin-bottom: 0.25rem;
        }

        .login-card .subtitle {
            font-size: 0.8rem;
            color: #64748b;
            margin-bottom: 1.5rem;
        }

        .form-control, .form-select {
            border-radius: 10px;
            padding: 0.7rem 1rem;
            border: 1.5px solid #e2e8f0;
            font-size: 0.9rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: #2E7D32;
            box-shadow: 0 0 0 3px rgba(46,125,50,0.1);
        }

        .input-group-text {
            border-radius: 10px 0 0 10px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-right: none;
            color: #64748b;
        }

        .input-group .form-control,
        .input-group .form-select {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }

        .btn-login {
            width: 100%;
            padding: 0.7rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            background: linear-gradient(135deg, #2E7D32, #43A047);
            border: none;
            color: #fff;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #1B5E20, #2E7D32);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(46,125,50,0.3);
            color: #fff;
        }

        .captcha-box {
            background: #f1f5f9;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border: 1.5px solid #e2e8f0;
        }

        .captcha-question {
            font-size: 1.15rem;
            font-weight: 700;
            color: #2E7D32;
            white-space: nowrap;
            user-select: none;
            letter-spacing: 2px;
        }

        .captcha-input {
            border: none;
            background: #fff;
            border-radius: 8px;
            padding: 0.45rem 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            width: 70px;
            text-align: center;
            border: 1.5px solid #e2e8f0;
        }

        .captcha-input:focus {
            outline: none;
            border-color: #2E7D32;
            box-shadow: 0 0 0 2px rgba(46,125,50,0.1);
        }

        .footer-links {
            text-align: center;
            margin-top: 1.25rem;
        }

        .footer-links a {
            color: rgba(255,255,255,0.5);
            font-size: 0.78rem;
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: rgba(255,255,255,0.8);
        }

        .footer-text {
            text-align: center;
            margin-top: 0.75rem;
            font-size: 0.7rem;
            color: rgba(255,255,255,0.3);
        }

        .year-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: linear-gradient(135deg, #66BB6A, #A5D6A7);
            color: #fff;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            margin-left: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-brand">
            <div class="brand-icon">
                <img src="{{ asset('images/logo.png') }}" alt="SIPANTAU Logo">
            </div>
            <h2>SIPANTAU</h2>
            <p>Sistem Monitoring SPJ — Kecamatan Paseh</p>
        </div>

        <div class="login-card">
            <h5>Selamat Datang</h5>
            <p class="subtitle">Silakan masuk dengan akun Anda</p>

            @if($errors->has('username'))
                <div class="alert alert-danger py-2 d-flex align-items-center" style="border-radius: 10px; font-size: 0.85rem;">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    {{ $errors->first('username') }}
                </div>
            @endif

            @if($errors->has('captcha'))
                <div class="alert alert-warning py-2 d-flex align-items-center" style="border-radius: 10px; font-size: 0.85rem;">
                    <i class="bi bi-shield-exclamation me-2"></i>
                    {{ $errors->first('captcha') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}">
                @csrf

                {{-- Pilihan Tahun --}}
                <div class="mb-3">
                    <label class="form-label" style="font-size: 0.8rem; font-weight: 600; color: #475569;">
                        <i class="bi bi-calendar-event me-1"></i>Tahun Anggaran
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                        <select name="tahun" class="form-select" id="login-tahun" required>
                            @foreach($availableYears as $year)
                                <option value="{{ $year }}"
                                    {{ (old('tahun', $defaultYear) == $year) ? 'selected' : '' }}>
                                    Tahun {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-size: 0.8rem; font-weight: 600; color: #475569;">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username"
                               value="{{ old('username') }}" required autofocus id="login-username">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-size: 0.8rem; font-weight: 600; color: #475569;">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password"
                               required id="login-password">
                    </div>
                </div>

                {{-- CAPTCHA --}}
                <div class="mb-3">
                    <label class="form-label" style="font-size: 0.8rem; font-weight: 600; color: #475569;">
                        <i class="bi bi-shield-check me-1"></i>Verifikasi Keamanan
                    </label>
                    <div class="captcha-box">
                        <span class="captcha-question">{{ $a }} + {{ $b }} =</span>
                        <input type="number" name="captcha" class="captcha-input" placeholder="?" required id="login-captcha" autocomplete="off">
                        <span style="font-size: 0.72rem; color: #64748b;">Jawab untuk melanjutkan</span>
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember" style="font-size: 0.82rem; color: #64748b;">Ingat saya</label>
                </div>

                <button type="submit" class="btn btn-login" id="login-submit">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                </button>
            </form>
        </div>

        <div class="footer-links">
            <a href="{{ route('landing') }}"><i class="bi bi-arrow-left me-1"></i>Kembali ke Beranda</a>
        </div>
        <p class="footer-text">
            &copy; {{ date('Y') }} SIPANTAU — Kecamatan Paseh
        </p>
    </div>
</body>
</html>

