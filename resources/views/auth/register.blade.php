<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F8FAFC]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0F2F5F">
    <title>Buat Akun Go Global | SMKN 7 JEMBER</title>

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
             BAGIAN KIRI: Visual Branding SMKN 7 Jember Go Global
             ============================================================== -->
        <div class="lg:w-5/12 relative bg-[#0F2F5F] text-white flex flex-col justify-between p-6 sm:p-8 lg:p-10 overflow-hidden min-h-[340px] lg:min-h-screen">
            
            <!-- Hero Japanese Background Asset -->
            <img src="{{ asset('images/hero-go-global.png') }}" 
                 alt="Siswa SMKN 7 Jember Go Global" 
                 class="absolute inset-0 w-full h-full object-cover object-right opacity-30 mix-blend-overlay">

            <!-- Deep Navy Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-[#0A1E3F] via-[#0F2F5F]/90 to-[#0F2F5F]/95" aria-hidden="true"></div>

            <!-- Top Left: Logo & Back Link -->
            <div class="relative z-10 flex items-center justify-between">
                <a href="{{ route('landing') }}" class="inline-flex items-center gap-3 group focus:outline-hidden" title="Kembali ke Beranda">
                    <img src="{{ asset('images/smkn-7-go-global.png') }}" 
                         alt="Logo SMKN 7 Jember Go Global" 
                         class="h-12 sm:h-14 w-auto object-contain filter drop-shadow-md brightness-110">
                </a>
                <a href="{{ route('login') }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-xs font-semibold text-slate-200 hover:text-white transition-colors backdrop-blur-xs">
                    <span>Sudah Ada Akun? Masuk</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

            <!-- Middle Left: Informative Content -->
            <div class="relative z-10 my-auto py-4 lg:py-1 max-w-md">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#166534]/50 border border-[#86EFAC]/30 backdrop-blur-xs mb-4">
                    <span class="w-2 h-2 rounded-full bg-[#86EFAC] animate-pulse"></span>
                    <span class="text-xs font-extrabold tracking-widest text-[#86EFAC] uppercase">
                        Registrasi Terintegrasi
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight leading-tight mb-3">
                    Satu Akun untuk Berbagai Akses Layanan Go Global
                </h1>

                <p class="text-slate-200 text-xs sm:text-sm leading-relaxed mb-4">
                    Daftarkan diri Anda untuk mengikuti seleksi magang Jepang, mengakses modul pembelajaran bahasa, mengajukan bimbingan konseling, atau memantau progres siswa.
                </p>

                <!-- Role Explanation Cards in Sidebar -->
                <div class="space-y-2.5 text-xs">
                    <div class="p-3 rounded-xl bg-white/10 border border-white/10 backdrop-blur-xs">
                        <strong class="text-[#FBBF24] block mb-0.5">🎓 Siswa & Alumni</strong>
                        <p class="text-slate-300 text-[11px] leading-relaxed">
                            Akses pendaftaran program magang, diklat bahasa Jepang, dan rekam jejak tracer study.
                        </p>
                    </div>
                    <div class="p-3 rounded-xl bg-white/10 border border-white/10 backdrop-blur-xs">
                        <strong class="text-sky-300 block mb-0.5">👨‍👩‍👧 Orang Tua / Wali</strong>
                        <p class="text-slate-300 text-[11px] leading-relaxed">
                            Transparansi pemantauan persiapan putra/putri dan kanal komunikasi langsung dengan tim BK.
                        </p>
                    </div>
                    <div class="p-3 rounded-xl bg-white/10 border border-white/10 backdrop-blur-xs">
                        <strong class="text-emerald-300 block mb-0.5">🏫 Guru & Tenaga Kependidikan</strong>
                        <p class="text-slate-300 text-[11px] leading-relaxed">
                            Pendampingan dan pembimbingan kejuruan serta evaluasi kesiapan siswa.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Bottom Left Notice -->
            <div class="relative z-10 pt-4 border-t border-white/15 text-[11px] text-slate-400">
                <span>Catatan: Akun Kepala Sekolah & Admin tidak dibuka untuk pendaftaran umum.</span>
            </div>
        </div>

        <!-- ==============================================================
             BAGIAN KANAN: Formulir Registrasi Publik
             ============================================================== -->
        <div class="lg:w-7/12 flex items-center justify-center p-4 sm:p-6 lg:p-8 bg-[#F8FAFC]">
            <div class="w-full max-w-xl bg-white rounded-3xl p-5 sm:p-6 lg:p-7 border border-slate-200/90 shadow-xl">
                
                <!-- Header Form -->
                <div class="mb-4">
                    <div class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-[#166534] mb-2">
                        <span>Pendaftaran Akun Baru</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0F2F5F] tracking-tight">
                        Buat Akun Go Global
                    </h2>
                    <p class="mt-2 text-xs sm:text-sm text-slate-600">
                        Daftarkan akun Anda untuk mengakses informasi, layanan, dan berbagai program SMKN 7 Jember Go Global.
                    </p>
                </div>

                <!-- Validation Errors Alert -->
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200/90 text-rose-900 text-xs leading-relaxed">
                        <div class="flex items-center gap-2 font-bold mb-1">
                            <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Periksa Kembali Data Pendaftaran:</span>
                        </div>
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Registration Form -->
                <form method="POST" action="{{ route('register') }}" class="space-y-3.5" id="register-form">
                    @csrf

                    <!-- 1. Pilihan Jenis Akun (4 Roles Only: Siswa, Alumni, Orang Tua, Guru) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Pilih Jenis Akun <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            
                            <!-- Option: Siswa -->
                            <label class="relative flex flex-col p-2 rounded-xl border-2 cursor-pointer transition-all role-card @if(old('role', 'siswa') === 'siswa') border-[#155EAA] bg-blue-50/50 @else border-slate-200 hover:border-slate-300 bg-white @endif">
                                <input type="radio" name="role" value="siswa" class="sr-only" @checked(old('role', 'siswa') === 'siswa') onchange="highlightRoleCard(this)">
                                <span class="text-lg mb-0.5">🎓</span>
                                <strong class="text-xs font-bold text-slate-900 leading-tight">Siswa</strong>
                                <span class="text-[10px] text-slate-500 mt-0">Siswa Aktif</span>
                            </label>

                            <!-- Option: Alumni -->
                            <label class="relative flex flex-col p-2 rounded-xl border-2 cursor-pointer transition-all role-card @if(old('role') === 'alumni') border-[#155EAA] bg-blue-50/50 @else border-slate-200 hover:border-slate-300 bg-white @endif">
                                <input type="radio" name="role" value="alumni" class="sr-only" @checked(old('role') === 'alumni') onchange="highlightRoleCard(this)">
                                <span class="text-lg mb-0.5">💼</span>
                                <strong class="text-xs font-bold text-slate-900 leading-tight">Alumni</strong>
                                <span class="text-[10px] text-slate-500 mt-0">Lulusan SMK</span>
                            </label>

                            <!-- Option: Orang Tua -->
                            <label class="relative flex flex-col p-2 rounded-xl border-2 cursor-pointer transition-all role-card @if(old('role') === 'orang_tua') border-[#155EAA] bg-blue-50/50 @else border-slate-200 hover:border-slate-300 bg-white @endif">
                                <input type="radio" name="role" value="orang_tua" class="sr-only" @checked(old('role') === 'orang_tua') onchange="highlightRoleCard(this)">
                                <span class="text-lg mb-0.5">👨‍👩‍👧</span>
                                <strong class="text-xs font-bold text-slate-900 leading-tight">Orang Tua</strong>
                                <span class="text-[10px] text-slate-500 mt-0">Wali Murid</span>
                            </label>

                            <!-- Option: Guru -->
                            <label class="relative flex flex-col p-2 rounded-xl border-2 cursor-pointer transition-all role-card @if(old('role') === 'guru') border-[#155EAA] bg-blue-50/50 @else border-slate-200 hover:border-slate-300 bg-white @endif">
                                <input type="radio" name="role" value="guru" class="sr-only" @checked(old('role') === 'guru') onchange="highlightRoleCard(this)">
                                <span class="text-lg mb-0.5">🏫</span>
                                <strong class="text-xs font-bold text-slate-900 leading-tight">Guru</strong>
                                <span class="text-[10px] text-slate-500 mt-0">Tenaga Pendidik</span>
                            </label>

                        </div>
                        <p class="text-[11px] text-slate-500 mt-1.5 italic">
                            * Akun Kepala Sekolah tidak tersedia pada formulir publik dan hanya dibuat secara administratif oleh pihak berwenang.
                        </p>
                    </div>

                    <!-- 2. Nama Lengkap Field -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}" 
                               required 
                               placeholder="Nama lengkap sesuai dokumen identitas resmi"
                               class="w-full px-4 py-3 rounded-xl border @error('name') border-rose-400 bg-rose-50/20 @else border-slate-200 bg-slate-50/50 @enderror text-xs sm:text-sm text-slate-900 focus:bg-white focus:border-[#155EAA] focus:ring-2 focus:ring-[#155EAA]/20 focus:outline-hidden transition-all">
                    </div>

                    <!-- 3. Email & Phone Fields (2 Columns) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Alamat Email Aktif <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   placeholder="nama@email.com"
                                   class="w-full px-4 py-3 rounded-xl border @error('email') border-rose-400 bg-rose-50/20 @else border-slate-200 bg-slate-50/50 @enderror text-xs sm:text-sm text-slate-900 focus:bg-white focus:border-[#155EAA] focus:ring-2 focus:ring-[#155EAA]/20 focus:outline-hidden transition-all">
                        </div>

                        <div>
                            <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Nomor Telepon / WhatsApp <span class="text-rose-500">*</span>
                            </label>
                            <input type="tel" 
                                   name="phone" 
                                   id="phone" 
                                   value="{{ old('phone') }}" 
                                   required 
                                   placeholder="Contoh: 081234567890"
                                   class="w-full px-4 py-3 rounded-xl border @error('phone') border-rose-400 bg-rose-50/20 @else border-slate-200 bg-slate-50/50 @enderror text-xs sm:text-sm text-slate-900 focus:bg-white focus:border-[#155EAA] focus:ring-2 focus:ring-[#155EAA]/20 focus:outline-hidden transition-all">
                        </div>
                    </div>

                    <!-- 4. Password & Confirm Password (2 Columns) with Show/Hide Toggle -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Kata Sandi <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" 
                                       name="password" 
                                       id="password" 
                                       required 
                                       autocomplete="new-password"
                                       placeholder="Minimal 8 karakter"
                                       class="w-full pl-4 pr-10 py-2.5 rounded-xl border @error('password') border-rose-400 bg-rose-50/20 @else border-slate-200 bg-slate-50/50 @enderror text-xs sm:text-sm text-slate-900 focus:bg-white focus:border-[#155EAA] focus:ring-2 focus:ring-[#155EAA]/20 focus:outline-hidden transition-all">
                                <button type="button" 
                                        onclick="toggleRegPassword('password', this)" 
                                        class="absolute right-3 top-3 text-slate-400 hover:text-slate-600 focus:outline-hidden"
                                        aria-label="Toggle password">
                                    👁
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Konfirmasi Sandi <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" 
                                       name="password_confirmation" 
                                       id="password_confirmation" 
                                       required 
                                       autocomplete="new-password"
                                       placeholder="Ulangi kata sandi"
                                       class="w-full pl-4 pr-10 py-3 rounded-xl border border-slate-200 bg-slate-50/50 text-xs sm:text-sm text-slate-900 focus:bg-white focus:border-[#155EAA] focus:ring-2 focus:ring-[#155EAA]/20 focus:outline-hidden transition-all">
                                <button type="button" 
                                        onclick="toggleRegPassword('password_confirmation', this)" 
                                        class="absolute right-3 top-3 text-slate-400 hover:text-slate-600 focus:outline-hidden"
                                        aria-label="Toggle password confirmation">
                                    👁
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Verification Notice Box -->
                    <div class="p-3 rounded-xl bg-amber-50/80 border border-amber-200/90 text-amber-900 text-xs leading-relaxed flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <strong>Pemberitahuan Status Akun:</strong>
                            Akun baru berstatus <em>"Menunggu Verifikasi"</em>. Akses data sensitif siswa akan dibuka setelah verifikasi data sekolah selesai.
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" 
                                class="w-full flex items-center justify-center gap-2 py-2.5 px-5 rounded-xl bg-[#166534] hover:bg-[#14532d] text-white text-xs sm:text-sm font-bold tracking-wide uppercase shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 cursor-pointer">
                            <span>Kirim Pendaftaran Akun</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>

                <!-- Footer Link to Login -->
                <div class="mt-4 text-center text-xs text-slate-600">
                    Sudah memiliki akun terdaftar? 
                    <a href="{{ route('login') }}" class="font-bold text-[#155EAA] hover:text-[#0F2F5F] hover:underline">
                        Masuk Disini &rarr;
                    </a>
                </div>

            </div>
        </div>

    </div>

    <!-- Client Script for Role Card Highlighting & Password Toggle -->
    <script>
        function highlightRoleCard(radio) {
            document.querySelectorAll('.role-card').forEach((card) => {
                card.classList.remove('border-[#155EAA]', 'bg-blue-50/50');
                card.classList.add('border-slate-200', 'bg-white');
            });
            const parent = radio.closest('.role-card');
            if (parent) {
                parent.classList.add('border-[#155EAA]', 'bg-blue-50/50');
                parent.classList.remove('border-slate-200', 'bg-white');
            }
        }

        function toggleRegPassword(id, btn) {
            const input = document.getElementById(id);
            if (!input) return;
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            btn.textContent = isPassword ? '🔒' : '👁';
        }
    </script>
</body>
</html>
