<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Data Analytics</title>
    
    <!-- Impor Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Impor Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Kustomisasi font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #020617; /* slate-950 */
            overflow: hidden;
        }

        /* Container untuk animasi latar belakang */
        .background-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        /* Partikel animasi */
        .particle {
            position: absolute;
            background-color: rgba(0, 194, 255, 0.15);
            border-radius: 50%;
            animation: float 25s infinite linear;
            bottom: -150px;
        }

        @keyframes float {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(-120vh) rotate(720deg);
                opacity: 0;
            }
        }

        /* Animasi untuk form login saat muncul */
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .form-container {
            animation: fade-in-up 0.8s ease-out forwards;
        }
    </style>
</head>
<body>
    <!-- Latar Belakang Animasi -->
    <div class="background-animation" id="background-animation"></div>

    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="w-full max-w-md p-8 space-y-6 bg-slate-900/50 backdrop-blur-2xl rounded-2xl shadow-2xl border border-slate-700/50 form-container">
            
            <!-- Header Form -->
            <div class="text-center">
                <div class="inline-block p-3 bg-slate-800/50 rounded-full mb-4 border border-slate-700">
                    <i data-lucide="bar-chart-big" class="w-8 h-8 text-cyan-400"></i>
                </div>
                <h1 class="text-3xl font-bold text-white">Analytics Login</h1>
                <p class="mt-2 text-sm text-slate-400">Masuk untuk mengakses dashboard Anda</p>
            </div>

            <!-- Form Login -->
            <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-6">
                @csrf

                <!-- Menampilkan error autentikasi umum -->
                @if ($errors->has('email') || $errors->has('password'))
                    @if (!$errors->has('email'))
                        <div class="p-3 mb-4 text-sm text-red-400 rounded-lg bg-red-900/50 border border-red-500/30 flex items-center" role="alert">
                           <i data-lucide="alert-triangle" class="w-4 h-4 mr-2"></i>
                           <span>Email atau password yang Anda masukkan salah.</span>
                        </div>
                    @endif
                @endif


                <!-- Input Email -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                        <i data-lucide="mail" class="w-5 h-5 text-slate-500"></i>
                    </div>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        autocomplete="email" 
                        required 
                        value="{{ old('email') }}"
                        class="block w-full pl-12 pr-4 py-3 text-white placeholder-slate-500 bg-slate-800/60 border @error('email') border-red-500/50 @else border-slate-700 @enderror rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-300"
                        placeholder="Alamat email">
                </div>
                @error('email')
                    <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                @enderror

                <!-- Input Password -->
                <div class="relative mt-4">
                     <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                        <i data-lucide="lock" class="w-5 h-5 text-slate-500"></i>
                    </div>
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        autocomplete="current-password" 
                        required
                        class="block w-full pl-12 pr-12 py-3 text-white placeholder-slate-500 bg-slate-800/60 border @error('password') border-red-500/50 @else border-slate-700 @enderror rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-300"
                        placeholder="Password">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3.5">
                        <button type="button" id="togglePassword" class="text-slate-500 hover:text-cyan-400 transition-colors">
                            <i id="eyeIcon" data-lucide="eye"></i>
                        </button>
                    </div>
                </div>
                 @error('password')
                    <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                @enderror

                <!-- Tombol Submit -->
                <div>
                    <button type="submit" class="group relative flex justify-center w-full px-4 py-3 mt-8 text-sm font-semibold text-slate-900 bg-cyan-400 border border-transparent rounded-lg hover:bg-cyan-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900 focus:ring-cyan-500 transition-all duration-300 transform hover:scale-105">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i data-lucide="log-in" class="h-5 w-5 text-cyan-800 group-hover:text-cyan-900 transition-colors"></i>
                        </span>
                        Masuk
                    </button>
                </div>
            </form>
            
            <!-- Link ke Halaman Registrasi -->
            @if (Route::has('register'))
                <p class="mt-6 text-sm text-center text-slate-400">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="font-medium text-cyan-400 hover:text-cyan-300">
                        Daftar di sini
                    </a>
                </p>
            @endif
        </div>
    </div>

    <script>
        // Inisialisasi Lucide Icons
        lucide.createIcons();

        // Logika untuk toggle password
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Ganti ikon
            if (type === 'password') {
                eyeIcon.setAttribute('data-lucide', 'eye');
            } else {
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            }
            lucide.createIcons(); // Render ulang ikon
        });

        // Logika untuk animasi latar belakang
        const bgAnimation = document.getElementById('background-animation');
        const numberOfParticles = 30;

        for (let i = 0; i < numberOfParticles; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            
            const size = Math.random() * 100 + 20;
            particle.style.width = `${size}px`;
            particle.style.height = `${size}px`;
            
            particle.style.left = `${Math.random() * 100}%`;
            particle.style.animationDuration = `${Math.random() * 15 + 10}s`;
            particle.style.animationDelay = `${Math.random() * 5}s`;
            
            bgAnimation.appendChild(particle);
        }
    </script>
</body>
</html>
