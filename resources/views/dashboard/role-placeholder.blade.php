<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F8FAFC]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0F2F5F">
    <title>Dashboard {{ Auth::user()->getRoleDisplayName() ?? 'Pengguna' }} | SMKN 7 JEMBER GO GLOBAL</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/smkn-7-go-global.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col font-sans text-[#1E293B] antialiased bg-[#F8FAFC]">

    <!-- Simple Top Navbar for Role Dashboard -->
    <header class="bg-white border-b border-slate-200/80 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/smkn-7-go-global.png') }}" alt="Logo SMKN 7 Jember Go Global" class="h-11 sm:h-12 w-auto object-contain">
            </a>

            <div class="flex items-center gap-3">
                <a href="{{ route('landing') }}" class="text-xs font-semibold text-slate-600 hover:text-[#155EAA] px-3 py-2 rounded-lg hover:bg-slate-50 transition-colors">
                    &larr; Ke Website Utama
                </a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-600 text-xs font-bold transition-colors cursor-pointer">
                        Keluar (Logout)
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow py-12 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto w-full">
        
        <!-- Status Alert if Any -->
        @if (session('status'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200/90 text-emerald-900 text-xs leading-relaxed flex items-start gap-3">
                <svg class="w-5 h-5 text-[#166534] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <strong class="font-bold block">Status Registrasi:</strong>
                    {{ session('status') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200/90 text-rose-900 text-xs leading-relaxed flex items-start gap-3">
                <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <strong class="font-bold block">Pemberitahuan Hak Akses:</strong>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Card Container -->
        <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200/90 shadow-lg text-center space-y-6">
            
            <!-- Role Avatar Icon -->
            <div class="w-20 h-20 rounded-3xl mx-auto flex items-center justify-center text-3xl shadow-sm border border-slate-100
                @if(Auth::user()->isSiswa()) bg-blue-50 text-[#155EAA]
                @elseif(Auth::user()->isAlumni()) bg-purple-50 text-purple-700
                @elseif(Auth::user()->isOrangTua()) bg-orange-50 text-orange-700
                @elseif(Auth::user()->isGuru()) bg-emerald-50 text-[#166534]
                @else bg-slate-50 text-slate-700 @endif">
                @if(Auth::user()->isSiswa()) 🎓
                @elseif(Auth::user()->isAlumni()) 💼
                @elseif(Auth::user()->isOrangTua()) 👨‍👩‍👧
                @elseif(Auth::user()->isGuru()) 👩‍🏫
                @else 👤 @endif
            </div>

            <!-- Greeting and User Details -->
            <div>
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-bold mb-3
                    @if(Auth::user()->isAktif()) bg-emerald-50 text-[#166534] border border-emerald-200
                    @else bg-amber-50 text-amber-800 border border-amber-200 @endif">
                    <span class="w-2 h-2 rounded-full @if(Auth::user()->isAktif()) bg-emerald-500 @else bg-amber-500 @endif"></span>
                    <span>Status Akun: {{ Auth::user()->getStatusDisplayName() }}</span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0F2F5F] tracking-tight">
                    Halo, {{ Auth::user()->name }}!
                </h1>

                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Terdaftar sebagai: <strong class="text-slate-800">{{ Auth::user()->getRoleDisplayName() }}</strong> &bull; {{ Auth::user()->email }}
                </p>
            </div>

            <!-- Descriptive Message -->
            <div class="max-w-xl mx-auto p-5 rounded-2xl bg-slate-50 border border-slate-200 text-xs sm:text-sm text-slate-600 leading-relaxed text-left space-y-3">
                <p>
                    Selamat datang di platform <strong>SMKN 7 Jember Go Global</strong>. Halaman modul khusus untuk peran <strong>{{ Auth::user()->getRoleDisplayName() }}</strong> sedang dalam proses integrasi fitur lebih lanjut.
                </p>

                @if(Auth::user()->isMenungguVerifikasi())
                    <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs">
                        <strong>📌 Menunggu Verifikasi Sekolah:</strong>
                        <p class="mt-1">Akun Anda telah tersimpan dengan aman di sistem. Petugas tata usaha dan tim BKK SMKN 7 Jember akan mencocokkan nomor kontak dan identitas Anda sebelum membuka seluruh fitur pendaftaran program magang ke Jepang.</p>
                    </div>
                @else
                    <p class="text-emerald-700 font-semibold">
                        ✓ Akun Anda telah terverifikasi secara resmi oleh tim sekolah.
                    </p>
                @endif
            </div>

            <!-- Quick Action Links -->
            <div class="pt-4 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('landing') }}" class="px-5 py-2.5 rounded-xl bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold transition-all shadow-sm">
                    Kembali ke Beranda Sekolah &rarr;
                </a>
                <a href="{{ route('landing') }}#akses" class="px-5 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold transition-all">
                    Akses Fitur & Layanan Publik
                </a>
            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer class="py-6 border-t border-slate-200 text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} SMKN 7 Jember &middot; Go Global. Berkarakter, Berkarya, Berprestasi.
    </footer>
</body>
</html>
