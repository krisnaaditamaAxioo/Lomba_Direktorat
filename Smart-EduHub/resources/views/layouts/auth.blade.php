<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'EduAdmin Pro') }} - @yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/SEHmob.png') }}">
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;90&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <script id="tailwind-config">
        tailwind.config = { 
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "on-tertiary-fixed-variant": "#7b2f00",
                        "on-surface": "#0b1c30",
                        "surface-container": "#e5eeff",
                        "secondary-fixed": "#c9e6ff",
                        "on-background": "#0b1c30",
                        "surface-container-highest": "#d3e4fe",
                        "surface-variant": "#d3e4fe",
                        "error-container": "#ffdad6",
                        "surface-container-low": "#eff4ff",
                        "surface": "#f8f9ff",
                        "primary-fixed": "#ffedd5",
                        "on-tertiary-fixed": "#351000",
                        "on-tertiary": "#ffffff",
                        "on-secondary": "#ffffff",
                        "inverse-on-surface": "#eaf1ff",
                        "secondary-fixed-dim": "#89ceff",
                        "on-secondary-fixed-variant": "#004c6e",
                        "surface-dim": "#cbdbf5",
                        "on-primary-fixed": "#7c2d12",
                        "tertiary": "#7e3000",
                        "tertiary-container": "#a44100",
                        "surface-container-lowest": "#ffffff",
                        "on-primary-container": "#7c2d12",
                        "secondary": "#740B07",
                        "on-primary-fixed-variant": "#c2410c",
                        "inverse-primary": "#fed7aa",
                        "surface-container-high": "#dce9ff",
                        "secondary-container": "#39b8fd",
                        "surface-tint": "#f97316",
                        "on-error": "#ffffff",
                        "inverse-surface": "#213145",
                        "background": "#f8f9ff",
                        "outline": "#777587",
                        "outline-variant": "#c7c4d8",
                        "error": "#ba1a1a",
                        "tertiary-fixed-dim": "#ffb695",
                        "primary-fixed-dim": "#fed7aa",
                        "surface-bright": "#f8f9ff",
                        "on-primary": "#ffffff",
                        "tertiary-fixed": "#ffdbcc",
                        "on-tertiary-container": "#ffd2be",
                        "on-secondary-fixed": "#001e2f",
                        "on-secondary-container": "#004666",
                        "on-surface-variant": "#464555",
                        "primary-container": "#fb923c",
                        "primary": "#ea580c",
                        "on-error-container": "#93000a"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "gutter": "24px",
                        "stack-sm": "8px",
                        "container-max": "1440px",
                        "margin-page": "32px",
                        "unit": "4px",
                        "stack-md": "16px",
                        "stack-lg": "24px"
                    },
                    "fontFamily": {
                        "display-xl": ["Inter"],
                        "body-lg": ["Inter"],
                        "label-md": ["Inter"],
                        "headline-md": ["Inter"],
                        "body-md": ["Inter"],
                        "label-sm": ["Inter"],
                        "headline-lg": ["Inter"]
                    },
                    "fontSize": {
                        "display-xl": ["36px", {"lineHeight": "44px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                        "body-lg": ["16px", {"lineHeight": "24px", "letterSpacing": "0em", "fontWeight": "400"}],
                        "label-md": ["12px", {"lineHeight": "16px", "letterSpacing": "0.02em", "fontWeight": "600"}],
                        "headline-md": ["20px", {"lineHeight": "28px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                        "body-md": ["14px", {"lineHeight": "20px", "letterSpacing": "0em", "fontWeight": "400"}],
                        "label-sm": ["11px", {"lineHeight": "14px", "letterSpacing": "0.03em", "fontWeight": "500"}],
                        "headline-lg": ["24px", {"lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "600"}]
                    }
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: "FILL" 0, "wght" 400, "GRAD" 0, "opsz" 24
        }
        .bg-login-hero {
            background-image: url(https://lh3.googleusercontent.com/aida-public/AB6AXuCH1ZCnhPqlI0pLl8uqk0QtBTijg-77rzb-3Q8COvT_K796DMRm7i3F6-zA7cD661V67H0uk55RgrKuUdNHga1bfV86GO0oGIBPq2544VWlSwIex_DaXPMgxYatDXo9Mf6H8JV_Z6Arzc_s3dctJxZhbOP9ZPlpvu1NBhErm6HXk1p5TW-HSlXMu9lH8-xztDNEsIDGYLnICZfmOfVnTaA5abT5eY7c0nvlPzQN8KwuVljQJQN5u2_nGON1iwJQRlyDMJ0s4oChJl4);
            background-size: cover;
            background-position: center
        }
        .glass-overlay {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px)
        }
        .btn-glow:hover {
            box-shadow: 0 0 20px rgba(249, 115, 22, 0.4)
        }
        /* Splash Screen Styles */
        .splash-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #0f172a 0%, #7c2d12 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease-out;
        }
        .splash-screen.fade-out {
            opacity: 0;
            pointer-events: none;
        }
        .splash-logo {
            animation: pulse 1.5s ease-in-out infinite;
        }
        .splash-text {
            animation: fadeInUp 0.8s ease-out;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.03); }
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    @yield('styles')
</head>
<body class="bg-background font-body-md text-on-background min-h-screen flex">
    
    <!-- Splash Screen: Ditambahkan 'lg:hidden' agar tidak pernah muncul saat dibuka via Desktop/Web -->
    <div id="splashScreen" class="splash-screen lg:hidden">
        <!-- h-10 (Ukuran pas untuk mobile), brightness-0 invert (Membuat logo png hitam/warna jadi putih murni) -->
        <img src="{{ asset('images/SEH.png') }}" alt="Logo" class="splash-logo h-10 w-auto mb-5 brightness-0 invert">
        
        <p class="splash-text text-white/60 text-sm font-medium tracking-wide">Loading...</p>
    </div>
    
    <main class="w-full flex min-h-screen overflow-hidden">
        @yield('content')
    </main>
    
    <script>
        // Splash Screen (Hanya berjalan eksekusinya jika mendeteksi layar mobile)
        window.addEventListener('load', function() {
            const splash = document.getElementById('splashScreen');
            if (splash && window.innerWidth < 1024) { // 1024px adalah breakpoint lg Tailwind
                setTimeout(function() {
                    splash.classList.add('fade-out');
                    setTimeout(() => {
                        splash.remove();
                    }, 500);
                }, 1800); // Durasi loading splash screen
            } else if (splash) {
                // Langsung hapus dari DOM jika diakses via desktop/web agar menghemat memori browser
                splash.remove();
            }
        });
    </script>
    @yield('scripts')
</body>
</html>