<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use App\Services\QuizService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class QuizServiceTest extends TestCase
{
    use RefreshDatabase;

    protected QuizService $quizService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->quizService = new QuizService();
    }

    #[Test]
    public function quiz_submission_with_perfect_score_passes_and_completes_module(): void
    {
        // Arrange
        $user = User::factory()->create();
        $module = Module::factory()->create(['course_id' => Course::factory()->create()]);
        $quiz = Quiz::factory()->create(['module_id' => $module->id, 'passing_score' => 80]);
        $question1 = Question::factory()->create(['quiz_id' => $quiz->id]);
        $correctAnswer1 = Answer::factory()->create(['question_id' => $question1->id, 'is_correct' => true]);
        Answer::factory()->create(['question_id' => $question1->id, 'is_correct' => false]);

        $submittedAnswers = [$question1->id => $correctAnswer1->id];

        // Act
        $result = $this->quizService->processQuizSubmission($module, $user, $submittedAnswers);

        // Assert
        $this->assertEquals(100, $result['score']);
        $this->assertTrue($result['isPassed']);
        $this->assertDatabaseHas('quiz_attempts', ['user_id' => $user->id, 'quiz_id' => $quiz->id, 'score' => 100]);
        $this->assertDatabaseHas('module_user', ['user_id' => $user->id, 'module_id' => $module->id]);
    }

    #[Test]
    public function quiz_submission_with_zero_score_fails_and_does_not_complete_module(): void
    {
        // Arrange
        $user = User::factory()->create();
        $module = Module::factory()->create(['course_id' => Course::factory()->create()]);
        $quiz = Quiz::factory()->create(['module_id' => $module->id, 'passing_score' => 80]);
        $question1 = Question::factory()->create(['quiz_id' => $quiz->id]);
        Answer::factory()->create(['question_id' => $question1->id, 'is_correct' => true]);
        $incorrectAnswer1 = Answer::factory()->create(['question_id' => $question1->id, 'is_correct' => false]);

        $submittedAnswers = [$question1->id => $incorrectAnswer1->id];

        // Act
        $result = $this->quizService->processQuizSubmission($module, $user, $submittedAnswers);

        // Assert
        $this->assertEquals(0, $result['score']);
        $this->assertFalse($result['isPassed']);
        $this->assertDatabaseHas('quiz_attempts', ['user_id' => $user->id, 'quiz_id' => $quiz->id, 'score' => 0]);
        $this->assertDatabaseMissing('module_user', ['user_id' => $user->id, 'module_id' => $module->id]);
    }

    #[Test]
    public function partial_score_can_pass_if_above_passing_score(): void
    {
        // Arrange
        $user = User::factory()->create();
        $module = Module::factory()->create(['course_id' => Course::factory()->create()]);
        $quiz = Quiz::factory()->create(['module_id' => $module->id, 'passing_score' => 50]);
        
        $question1 = Question::factory()->create(['quiz_id' => $quiz->id]);
        $correctAnswer1 = Answer::factory()->create(['question_id' => $question1->id, 'is_correct' => true]);
        
        $question2 = Question::factory()->create(['quiz_id' => $quiz->id]);
        Answer::factory()->create(['question_id' => $question2->id, 'is_correct' => true]);
        $incorrectAnswer2 = Answer::factory()->create(['question_id' => $question2->id, 'is_correct' => false]);

        $submittedAnswers = [
            $question1->id => $correctAnswer1->id,
            $question2->id => $incorrectAnswer2->id,
        ];

        // Act
        $result = $this->quizService->processQuizSubmission($module, $user, $submittedAnswers);

        // Assert
        $this->assertEquals(50, $result['score']);
        $this->assertTrue($result['isPassed']);
        $this->assertDatabaseHas('module_user', ['user_id' => $user->id, 'module_id' => $module->id]);
    }
}