@extends('layouts.app')

@section('title', $course->title . ' - Grocademy')

@section('content')
    <div class="course-detail-header">
        <img src="{{ asset('storage/' . $course->thumbnail_image) }}" alt="{{ $course->title }}">
        <div class="course-detail-content">
            <h1>{{ $course->title }}</h1>
            <p><strong>Oleh:</strong> {{ $course->instructor }}</p>
            <h3>Deskripsi</h3>
            <p>{{ $course->description }}</p>
            
            <h3>Topik yang Akan Dipelajari</h3>
            <ul>
                @foreach ($course->topics as $topic)
                    <li>{{ $topic }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="auth-card" style="margin-top: 1.5rem;">
        <h2 style="text-align:center; font-size: 1.5rem;">Harga: Rp{{ number_format($course->price, 0, ',', '.') }}</h2>
        
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @auth
            @if ($isPurchased)
                <a href="{{ route('modules.index', $course) }}" class="btn btn-primary btn-block" style="background-color: var(--success-color);">Lanjutkan Belajar</a>
            @else
                <form action="{{ route('courses.buy', $course) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-block">Beli Kursus Ini</button>
                </form>
            @endif
        @else
            <p style="text-align:center;"><a href="{{ route('login') }}">Login untuk membeli kursus ini</a></p>
        @endauth
    </div>
@endsection