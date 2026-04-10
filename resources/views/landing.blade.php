<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPANTAU - Sistem Monitoring SPJ Kecamatan Paseh</title>
    <meta name="description" content="SIPANTAU - Sistem Monitoring Surat Pertanggungjawaban Kecamatan Paseh. Platform digital untuk pengelolaan dan pemantauan dokumen SPJ.">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2E7D32;
            --primary-light: #43A047;
            --primary-dark: #1B5E20;
            --accent: #66BB6A;
            --accent-light: #A5D6A7;
            --bg-dark: #1A2E1A;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            overflow-x: hidden;
        }

        /* ===== NAVBAR ===== */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background 0.3s;
        }

        .navbar.scrolled {
            background: rgba(15, 27, 45, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.2);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: #fff;
        }

        .navbar-brand .brand-icon {
            width: 48px;
            height: 48px;
            background: transparent;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 0;
        }

        .navbar-brand .brand-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .navbar-brand span {
            font-weight: 700;
            font-size: 1.15rem;
            letter-spacing: 1px;
        }

        .navbar-login {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 1.5rem;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 10px;
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.88rem;
            transition: all 0.3s;
        }

        .navbar-login:hover {
            background: var(--accent);
            border-color: var(--accent);
            color: var(--primary-dark);
            transform: translateY(-1px);
        }

        /* ===== HERO ===== */
        .hero {
            min-height: 100vh;
            background: linear-gradient(135deg, #0d1f0d 0%, #1B5E20 40%, #2E7D32 70%, #43A047 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 80%;
            height: 160%;
            background: radial-gradient(circle, rgba(102,187,106,0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 120px;
            background: linear-gradient(to top, #f8fafc, transparent);
            pointer-events: none;
        }

        .hero-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
        }

        .hero-content {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 2rem;
            max-width: 750px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1rem;
            background: rgba(102,187,106,0.15);
            border: 1px solid rgba(102,187,106,0.3);
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--accent-light);
            margin-bottom: 1.5rem;
            letter-spacing: 0.5px;
        }

        .hero h1 {
            font-size: 3.2rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.15;
            margin-bottom: 0.5rem;
        }

        .hero h1 .accent {
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 1.05rem;
            color: rgba(255,255,255,0.55);
            line-height: 1.7;
            margin-bottom: 2.5rem;
            max-width: 580px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-hero-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.85rem 2rem;
            background: #fff;
            border: none;
            border-radius: 12px;
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 4px 20px rgba(255,255,255,0.2);
        }

        .btn-hero-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(255,255,255,0.3);
            color: var(--primary-dark);
        }

        .btn-hero-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.85rem 2rem;
            background: transparent;
            border: 1.5px solid rgba(255,255,255,0.2);
            border-radius: 12px;
            color: rgba(255,255,255,0.8);
            font-weight: 500;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-hero-secondary:hover {
            border-color: rgba(255,255,255,0.4);
            color: #fff;
            background: rgba(255,255,255,0.05);
        }

        /* ===== FEATURES ===== */
        .features {
            padding: 5rem 2rem;
            background: #f8fafc;
        }

        .features-header {
            text-align: center;
            max-width: 550px;
            margin: 0 auto 3rem;
        }

        .features-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .features-header p {
            color: #64748b;
            font-size: 0.92rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .feature-card {
            background: #fff;
            border-radius: 14px;
            padding: 1.75rem;
            border: 1px solid #e2e8f0;
            transition: all 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.06);
            border-color: rgba(46,125,50,0.3);
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .feature-card p {
            font-size: 0.82rem;
            color: #64748b;
            line-height: 1.6;
        }

        /* ===== FOOTER ===== */
        .footer {
            background: var(--bg-dark);
            color: rgba(255,255,255,0.5);
            text-align: center;
            padding: 2rem;
            font-size: 0.78rem;
        }

        .footer a {
            color: var(--accent);
            text-decoration: none;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .hero h1 { font-size: 2.2rem; }
            .hero-subtitle { font-size: 0.92rem; }
            .navbar { padding: 0.85rem 1rem; }
            .features-grid { grid-template-columns: 1fr; }
        }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hero-content > * {
            animation: fadeUp 0.8s ease forwards;
            opacity: 0;
        }
        .hero-content > *:nth-child(1) { animation-delay: 0.1s; }
        .hero-content > *:nth-child(2) { animation-delay: 0.25s; }
        .hero-content > *:nth-child(3) { animation-delay: 0.4s; }
        .hero-content > *:nth-child(4) { animation-delay: 0.55s; }
    </style>
</head>
<body>
    {{-- Navbar --}}
    <nav class="navbar" id="navbar">
        <a href="/" class="navbar-brand">
            <div class="brand-icon"><img src="{{ asset('images/logo.png') }}" alt="SIPANTAU Logo"></div>
            <span>SIPANTAU</span>
        </a>
        <a href="{{ route('login') }}" class="navbar-login">
            <i class="bi bi-box-arrow-in-right"></i> Masuk
        </a>
    </nav>

    {{-- Hero --}}
    <section class="hero">
        <div class="hero-grid"></div>
        <div class="hero-content">
            <div class="hero-badge">
                <i class="bi bi-building"></i>
                Kecamatan Paseh
            </div>
            <h1>
                Sistem Monitoring<br>
                <span class="accent">Surat Pertanggungjawaban</span>
            </h1>
            <p class="hero-subtitle">
                Platform digital untuk pengelolaan, pemantauan, dan arsip dokumen SPJ secara terpusat di Kecamatan Paseh.
            </p>
            <div class="hero-actions">
                <a href="{{ route('login') }}" class="btn-hero-primary">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk ke Sistem
                </a>
                <a href="#fitur" class="btn-hero-secondary">
                    <i class="bi bi-info-circle"></i> Tentang Aplikasi
                </a>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="features" id="fitur">
        <div class="features-header">
            <h2>Fitur Utama</h2>
            <p>Dirancang untuk mempermudah pengelolaan dokumen SPJ di tingkat Kecamatan.</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(46,125,50,0.1); color: var(--primary);">
                    <i class="bi bi-cloud-arrow-up-fill"></i>
                </div>
                <h3>Unggah SPJ</h3>
                <p>Unggah dokumen surat pertanggungjawaban untuk arsip digital.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(5,150,105,0.1); color: #059669;">
                    <i class="bi bi-graph-up"></i>
                </div>
                <h3>Dashboard Monitoring</h3>
                <p>Pantau progres kelengkapan SPJ secara real-time.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(236,72,153,0.1); color: #ec4899;">
                    <i class="bi bi-file-earmark-arrow-up-fill"></i>
                </div>
                <h3>Import Data Program</h3>
                <p>Import data program, kegiatan, dan subkegiatan.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(14,165,233,0.1); color: #0ea5e9;">
                    <i class="bi bi-clock-history"></i>
                </div>
                <h3>Riwayat Lengkap</h3>
                <p>Pencatatan waktu pelaksanaan kegiatan dan waktu pengumpulan SPJ.</p>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="footer">
        <p>&copy; {{ date('Y') }} <strong>SIPANTAU</strong> — Kecamatan Paseh. Sistem Monitoring Surat Pertanggungjawaban.</p>
    </footer>

    <script>
        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 50);
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>
