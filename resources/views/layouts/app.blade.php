<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0F2F5F">
    <meta name="description"
        content="SMKN 7 Jember Go Global – Berkarakter, Berkarya, Berprestasi. Menuju Masa Depan Cerah dengan Karir Internasional Bersama SMKN 7 Jember.">

    <title>@yield('title', 'SMKN 7 JEMBER – GO GLOBAL | Berkarakter, Berkarya, Berprestasi')</title>

    <!-- Google Fonts: Plus Jakarta Sans & Merriweather -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Merriweather:ital,wght@0,400;0,700;1,400&display=swap"
        rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/smkn-7-go-global.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="min-h-screen flex flex-col bg-[#F8FAFC] text-[#1E293B] antialiased selection:bg-[#155EAA]/20 selection:text-[#0F2F5F]">
    <!-- Accessibility Skip Link -->
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-[#0F2F5F] focus:text-white focus:rounded-md focus:shadow-lg focus:outline-hidden">
        Lewati ke konten utama
    </a>

    <!-- Sticky Navbar -->
    @include('components.navbar')

    <!-- Main Content Area -->
    <main id="main-content" class="flex-grow">
        @yield('content')
    </main>

    <!-- Professional Footer -->
    @include('components.footer')

    <!-- Interactive Modals (Login placeholder, Konsultasi, Simulasi Finansial, Lightbox) -->
    @include('components.modals')

    <!-- Back to Top Floating Button -->
    <button id="btn-back-to-top" type="button" aria-label="Kembali ke atas"
        class="fixed bottom-6 right-6 z-40 p-3 rounded-full bg-[#155EAA] hover:bg-[#0F2F5F] text-white shadow-lg hover:shadow-xl transition-all duration-300 opacity-0 pointer-events-none translate-y-4 focus:outline-hidden focus:ring-2 focus:ring-[#155EAA] focus:ring-offset-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>
</body>

</html>
