@extends('layouts.app')

@section('title', 'Dashboard - Grocademy')

@section('content')
    <h1>Selamat Datang, {{ Auth::user()->first_name }}!</h1>
    <p>Silakan pilih menu di samping untuk mulai belajar atau menjelajahi kursus baru.</p>
@endsection