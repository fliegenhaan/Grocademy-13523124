<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $course->title }} - Grocademy</title>
</head>
<body>
    <div class="container">
        <h1>{{ $course->title }}</h1>
        <p>by {{ $course->instructor }}</p>
        <hr>

        <img src="{{ $course->thumbnail_image }}" alt="{{ $course->title }}" width="400">
        
        <h3>Deskripsi</h3>
        <p>{{ $course->description }}</p>

        <h3>Topik yang Akan Dipelajari</h3>
        <ul>
            @foreach ($course->topics as $topic)
                <li>{{ $topic }}</li>
            @endforeach
        </ul>

        @if (session('success'))
            <div style="color: green;">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div style="color: red;">{{ session('error') }}</div>
        @endif

        <h2>Harga: Rp{{ number_format($course->price, 0, ',', '.') }}</h2>

        @auth
            @if ($isPurchased)
                <a href="{{ route('modules.index', $course) }}" style="background-color: green; color: white; padding: 10px; text-decoration: none;">Mulai Belajar</a>
            @else
                <form action="{{ route('courses.buy', $course) }}" method="POST">
                    @csrf
                    <button type="submit" style="background-color: blue; color: white;">Beli Kursus Ini</button>
                </form>
            @endif
        @else
            <p><a href="{{ route('login') }}">Login untuk membeli kursus ini</a></p>
        @endauth
    </div>
</body>
</html>