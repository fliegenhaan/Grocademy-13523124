<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Browse Courses - Grocademy</title>
    <style>
        /* CSS sederhana untuk tampilan kartu */
        body { font-family: sans-serif; }
        .container { max-width: 1200px; margin: auto; padding: 20px; }
        .search-bar { margin-bottom: 20px; }
        .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .card { border: 1px solid #ddd; border-radius: 8px; padding: 15px; text-align: center; }
        .card img { max-width: 100%; height: auto; border-radius: 4px; }
        .pagination { margin-top: 30px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Temukan Kursus Terbaikmu!</h1>
        
        <div class="search-bar">
            <form action="{{ route('courses.index') }}" method="GET">
                <input type="text" name="search" placeholder="Cari kursus..." value="{{ request('search') }}" style="width: 300px;">
                
                <select name="per_page" onchange="this.form.submit()">
                    <option value="9" {{ request('per_page') == 9 ? 'selected' : '' }}>9 per Halaman</option>
                    <option value="18" {{ request('per_page') == 18 ? 'selected' : '' }}>18 per Halaman</option>
                    <option value="36" {{ request('per_page') == 36 ? 'selected' : '' }}>36 per Halaman</option>
                </select>
                
                <button type="submit">Cari</button>
            </form>
        </div>
        
        <div class="pagination">
            {{ $courses->appends(request()->query())->links() }}
        </div>

        <div class="grid">
            @forelse ($courses as $course)
                <div class="card">
                    <img src="{{ $course->thumbnail_image }}" alt="{{ $course->title }}">
                    <h3>{{ $course->title }}</h3>
                    <p>by {{ $course->instructor }}</p>
                    <h4>Rp{{ number_format($course->price, 0, ',', '.') }}</h4>
                    
                    @auth
                        @if (in_array($course->id, $purchasedCoursesIds))
                            <p style="color: green;"><strong>Sudah Dimiliki</strong></p>
                        @else
                            <a href="{{ route('courses.show', $course) }}">Lihat Detail</a>
                        @endif
                    @else
                        <a href="{{ route('courses.show', $course) }}">Lihat Detail</a>
                    @endauth
                </div>
            @empty
                <p>Tidak ada kursus yang ditemukan.</p>
            @endforelse
        </div>
        
        <div class="pagination">
            {{ $courses->links() }}
        </div>
    </div>
</body>
</html>