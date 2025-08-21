<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Grocademy</title>
    @vite('resources/css/app.css')
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h1>Buat Akun Baru</h1>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="form-group">
                    <label for="first_name">Nama Depan</label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Nama Belakang</label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </form>
            <p style="text-align: center; margin-top: 1rem;">Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a></p>
        </div>
    </div>
</body>
</html>