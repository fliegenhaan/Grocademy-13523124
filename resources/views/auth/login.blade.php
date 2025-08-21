<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Grocademy</title>
    @vite('resources/css/app.css')
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h1>Login ke Akun Anda</h1>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label for="identifier">Email atau Username</label>
                    <input type="text" name="identifier" id="identifier" value="{{ old('identifier') }}" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            <p style="text-align: center; margin-top: 1rem;">Belum punya akun? <a href="{{ route('register') }}">Register di sini</a></p>
        </div>
    </div>
</body>
</html>