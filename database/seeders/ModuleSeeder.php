<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Module;
use App\Models\User;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Module::query()->delete();
        \DB::table('module_user')->truncate();

        $courses = Course::all();
        $users = User::where('is_admin', false)->get();

        foreach ($courses as $course) {
            // Buat 5-8 modul untuk setiap course
            for ($i = 1; $i <= rand(5, 8); $i++) {
                $course->modules()->create([
                    'title' => "Modul {$i}: " . $this->getModuleTitle($course->title, $i),
                    'description' => 'Ini adalah deskripsi untuk modul ke-' . $i . ' dari course ' . $course->title,
                    'order' => $i,
                    'pdf_content' => null, // bisa diisi URL ke file PDF dummy
                    'video_content' => null, // bisa diisi URL ke video dummy
                ]);
            }
        }
        
        // --- Seeding Pivot Table (module_user) ---
        // Loop melalui setiap user untuk menandai beberapa modul sebagai selesai
        foreach ($users as $user) {
            // Ambil course yang sudah dibeli oleh user ini
            $purchasedCourses = $user->courses;
            
            foreach ($purchasedCourses as $purchasedCourse) {
                $modules = $purchasedCourse->modules;
                // Selesaikan sekitar 30% - 70% modul secara acak
                $modulesToCompleteCount = floor($modules->count() * (rand(30, 70) / 100));
                $modulesToComplete = $modules->random($modulesToCompleteCount);
                
                foreach ($modulesToComplete as $module) {
                    $user->completedModules()->attach($module->id, ['completed_at' => now()]);
                }
            }
        }
    }

    /**
     * Helper function untuk judul modul yang lebih dinamis.
     */
    private function getModuleTitle(string $courseTitle, int $order): string
    {
        $titles = [
            'Dasar-Dasar Pemrograman Python' => ['Pengenalan Python', 'Setup Lingkungan', 'Sintaks Dasar', 'Struktur Kontrol', 'Fungsi dan Modul', 'Dasar OOP', 'File I/O', 'Studi Kasus'],
            'Web Development dengan Laravel 11' => ['Instalasi & Konfigurasi', 'Routing & Controller', 'Template Engine Blade', 'Eloquent & Migrations', 'Form & Validasi', 'Authentication', 'REST API', 'Deployment'],
            'Manajemen Database dengan MySQL' => ['Pengenalan RDBMS', 'Tipe Data & Tabel', 'Query SELECT', 'JOINs & Subqueries', 'Agregasi Data', 'Indexing', 'Stored Procedures', 'Backup & Restore'],
            'Investasi Saham untuk Nimons' => ['Apa itu Saham?', 'Membuka Akun Sekuritas', 'Analisis Fundamental Pisang', 'Chart Candlestick', 'Jangan Taruh Semua Banana di Satu Keranjang', 'Kapan Beli, Kapan Jual', 'Pajak Saham', 'Simulasi Trading'],
        ];

        return $titles[$courseTitle][$order - 1] ?? "Topik Lanjutan {$order}";
    }
}