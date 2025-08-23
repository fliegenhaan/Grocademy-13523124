<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Module;
use App\Services\CertificateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class CertificateServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CertificateService $certificateService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->certificateService = new CertificateService();
    }

    #[Test]
    public function certificate_generation_fails_if_not_all_modules_are_completed(): void
    {
        // Arrange
        $user = User::factory()->create();
        $course = Course::factory()->create();
        Module::factory()->count(2)->create(['course_id' => $course->id]);
        $user->completedModules()->attach(Module::first()->id);

        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Anda belum menyelesaikan semua modul di kursus ini.');

        // Act
        $this->certificateService->generateCertificate($course, $user);
    }

    #[Test]
    public function certificate_can_be_generated_when_all_modules_are_completed(): void
    {
        // Arrange
        Pdf::fake();

        $user = User::factory()->create();
        $course = Course::factory()->create();
        $modules = Module::factory()->count(2)->create(['course_id' => $course->id]);
        $user->completedModules()->attach($modules->pluck('id'));
        
        // Act
        $response = $this->certificateService->generateCertificate($course, $user);

        // Assert
        Pdf::assertViewIs('certificate.show');
        Pdf::assertViewHas('user', $user);
        Pdf::assertViewHas('course', $course);
        $this->assertNotNull($response);
    }
}