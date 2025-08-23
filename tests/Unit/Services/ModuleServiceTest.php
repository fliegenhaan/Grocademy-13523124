<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Module;
use App\Models\Quiz;
use App\Services\ModuleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ModuleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ModuleService $moduleService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->moduleService = new ModuleService();
    }

    #[Test]
    public function module_can_be_marked_as_complete(): void
    {
        // Arrange
        $user = User::factory()->create();
        $module = Module::factory()->create(['course_id' => Course::factory()->create()]);

        // Act
        $this->moduleService->completeModule($module, $user);

        // Assert
        $this->assertDatabaseHas('module_user', [
            'user_id' => $user->id,
            'module_id' => $module->id,
        ]);
    }

    #[Test]
    public function module_completion_status_can_be_reverted(): void
    {
        // Arrange
        $user = User::factory()->create();
        $module = Module::factory()->create(['course_id' => Course::factory()->create()]);
        $user->completedModules()->attach($module->id);

        // Act
        $this->moduleService->uncompleteModule($module, $user);

        // Assert
        $this->assertDatabaseMissing('module_user', [
            'user_id' => $user->id,
            'module_id' => $module->id,
        ]);
    }

    #[Test]
    public function uncompleting_module_with_quiz_deletes_attempts(): void
    {
        // Arrange
        $user = User::factory()->create();
        $module = Module::factory()->create(['course_id' => Course::factory()->create()]);
        $quiz = Quiz::factory()->create(['module_id' => $module->id]);
        $user->quizAttempts()->create(['quiz_id' => $quiz->id, 'score' => 100]);
        $user->completedModules()->attach($module->id);

        // Act
        $this->moduleService->uncompleteModule($module, $user);

        // Assert
        $this->assertDatabaseMissing('module_user', ['user_id' => $user->id, 'module_id' => $module->id]);
        $this->assertDatabaseMissing('quiz_attempts', ['user_id' => $user->id, 'quiz_id' => $quiz->id]);
    }

    #[Test]
    public function module_is_locked_if_previous_quiz_is_not_passed(): void
    {
        // Arrange
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $module1 = Module::factory()->create(['course_id' => $course->id, 'order' => 1]);
        $module2 = Module::factory()->create(['course_id' => $course->id, 'order' => 2]);
        $quiz2 = Quiz::factory()->create(['module_id' => $module2->id, 'passing_score' => 80]);
        $module3 = Module::factory()->create(['course_id' => $course->id, 'order' => 3]);
        
        $user->completedModules()->attach($module1->id);
        $user->quizAttempts()->create(['quiz_id' => $quiz2->id, 'score' => 50]);

        // Act
        $data = $this->moduleService->getModuleIndexData($course, $user);
        $modules = $data['modules'];
        
        // Assert
        $this->assertFalse($modules->find($module1->id)->is_locked);
        $this->assertFalse($modules->find($module2->id)->is_locked);
        $this->assertTrue($modules->find($module3->id)->is_locked);
    }
}