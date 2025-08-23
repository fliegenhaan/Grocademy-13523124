<?php

namespace App\Services;

use App\Models\Module;
use App\Models\QuizAttempt;
use App\Models\User;

class QuizService
{
    public function processQuizSubmission(Module $module, User $user, array $submittedAnswers): array
    {
        $quiz = $module->quiz()->with('questions.answers')->firstOrFail();
        $score = 0;
        $totalQuestions = $quiz->questions->count();

        foreach ($quiz->questions as $question) {
            if (isset($submittedAnswers[$question->id])) {
                $correctAnswer = $question->answers->where('is_correct', true)->first();

                if ($correctAnswer && $submittedAnswers[$question->id] == $correctAnswer->id) {
                    $score++;
                }
            }
        }

        $finalScore = ($totalQuestions > 0) ? ($score / $totalQuestions) * 100 : 0;

        QuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'score'   => $finalScore,
        ]);

        $isPassed = $finalScore >= $quiz->passing_score;

        if ($isPassed) {
            $user->completedModules()->syncWithoutDetaching([$module->id]);
        }

        return [
            'score' => $finalScore,
            'isPassed' => $isPassed,
            'passing_score' => $quiz->passing_score,
        ];
    }
}