<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Exception;

class CourseServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CourseService $courseService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->courseService = new CourseService();
    }

    #[Test]
    public function user_can_purchase_course_successfully(): void
    {
        // Arrange
        $user = User::create([
            'first_name' => 'timothy',
            'last_name' => 'ronald',
            'username' => 'timothyr',
            'email' => 'ronaldtimot@test.com',
            'password' => bcrypt('password'),
            'balance' => 100000,
            'is_admin' => false,
        ]);

        $course = Course::create([
            'title' => 'Mahir Crypto',
            'description' => 'Waktu kalian sisa 5 tahun',
            'instructor' => 'Timothy Ronald',
            'topics' => json_encode(['crypto', 'bitcoin']),
            'price' => 50000,
            'thumbnail_image' => 'crypto.jpg',
        ]);

        // Act
        $this->courseService->buyCourse($user, $course);

        // Assert
        $this->assertDatabaseHas('course_user', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        $this->assertEquals(50000, $user->fresh()->balance);
    }

    #[Test]
    public function purchase_fails_due_to_insufficient_balance(): void
    {
        // Arrange
        $user = User::create([
            'first_name' => 'bokek',
            'last_name' => 'user',
            'username' => 'bokekuser',
            'email' => 'bokekuser@test.com',
            'password' => bcrypt('password'),
            'balance' => 30000,
            'is_admin' => false,
        ]);

        $course = Course::create([
            'title' => 'Kursus Mahal',
            'description' => 'Ilmu mahal buat naklukin cewe jaksel',
            'instructor' => 'Si Paling Nyampe',
            'topics' => json_encode(['jaksel', 'kalcer']),
            'price' => 50000,
            'thumbnail_image' => 'kursusmahal.jpg',
        ]);

        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Saldo Anda tidak mencukupi untuk membeli kursus ini.');

        // Act
        $this->courseService->buyCourse($user, $course);
    }

    #[Test]
    public function purchase_fails_if_course_is_already_owned(): void
    {
        // Arrange
        $user = User::create([
            'first_name' => 'anak',
            'last_name' => 'rajin',
            'username' => 'anakrajin',
            'email' => 'anakrajin@test.com',
            'password' => bcrypt('password'),
            'balance' => 100000,
            'is_admin' => false,
        ]);

        $course = Course::create([
            'title' => 'Kursus Larapel 10 jam',
            'description' => 'Larapel dari 0 sampe mahir',
            'instructor' => 'Pak Monte',
            'topics' => json_encode(['laravel', 'php']),
            'price' => 50000,
            'thumbnail_image' => 'kursuslarapel.jpg',
        ]);

        $user->courses()->attach($course);

        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Anda sudah memiliki course ini.');

        // Act
        $this->courseService->buyCourse($user, $course);
    }
}