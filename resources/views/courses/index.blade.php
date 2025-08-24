@extends('layouts.app')

@section('title', 'Browse Courses - Grocademy')

@section('content')
    <h1>Temukan Kursus Terbaikmu!</h1>
    
    <div class="search-bar">
        <form action="{{ route('courses.index') }}" method="GET" style="display: flex; width: 100%; gap: 1rem;">
            <input type="text" name="search" placeholder="Cari kursus berdasarkan judul, instruktur..." value="{{ request('search') }}">
            <label for="per_page_select" id="per_page_select" class="sr-only">Item per Halaman</label>
            <select name="per_page" onchange="this.form.submit()">
                <option value="9" {{ request('per_page', 9) == 9 ? 'selected' : '' }}>9 per Halaman</option>
                <option value="18" {{ request('per_page') == 18 ? 'selected' : '' }}>18 per Halaman</option>
                <option value="36" {{ request('per_page') == 36 ? 'selected' : '' }}>36 per Halaman</option>
            </select>
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
                    <p class="card-price">Rp{{ number_format($course->price, 0, ',', '.') }}</p>
                    <div class="card-footer">
                        @auth
                            @if (in_array($course->id, $purchasedCoursesIds))
                                <span class="owned-badge">Sudah Dimiliki</span>
                            @else
                                <a href="{{ route('courses.show', $course) }}" class="btn btn-secondary btn-block">Lihat Detail</a>
                            @endif
                        @else
                            <a href="{{ route('courses.show', $course) }}" class="btn btn-secondary btn-block">Lihat Detail</a>
                        @endauth
                    </div>
                </div>
            </div>
        @empty
            <p>Tidak ada kursus yang ditemukan dengan kriteria pencarian Anda.</p>
        @endforelse
    </div>
    
    <div class="pagination" style="display: flex; justify-content: center; margin-top: 2rem;">
        {{ $courses->appends(request()->query())->links() }}
    </div>
@endsection