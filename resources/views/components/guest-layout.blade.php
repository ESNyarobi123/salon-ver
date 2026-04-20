@props(['title' => 'TIPTAP |  ', 'showTopLogo' => true])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/jpeg" href="{{ asset('logo.jpeg') }}">
        <link rel="shortcut icon" href="{{ asset('logo.jpeg') }}">

        <!-- Premium Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            * {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            }
            
            body { 
                background: #0f0a1e;
                min-height: 100vh;
            }

            /* Salon photo backdrop (online) + dark overlay */
            .salon-backdrop::before {
                content: '';
                position: fixed;
                inset: 0;
                z-index: 0;
                background-image:
                    linear-gradient(135deg, rgba(15, 10, 30, 0.92) 0%, rgba(15, 10, 30, 0.72) 45%, rgba(6, 182, 212, 0.10) 100%),
                    url('https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=2800,fit=crop/Yg2y44MM36I5P7eD/whatsapp-image-2025-07-13-at-15.44.55_a3d65a80-AoPJ44L0Q8u3Zolg.jpg');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                filter: saturate(1.05) contrast(1.05);
                transform: scale(1.02);
            }
            .salon-backdrop::after {
                content: '';
                position: fixed;
                inset: 0;
                z-index: 0;
                background:
                    radial-gradient(700px 500px at 12% 18%, rgba(139, 92, 246, 0.16) 0%, transparent 60%),
                    radial-gradient(700px 500px at 88% 70%, rgba(6, 182, 212, 0.14) 0%, transparent 60%),
                    radial-gradient(1000px 700px at 50% 110%, rgba(0, 0, 0, 0.40) 0%, transparent 60%);
                pointer-events: none;
            }

            /* Gradient Text */
            .gradient-text {
                background: linear-gradient(135deg, #8b5cf6 0%, #06b6d4 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            /* Glassmorphism */
            .glass-card {
                background: rgba(28, 22, 51, 0.6);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.08);
            }

            /* Animations */
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-15px); }
            }
            
            @keyframes pulse-glow {
                0%, 100% { box-shadow: 0 0 30px rgba(139, 92, 246, 0.3); }
                50% { box-shadow: 0 0 60px rgba(139, 92, 246, 0.5); }
            }

            .animate-float { animation: float 6s ease-in-out infinite; }
            .animate-pulse-glow { animation: pulse-glow 3s ease-in-out infinite; }

            /* Mobile Optimizations */
            @media (max-width: 640px) {
                .glass-card {
                    background: rgba(28, 22, 51, 0.8);
                    backdrop-filter: blur(10px);
                    -webkit-backdrop-filter: blur(10px);
                }
                .animate-float {
                    animation: none;
                }
            }

            /* Touch-friendly tap targets */
            @media (hover: none) {
                button, a {
                    min-height: 44px;
                }
            }
            ::-webkit-scrollbar { width: 6px; }
            ::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.02); }
            ::-webkit-scrollbar-thumb {
                background: linear-gradient(180deg, rgba(139, 92, 246, 0.5) 0%, rgba(6, 182, 212, 0.5) 100%);
                border-radius: 10px;
            }
        </style>
    </head>
    <body class="font-sans antialiased text-white salon-backdrop">
        <!-- Background Effects -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden">
            <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-violet-600/10 rounded-full blur-[150px] -mr-48 -mt-48"></div>
            <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-cyan-600/10 rounded-full blur-[150px] -ml-48 -mb-48"></div>
        </div>

        <div class="min-h-screen flex flex-col justify-center items-center pt-4 sm:pt-0 px-3 sm:px-4 relative z-10">
            <!-- Auth card -->
            <div class="flex flex-col items-center justify-center w-full">
                @if($showTopLogo)
                    <a href="/" class="flex items-center gap-2 sm:gap-3 group mb-6 sm:mb-8">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full flex items-center justify-center shadow-xl shadow-violet-500/30 transform group-hover:rotate-12 transition-all duration-500 animate-pulse-glow overflow-hidden">
                            <img src="{{ asset('logo.jpeg') }}" alt="TIPTAP Logo" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <span class="text-xl sm:text-2xl font-black text-white tracking-tight block leading-none hidden">TIP<span class="gradient-text">TAP</span></span>
                        </div>
                    </a>
                @endif

                <div class="w-full sm:max-w-md glass-card rounded-2xl sm:rounded-3xl p-5 sm:p-8 shadow-2xl shadow-black/50 relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-32 h-32 sm:w-40 sm:h-40 bg-violet-500/10 rounded-full blur-2xl sm:blur-3xl"></div>
                    <div class="absolute -bottom-10 -left-10 w-32 h-32 sm:w-40 sm:h-40 bg-cyan-500/10 rounded-full blur-2xl sm:blur-3xl"></div>
                    <div class="relative z-10">
                        {{ $slot }}
                    </div>
                </div>

                <p class="mt-6 sm:mt-8 text-white/30 text-xs font-medium text-center">&copy; {{ date('Y') }} TIPTAP. All rights reserved.</p>
            </div>
        </div>
    </body>
</html>
