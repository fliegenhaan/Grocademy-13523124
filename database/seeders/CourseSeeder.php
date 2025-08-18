<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\User;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Course::query()->delete();

        $coursesData = [
            [
                'title' => 'Dasar-Dasar Pemrograman Python',
                'description' => 'Pelajari fundamental Python dari nol hingga mahir. Cocok untuk pemula yang ingin terjun ke dunia data science dan web development.',
                'instructor' => 'Dr. Elara Vance',
                'topics' => json_encode(['Variables', 'Data Types', 'Loops', 'Functions', 'OOP']),
                'price' => 150000,
                'thumbnail_image' => 'https://picsum.photos/seed/python/600/400',
            ],
            [
                'title' => 'Web Development dengan Laravel 11',
                'description' => 'Kuasai framework PHP paling populer, Laravel. Bangun aplikasi web monolit yang tangguh dan modern.',
                'instructor' => 'Prof. Kaelen',
                'topics' => json_encode(['Routing', 'Blade', 'Eloquent ORM', 'MVC', 'API']),
                'price' => 250000,
                'thumbnail_image' => 'https://picsum.photos/seed/laravel/600/400',
            ],
            [
                'title' => 'Manajemen Database dengan MySQL',
                'description' => 'Pahami konsep relational database dan kuasai query SQL untuk manajemen data yang efisien.',
                'instructor' => 'Gro',
                'topics' => json_encode(['SQL Basics', 'Joins', 'Indexing', 'Transactions']),
                'price' => 180000,
                'thumbnail_image' => 'https://picsum.photos/seed/mysql/600/400',
            ],
            [
                'title' => 'Investasi Saham untuk Nimons',
                'description' => 'Kursus khusus dari Gro untuk para Nimons yang ingin memahami dunia investasi saham. Dijamin bukan dianggap kue!',
                'instructor' => 'Gro',
                'topics' => json_encode(['Analisis Fundamental', 'Analisis Teknikal', 'Manajemen Risiko']),
                'price' => 500000,
                'thumbnail_image' => 'https://picsum.photos/seed/investasi/600/400',
            ],
        ];

        foreach ($coursesData as $data) {
            Course::create($data);
        }

        // --- Seeding Pivot Table (course_user) ---
        $users = User::where('is_admin', false)->get();
        $courses = Course::all();

        foreach ($users as $user) {
            // Setiap user membeli 1 atau 2 course secara acak
            $purchasedCourses = $courses->random(rand(1, 2));
            foreach ($purchasedCourses as $course) {
                // Attach relasi di pivot table
                $user->courses()->attach($course->id);
            }
        }
    }
}