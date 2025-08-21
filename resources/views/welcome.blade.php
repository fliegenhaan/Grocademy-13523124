<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di Grocademy</title>
    @vite('resources/css/app.css')
</head>
<body>
    <div class="auth-container">
        <div class="welcome-container">
            <h1>Selamat Datang di Grocademy</h1>
            <p>Platform pembelajaran digital yang dirancang untuk membuat siapa saja semangat menambah ilmu dengan cara yang menyenangkan.</p>
            <div class="welcome-actions">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary">Masuk ke Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">Log In</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-secondary">Register</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </div>
</body>
</html>