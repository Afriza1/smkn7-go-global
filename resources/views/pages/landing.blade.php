@extends('layouts.app')

@section('title', 'SMKN 7 JEMBER – GO GLOBAL | Berkarakter, Berkarya, Berprestasi')

@section('content')
    @include('components.hero')
    @include('components.quick-access')
    @include('components.announcements')
    @include('components.counseling')
    @include('components.alumni')
    @include('components.career')
    @include('components.gallery')
@endsection
