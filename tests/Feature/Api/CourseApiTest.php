<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;

class CourseApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_create_a_course(): void
    {
        // Arrange
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true]);
        $token = auth('api')->login($admin);

        $courseData = [
            'title' => 'Kursus Baru dari Test',
            'description' => 'Deskripsi kursus baru.',
            'instructor' => 'Tester',
            'price' => 150000,
            'topics' => ['PHP', 'Laravel', 'Testing'],
            'thumbnail_image' => UploadedFile::fake()->image('thumbnail.jpg')
        ];

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/courses', $courseData);


        // Assert
        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['id', 'title', 'description', 'instructor', 'price', 'topics']
            ])
            ->assertJsonFragment(['title' => 'Kursus Baru dari Test']);

        $this->assertDatabaseHas('courses', ['title' => 'Kursus Baru dari Test']);
    }
}