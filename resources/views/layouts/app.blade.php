<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Grocademy')</title>
    @vite('resources/css/app.css')
</head>
<body>

    <div id="app-layout">
        @auth
        <button class="menu-toggle" id="menu-toggle">☰</button>
        <div class="overlay" id="overlay"></div>
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2 style="cursor: pointer;" onclick="window.location.href='{{ route('courses.index') }}'">Grocademy</h2>
            </div>
            
            <nav class="sidebar-nav">
                <a href="{{ route('courses.index') }}">Browse Courses</a>
                <a href="{{ route('courses.my') }}">My Courses</a>
            </nav>

            <div class="sidebar-footer">
                <div class="user-info">
                    <p>{{ Auth::user()->username }}</p>
                    <p class="balance">Saldo: Rp{{ number_format(Auth::user()->balance, 0, ',', '.') }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-logout">Logout</button>
                </form>
            </div>
        </aside>
        @endauth

        <main class="main-content">
            @yield('content')
        </main>
    </div>

    @auth
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menuToggle = document.getElementById('menu-toggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const mainContent = document.querySelector('.main-content');

            function closeSidebar() {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                menuToggle.innerHTML = '☰';
            }

            menuToggle.addEventListener('click', function (e) {
                e.stopPropagation(); // Mencegah event "click" menyebar ke elemen lain
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
                // Ganti ikon toggle
                if (sidebar.classList.contains('active')) {
                    menuToggle.innerHTML = '✕'; // Ikon silang (close)
                } else {
                    menuToggle.innerHTML = '☰'; // Ikon hamburger
                }
            });

            // Tutup sidebar jika user mengklik overlay atau konten utama
            overlay.addEventListener('click', closeSidebar);
            mainContent.addEventListener('click', function() {
                if (window.innerWidth < 992 && sidebar.classList.contains('active')) {
                    closeSidebar();
                }
            });
        });
    </script>
    @endauth

</body>
</html>