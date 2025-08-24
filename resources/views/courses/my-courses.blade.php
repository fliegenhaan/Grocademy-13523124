@extends('layouts.app')

@section('title', 'My Courses - Grocademy')

@section('content')
    <h1>Kursus yang Telah Anda Beli</h1>
    
    <div class="search-bar">
        <form action="{{ route('courses.my') }}" method="GET" style="display: flex; width: 100%; gap: 1rem;">
            <input type="text" name="search" placeholder="Cari di kursus Anda..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>
    </div>

    <div class="course-grid">
        @forelse ($courses as $course)
            <div class="card">
                <img src="{{ asset('storage/' . $course->thumbnail_image) }}" alt="{{ $course->title }}" class="card-img">
                <div class="card-body">
                    <h2 class="card-title">{{ $course->title }}</h2>
                    <p class="card-instructor">by {{ $course->instructor }}</p>
                    <div class="card-footer">
                        <a href="{{ route('modules.index', $course) }}" class="btn btn-primary btn-block">
                            Lanjutkan Belajar
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <p>Anda belum membeli kursus apapun. <a href="{{ route('courses.index') }}">Cari kursus sekarang!</a></p>
        @endforelse
    </div>
    
    <div class="pagination" style="display: flex; justify-content: center; margin-top: 2rem;">
        {{ $courses->appends(request()->query())->links() }}
    </div>
@endsection