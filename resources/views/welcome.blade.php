@extends('layouts.app')

@section('title', 'SMKN 7 JEMBER – GO GLOBAL | Berkarakter, Berkarya, Berprestasi')

@section('content')
    {{-- B. Hero Section --}}
    @include('components.hero')

    {{-- C. Akses Cepat (10 Feature Cards with Varied Pastel Tones) --}}
    @include('components.quick-access')

    {{-- D. Pengumuman Terbaru (2-Column Realistic Announcements) --}}
    @include('components.announcements')

    {{-- E. Konseling & Intervensi Dini (Student Guidance & MCU Prep) --}}
    @include('components.counseling')

    {{-- F. Inspirasi Alumni (Interactive Stories & Quotes) --}}
    @include('components.alumni')

    {{-- G. Karir Global untuk Masa Depan (International Career Pathways & Roadmap) --}}
    @include('components.career')

    {{-- H. Galeri Kegiatan --}}
    @include('components.gallery')

@endsection
