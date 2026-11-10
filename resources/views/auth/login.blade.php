<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F8FAFC]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0F2F5F">
    <title>Masuk ke Portal Go Global | SMKN 7 JEMBER</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Merriweather:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/smkn-7-go-global.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col font-sans text-[#1E293B] antialiased bg-[#F8FAFC]">

    <!-- Main Container: 2-Column Split Layout -->
    <div class="min-h-full flex flex-col lg:flex-row flex-grow">
        
        <!-- ==============================================================
             BAGIAN KIRI: Visual Branding SMKN 7 Jember Go Global & Jepang
             ============================================================== -->
        <div class="lg:w-1/2 relative bg-[#0F2F5F] text-white flex flex-col justify-between p-6 sm:p-8 lg:p-10 overflow-hidden min-h-[380px] lg:min-h-screen">
            
            <!-- Hero Japanese Background Asset -->
            <img src="{{ asset('images/hero-go-global.png') }}" 
                 alt="Siswa SMKN 7 Jember Go Global" 
                 class="absolute inset-0 w-full h-full object-cover object-right opacity-30 mix-blend-overlay">

            <!-- Deep Navy Gradient Overlay for Crisp Text Contrast -->
            <div class="absolute inset-0 bg-gradient-to-t from-[#0A1E3F] via-[#0F2F5F]/90 to-[#0F2F5F]/95" aria-hidden="true"></div>

            <!-- Top Left: Logo & Back Link -->
            <div class="relative z-10 flex items-center justify-between">
                <a href="{{ route('landing') }}" class="inline-flex items-center gap-3 group focus:outline-hidden" title="Kembali ke Beranda">
                    <img src="{{ asset('images/smkn-7-go-global.png') }}" 
                         alt="Logo SMKN 7 Jember Go Global" 
                         class="h-12 sm:h-14 w-auto object-contain filter drop-shadow-md brightness-110">
                </a>
                <a href="{{ route('landing') }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-xs font-semibold text-slate-200 hover:text-white transition-colors backdrop-blur-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Beranda</span>
                </a>
            </div>

            <!-- Middle Left: Tagline, Narrative & Japanese Aesthetic Accents -->
            <div class="relative z-10 my-auto py-5 lg:py-2 max-w-lg">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#155EAA]/40 border border-[#FBBF24]/30 backdrop-blur-xs mb-4">
                    <span class="w-2 h-2 rounded-full bg-[#FBBF24] animate-pulse"></span>
                    <span class="text-xs font-extrabold tracking-widest text-[#FBBF24] uppercase">
                        Berkarakter, Berkarya, Berprestasi
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white tracking-tight leading-tight mb-3">
                    Gerbang Menuju Peluang Karir Internasional di Jepang
                </h1>

                <p class="text-slate-200 text-xs sm:text-sm leading-relaxed mb-4">
                    Masuk ke platform terpadu SMKN 7 Jember untuk memantau tahapan seleksi, persiapan dokumen keberangkatan, modul bimbingan bahasa, hingga monitoring penempatan kerja.
                </p>

                <!-- Value Highlights -->
                <div class="space-y-2 text-xs text-slate-300">
                    <div class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-[11px]">✓</span>
                        <span>Akses resmi program Tokutei Ginou & Magang Industri Jepang</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-[11px]">✓</span>
                        <span>Portal pemantauan terpadu untuk Siswa, Alumni, Guru & Orang Tua</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-[11px]">✓</span>
                        <span>Didukung kemitraan asosiasi industri resmi di berbagai prefektur Jepang</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Left: Institutional Identity -->
            <div class="relative z-10 pt-4 border-t border-white/15 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[11px] text-slate-400">
                <span>SMKN 7 Jember &middot; Semboro, Kabupaten Jember</span>
                <span class="text-[#FBBF24]">Portal Go Global 2026/2027</span>
            </div>
        </div>

        <!-- ==============================================================
             BAGIAN KANAN: Formulir Login Profesional
             ============================================================== -->
        <div class="lg:w-1/2 flex items-center justify-center p-4 sm:p-6 lg:p-8 bg-[#F8FAFC]">
            <div class="w-full max-w-md bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-xl">
                
                <!-- Header Form -->
                <div class="mb-4">
                    <div class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-[#155EAA] mb-2">
                        <span>Portal Masuk Akun</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0F2F5F] tracking-tight">
                        Selamat Datang Kembali
                    </h2>
                    <p class="mt-2 text-xs sm:text-sm text-slate-600">
                        Masukkan email dan password akun Anda untuk mengakses dashboard layanan.
                    </p>
                </div>

                <!-- Flash Status / Verification Notification -->
                @if (session('status'))
                    <div class="mb-4 p-3 rounded-2xl bg-emerald-50 border border-emerald-200/90 text-emerald-900 text-xs leading-relaxed flex items-start gap-3">
                        <svg class="w-5 h-5 text-[#166534] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <strong class="font-bold block">Pemberitahuan Sistem:</strong>
                            {{ session('status') }}
                        </div>
                    </div>
                @endif

                <!-- Validation Errors Alert -->
                @if ($errors->any())
                    <div class="mb-4 p-3 rounded-2xl bg-rose-50 border border-rose-200/90 text-rose-900 text-xs leading-relaxed">
                        <div class="flex items-center gap-2 font-bold mb-1">
                            <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Gagal Masuk:</span>
                        </div>
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- The Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4" id="login-form">
                    @csrf

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Alamat Email <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus 
                                   autocomplete="username"
                                   placeholder="nama@smkn7jember.sch.id"
                                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border @error('email') border-rose-400 bg-rose-50/20 @else border-slate-200 bg-slate-50/50 @enderror text-xs sm:text-sm text-slate-900 focus:bg-white focus:border-[#155EAA] focus:ring-2 focus:ring-[#155EAA]/20 focus:outline-hidden transition-all">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Password Field with Show/Hide Toggle -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Kata Sandi <span class="text-rose-500">*</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-[#155EAA] hover:underline">
                                    Lupa sandi?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   required 
                                   autocomplete="current-password"
                                   placeholder="Masukkan password Anda"
                                   class="w-full pl-10 pr-11 py-2.5 rounded-xl border @error('password') border-rose-400 bg-rose-50/20 @else border-slate-200 bg-slate-50/50 @enderror text-xs sm:text-sm text-slate-900 focus:bg-white focus:border-[#155EAA] focus:ring-2 focus:ring-[#155EAA]/20 focus:outline-hidden transition-all">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <!-- Password Toggle Button -->
                            <button type="button" 
                                    id="toggle-password-btn" 
                                    class="absolute right-3.5 top-2.5 text-slate-400 hover:text-slate-600 focus:outline-hidden p-0.5" 
                                    aria-label="Tampilkan atau sembunyikan kata sandi">
                                <svg id="eye-icon-show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="eye-icon-hide" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me Checkbox -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                            <input type="checkbox" 
                                   name="remember" 
                                   id="remember"
                                   class="w-4 h-4 rounded text-[#155EAA] border-slate-300 focus:ring-[#155EAA]">
                            <span>Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" 
                                id="btn-submit-login"
                                class="w-full flex items-center justify-center gap-2 py-2.5 px-5 rounded-xl bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs sm:text-sm font-bold tracking-wide uppercase shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 cursor-pointer">
                            <span>Masuk ke Akun</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>

                <!-- Quick Testing Helper: Demo Accounts -->
                <div class="mt-5 pt-4 border-t border-slate-200/80">
                    <span class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 text-center">
                        Akun Contoh Pengujian (Klik untuk Isi Cepat):
                    </span>
                    <div class="grid grid-cols-2 gap-2 text-[11px]">
                        <button type="button" 
                                onclick="fillLoginForm('kepala.sekolah@smkn7jember.sch.id', 'password123')"
                                class="p-2 rounded-xl border border-blue-200 bg-blue-50/70 hover:bg-blue-100 text-[#0F2F5F] font-bold text-left transition-colors flex items-center justify-between">
                            <span>👔 Kepala Sekolah</span>
                            <span class="text-[10px] text-blue-600 font-normal">Pilih</span>
                        </button>
                        <button type="button" 
                                onclick="fillLoginForm('siswa@smkn7jember.sch.id', 'password123')"
                                class="p-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-left transition-colors flex items-center justify-between">
                            <span>🎓 Siswa Aktif</span>
                            <span class="text-[10px] text-slate-500 font-normal">Pilih</span>
                        </button>
                        <button type="button" 
                                onclick="fillLoginForm('alumni@smkn7jember.sch.id', 'password123')"
                                class="p-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-left transition-colors flex items-center justify-between">
                            <span>💼 Alumni Jepang</span>
                            <span class="text-[10px] text-slate-500 font-normal">Pilih</span>
                        </button>
                        <button type="button" 
                                onclick="fillLoginForm('guru@smkn7jember.sch.id', 'password123')"
                                class="p-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-left transition-colors flex items-center justify-between">
                            <span>👩‍🏫 Guru / BK</span>
                            <span class="text-[10px] text-slate-500 font-normal">Pilih</span>
                        </button>
                    </div>
                </div>

                <!-- Footer Link to Register -->
                <div class="mt-4 text-center text-xs text-slate-600">
                    Belum memiliki akun? 
                    <a href="{{ route('register') }}" class="font-bold text-[#155EAA] hover:text-[#0F2F5F] hover:underline">
                        Daftar Akun Baru Disini &rarr;
                    </a>
                </div>

            </div>
        </div>

    </div>

    <!-- Client Script for Password Toggle and Quick Fill -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('toggle-password-btn');
            const passwordInput = document.getElementById('password');
            const eyeShow = document.getElementById('eye-icon-show');
            const eyeHide = document.getElementById('eye-icon-hide');

            toggleBtn?.addEventListener('click', () => {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                eyeShow?.classList.toggle('hidden', isPassword);
                eyeHide?.classList.toggle('hidden', !isPassword);
            });
        });

        function fillLoginForm(email, password) {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            if (emailInput && passwordInput) {
                emailInput.value = email;
                passwordInput.value = password;
                emailInput.focus();
            }
        }
    </script>
</body>
</html>
