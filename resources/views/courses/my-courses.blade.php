<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Courses - Grocademy</title>
    <style>
        /* (Anda bisa menggunakan CSS yang sama persis seperti di index.blade.php) */
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
        <h1>Kursus yang Telah Anda Beli</h1>
        
        <div class="search-bar">
            <form action="{{ route('courses.my') }}" method="GET">
                <input type="text" name="search" placeholder="Cari di kursus Anda..." value="{{ request('search') }}" style="width: 300px;">
                <button type="submit">Cari</button>
            </form>
        </div>

        <div class="grid">
            @forelse ($courses as $course)
                <div class="card">
                    <img src="{{ $course->thumbnail_image }}" alt="{{ $course->title }}">
                    <h3>{{ $course->title }}</h3>
                    <p>by {{ $course->instructor }}</p>
                    
                    <a href="{{ route('modules.index', $course) }}" style="background-color: green; color: white; padding: 10px; text-decoration: none;">
                        Lanjutkan Belajar
                    </a>
                </div>
            @empty
                <p>Anda belum membeli kursus apapun. <a href="{{ route('courses.index') }}">Cari kursus sekarang!</a></p>
            @endforelse
        </div>
        
        <div class="pagination">
            {{ $courses->appends(request()->query())->links() }}
        </div>
    </div>
</body>
</html>