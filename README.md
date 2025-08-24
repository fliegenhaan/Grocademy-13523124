### Identitas Diri

-   **Nama:** Muhammad Raihaan Perdana
-   **NIM:** 13523124

---

### Daftar Isi

1.  [Technology Stack](#technology-stack)
2.  [Cara Menjalankan Aplikasi](#cara-menjalankan-aplikasi)
3.  [Design Pattern yang Digunakan](#design-pattern-yang-digunakan)
4.  [API Endpoints](#endpoint-apa-saja-yang-dibuat)
5.  [Bonus yang Dikerjakan](#bonus-yang-dikerjakan)
6.  [Screenshot Aplikasi](#screenshot-aplikasi)

---

### Technology Stack

Berikut adalah teknologi yang digunakan dalam pengembangan proyek ini:

-   **Bahasa Pemrograman:** PHP 8.1
-   **Framework Backend:** Laravel 11
-   **Database:** MySQL / MariaDB
-   **Frontend (Monolith):** Laravel Blade
-   **Lingkungan Pengembangan:** Windows Subsystem for Linux (WSL) & Docker Desktop
-   **Manajemen Lingkungan:** Laravel Sail
-   **Web Server (Development):** PHP Built-in Server (`php artisan serve`) / Apache (via Docker)

---

### Cara Menjalankan Aplikasi

Aplikasi ini telah di-containerize menggunakan Docker (via Laravel Sail) untuk kemudahan setup. Pastikan **WSL** dan **Docker Desktop** sudah terinstal dan berjalan di laptop/komputer Anda.

**1. Clone Repository**
```bash
git clone https://github.com/fliegenhaan/Grocademy-13523124.git
cd Grocademy-13523124
```

**2. Setup Lingkungan Backend (Laravel)**
- Salin File Environment
```bash
cp .env.example .env
```
- Jalankan Sail (Docker)
```bash
./vendor/bin/sail up -d
```
- Install Dependensi Composer
```bash
./vendor/bin/sail composer install
```
- Generate Application Key
```bash
./vendor/bin/sail artisan key:generate
```
- Jalankan Migrasi dan Seeding Database
```bash
./vendor/bin/sail artisan migrate --seed
```
- Buat Symbolic Link untuk Storage (agar file yang diupload seperti thumbnail dapat diakses publik)
```bash
./vendor/bin/sail artisan storage:link
```
- Jalankan di local
```bash
./vendor/bin/sail npm run dev
```
**3. Akses Aplikasi**
Website Utama (Monolith):
Aplikasi sekarang berjalan dan dapat diakses di: http://localhost

Frontend Admin:
Frontend Admin yang disediakan perlu dihubungkan ke API yang berjalan di http://localhost.

Akun Demo:
Admin: username: admin@grocademy.com, password: password123
User (hasil seeder): username: helena34, password: password

---

### Design Pattern yang Digunakan

Sesuai spesifikasi, proyek ini mengimplementasikan beberapa design pattern untuk memastikan kode yang terstruktur, dapat dipelihara, dan skalabel.

**1. Transformer/Adapter Pattern (API Resources)**
> **Alasan:** Pattern ini digunakan untuk mengubah (transform) struktur data dari Eloquent Model menjadi format JSON yang spesifik untuk API. Dengan menggunakan API Resources (`CourseResource`, `ModuleResource`, `UserResource`), saya dapat mengontrol dengan tepat data apa saja yang diekspos ke frontend, memastikan konsistensi response, dan menghindari kebocoran struktur database. Ini mengadaptasi data internal untuk kebutuhan eksternal.

**2. Factory Pattern (Database Seeders)**
> **Alasan:** Pattern ini digunakan untuk membuat objek tanpa harus mengekspos logika pembuatannya. Laravel menyediakan fitur Model Factory yang saya manfaatkan dalam *database seeders*. Ini memungkinkan saya untuk mendefinisikan "blueprint" data dummy dan meng-generate data dalam jumlah besar untuk `User`, `Course`, `Module`, dan `Quiz` dengan mudah, sehingga mempercepat proses development dan testing.

**3. Chain of Responsibility Pattern (Middleware)**
> **Alasan:** Middleware pada Laravel adalah implementasi murni dari pattern ini. Saya menggunakannya untuk membuat serangkaian "lapisan" yang harus dilewati oleh sebuah HTTP request sebelum mencapai Controller. Setiap middleware (seperti `auth:api` dan `is.admin`) bertanggung jawab atas satu tugas spesifik (memverifikasi token, mengecek role admin) dan dapat memutuskan apakah akan meneruskan request ke lapisan berikutnya atau menghentikannya. Ini membuat sistem otentikasi dan otorisasi menjadi sangat modular.

**4. Architectural Pattern (Model-View-Controller)**
> **Alasan:** Sebagai fondasi utama dari framework Laravel, MVC saya gunakan untuk memisahkan logika aplikasi menjadi tiga komponen utama. **Model** (`User`, `Course`) mengelola data dan interaksi dengan database. **View** (file-file Blade) bertanggung jawab atas presentasi dan UI. **Controller** (`CourseController`, `UserController`, `ModuleController`, `QuizController`, dan `CertificateController`) bertindak sebagai perantara yang menerima input dari user dan mengelola interaksi antara Model dan View. Pemisahan ini membuat kode menjadi jauh lebih terorganisir dan mudah dikelola.

### Endpoint Apa Saja yang Dibuat

Aplikasi ini memiliki dua jenis routing: **Web Routes** untuk interaksi pengguna umum dengan monolith, dan **API Routes** yang diekspos untuk dikonsumsi oleh Frontend Admin.

---

#### 1. Web Routes (`routes/web.php`)

Endpoint ini menangani semua halaman dan aksi yang bisa dilakukan oleh pengguna melalui website utama Grocademy.

| Method | URI | Nama Route | Middleware | Deskripsi |
| :--- | :--- | :--- | :--- | :--- |
| `GET` | `/` | - | - | Menampilkan halaman selamat datang. |
| `GET` | `/register` | `register` | `guest` | Menampilkan halaman registrasi. |
| `POST`| `/register` | - | `guest` | Memproses pendaftaran pengguna baru. |
| `GET` | `/login` | `login` | `guest` | Menampilkan halaman login. |
| `POST`| `/login` | - | `guest` | Memproses login pengguna. |
| `POST`| `/logout` | `logout` | `auth` | Memproses logout pengguna. |
| `GET` | `/dashboard` | `dashboard`| `auth` | Menampilkan dashboard setelah login. |
| `GET` | `/courses` | `courses.index`| - | Menampilkan daftar semua course yang tersedia. |
| `GET` | `/courses/{course}` | `courses.show` | - | Menampilkan halaman detail sebuah course. |
| `POST`| `/courses/{course}/buy` | `courses.buy` | `auth` | Memproses pembelian course oleh pengguna. |
| `GET` | `/my-courses` | `courses.my` | `auth` | Menampilkan daftar course yang sudah dibeli pengguna. |
| `GET` | `/courses/{course}/modules`| `modules.index`| `auth` | Menampilkan daftar modul dari sebuah course yang dibeli. |
| `GET` | `/modules/{module}` | `modules.show` | `auth` | Menampilkan detail konten dari sebuah modul. |
| `POST`| `/modules/{module}/complete` | `modules.complete`| `auth` | Menandai sebuah modul sebagai selesai. |
| `POST`| `/modules/{module}/uncomplete`| `modules.uncomplete`| `auth` | Membatalkan status selesai pada sebuah modul. |
| `GET` | `/courses/{course}/certificate`|`certificate.download`| `auth` | Mengunduh sertifikat jika course telah selesai. |
| `GET` | `/modules/{module}/Quiz` | `quiz.show` | `auth` | Menampilkan detail konten dari sebuah quiz. |
| `POST`| `/modules/{module}/Quiz` | `quiz.submit`| `auth` | Mensubmit quiz yang telah dikerjakan. |

---

#### 2. API Routes (`routes/api.php`)

Endpoint ini diekspos untuk Frontend Admin dan dilindungi oleh otentikasi Bearer Token (JWT). Semua endpoint yang memerlukan role admin dilindungi oleh middleware `is.admin`.

| Method | URI | Nama Route | Middleware | Deskripsi |
| :--- | :--- | :--- | :--- | :--- |
| `GET` | `/api` | - | - | Endpoint selamat datang API. |
| `POST` | `/api/auth/login` | - | `guest` | Login untuk akun admin. |
| `GET` | `/api/auth/self` | - | `auth:api` | Mendapatkan data admin yang sedang login. |
| `POST` | `/api/auth/register` | - | `auth:api`, `is.admin` | Mendaftarkan user baru (hanya admin). |
| `GET` | `/api/users` | `users.index` | `auth:api`, `is.admin` | **[READ]** Mendapatkan daftar semua user. |
| `GET` | `/api/users/{user}` | `users.show` | `auth:api`, `is.admin` | **[READ]** Mendapatkan detail satu user. |
| `PUT` | `/api/users/{user}` | `users.update` | `auth:api`, `is.admin` | **[UPDATE]** Memperbarui data user. |
| `DELETE`| `/api/users/{user}` | `users.destroy`| `auth:api`, `is.admin` | **[DELETE]** Menghapus seorang user. |
| `POST` | `/api/users/{user}/balance` | `users.balance`| `auth:api`, `is.admin` | Menambahkan saldo ke akun user. |
| `GET` | `/api/courses` | `courses.index` | `auth:api`, `is.admin` | **[READ]** Mendapatkan daftar semua course. |
| `POST` | `/api/courses` | `courses.store` | `auth:api`, `is.admin` | **[CREATE]** Membuat course baru. |
| `GET` | `/api/courses/{course}` | `courses.show` | `auth:api`, `is.admin` | **[READ]** Mendapatkan detail satu course. |
| `PUT` | `/api/courses/{course}` | `courses.update` | `auth:api`, `is.admin` | **[UPDATE]** Memperbarui data course. |
| `DELETE`| `/api/courses/{course}` | `courses.destroy`| `auth:api`, `is.admin` | **[DELETE]** Menghapus sebuah course. |
| `GET` | `/api/courses/{course}/modules` | `courses.modules.index`| `auth:api`, `is.admin` | **[READ]** Mendapatkan module dari sebuah course. |
| `POST` | `/api/courses/{course}/modules` | `courses.modules.store`| `auth:api`, `is.admin` | **[CREATE]** Membuat module baru untuk course. |
| `GET` | `/api/modules/{module}` | `modules.show` | `auth:api`, `is.admin` | **[READ]** Mendapatkan detail satu module. |
| `PUT` | `/api/modules/{module}` | `modules.update` | `auth:api`, `is.admin` | **[UPDATE]** Memperbarui data module. |
| `DELETE`| `/api/modules/{module}` | `modules.destroy`| `auth:api`, `is.admin` | **[DELETE]** Menghapus sebuah module. |
| `PATCH` | `/api/courses/{course}/modules/reorder`|`courses.modules.reorder`| `auth:api`, `is.admin` | Mengubah urutan module. |
| `POST`| `/modules/{module}/quizzes` | `quiz.store`| `auth:api`, `is.admin` | **[CREATE]** Menambah sebuah Quiz dalam sebuah module. |
| `POST` | `/quizzes/{quiz}/questions`|`quiz.addQuestion`| `auth:api`, `is.admin` | **[CREATE]** Menambah pertanyaan dalam sebuah Quiz. |

---

### Bonus yang Dikerjakan
#### B01 - OWASP
Website ini telah dilengkapi dengan pertahanan terhadap 3 celah keamanan utama dari OWASP Top 10 untuk menjamin keamanan data dan pengguna.

**A01 - Broken Access Control**: Sistem memastikan setiap pengguna hanya bisa mengakses konten (seperti modul kursus) yang menjadi haknya. Setiap permintaan untuk melihat modul akan divalidasi di sisi server untuk memeriksa kepemilikan kursus, sehingga mencegah pengguna mengakses URL secara paksa.

**A03 - Injection (SQL Injection)**: Website ini kebal terhadap serangan Injeksi SQL karena dibangun menggunakan Laravel Eloquent ORM. Eloquent secara otomatis menggunakan prepared statements yang memisahkan data input dari perintah database, sehingga input berbahaya dari pengguna tidak akan pernah dieksekusi.

**A07 - Cross-Site Request Forgery (CSRF)**: Setiap form yang mengirimkan data sensitif (seperti pembelian atau logout) dilindungi oleh token CSRF yang unik untuk setiap sesi. Mekanisme ini memastikan bahwa semua permintaan berasal dari dalam aplikasi itu sendiri, bukan dari situs eksternal yang mencoba membajak sesi pengguna.

Untuk video penjelasan lengkapnya ada di sini: https://youtu.be/9b2EpI8DmvM?si=h5z8NFsClkDFI-Yp 

#### B04 - Caching
Website ini dilengkapi dengan implementasi caching menggunakan Redis untuk mempercepat waktu muat halaman dan mengurangi beban database. Fokus utama caching berada pada halaman yang paling sering dimuat user yaitu daftar modul, di mana data progress pengguna yang kompleks (modul selesai, skor kuis, status modul) disimpan sementara. Saat data progres berubah (seperti setelah mengerjakan quiz atau menandai selesai sebuah modul), cache secara otomatis dihapus (cache invalidation) untuk memastikan data yang ditampilkan selalu data yang terbaru.

#### B05 - Lighthouse
Website ini memiliki kualitas halaman yang sangat baik dibuktikan dengan audit dari tool Lighthouse (dari google).
Skor rata-rata lighthouse yang didapat dari semua page di website:
... + ... + ... = ...
#### B06 - Responsive Layout
Website ini memiliki layout yang responsive baik di layar lebar (desktop), medium (tablet), maupun kecil (mobile).

#### B07 - Dokumentasi API
Website ini dilengkapi dengan dokumentasi untuk setiap API yang telah dibuat menggunakan Swagger. Dokumentasi ini bersifat interaktif dan menjelaskan setiap endpoint, metode HTTP yang digunakan (GET, POST, dll.), parameter yang diperlukan, format respons, serta contoh request dan response yang berhasil maupun gagal. Tersedia di [localhost/api/documentation](http://localhost/api/documentation)
#### B08 - SOLID
blablablabla
#### B09 - Automated Testing
Saya juga membuat unit test untuk memvalidasi logika di dalam setiap kelas Service (Backend) menggunakan PHPUnit. Tes ini mencakup berbagai skenario: "happy path" (ketika semua berjalan lancar), kasus kegagalan (seperti saldo tidak cukup atau kuis gagal), dan kasus-kasus khusus lainnya. Dengan menjalankan tes ini, saya dapat memastikan setiap perubahan baru tidak merusak fungsionalitas yang sudah ada. Semua file unit test tersedia di dalam folder tests.
#### B10 - Fitur Tambahan (Quiz)
Website ini juga dilengkapi dengan sistem kuis yang terintegrasi dengan modul. Fitur ini memungkinkan pengguna untuk menguji pemahaman mereka setelah mempelajari sebuah modul. Sistem secara otomatis menghitung skor, menentukan kelulusan berdasarkan passing_score, dan menggunakan hasil kuis tersebut untuk mengontrol alur pembelajaran, di mana pengguna harus lulus kuis di modul saat ini sebelum bisa mengakses modul berikutnya.

---

### Screenshot Aplikasi


---