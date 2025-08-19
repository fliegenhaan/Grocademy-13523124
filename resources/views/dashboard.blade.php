<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
    @auth
        <h1>Selamat Datang, {{ Auth::user()->username }}!</h1>
        <p>Saldo Anda saat ini: Rp{{ number_format(Auth::user()->balance, 0, ',', '.') }}</p>

        <nav>
            <a href="{{ route('courses.index') }}">Browse Semua Course</a> | 
            <a href="{{ route('courses.my') }}">My Courses</a>
        </nav>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    @endauth

    @guest
        <h1>Anda belum login.</h1>
        <a href="{{ route('login') }}">Login</a>
    @endguest
</body>
</html>