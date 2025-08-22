<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function show(Module $module)
    {
        $quiz = $module->quiz()->with('questions.answers')->firstOrFail();

        return view('quiz.show', compact('quiz', 'module'));
    }

    public function submit(Request $request, Module $module)
    {
        $quiz = $module->quiz()->with('questions.answers')->firstOrFail();
        $answers = $request->input('answers');
        
        $score = 0;
        $totalQuestions = $quiz->questions->count();

        foreach ($quiz->questions as $question) {
            if (isset($answers[$question->id])) {
                $correctAnswer = $question->answers->where('is_correct', true)->first();
            
                if ($correctAnswer && $answers[$question->id] == $correctAnswer->id) {
                    $score++;
                }
            }
        }

        $finalScore = ($totalQuestions > 0) ? ($score / $totalQuestions) * 100 : 0;

        QuizAttempt::create([
            'user_id' => Auth::id(),
            'quiz_id' => $quiz->id,
            'score'   => $finalScore,
        ]);

        $isPassed = $finalScore >= $quiz->passing_score;

        if ($isPassed) {
            Auth::user()->completedModules()->syncWithoutDetaching([$module->id]);
        }

        return view('quiz.result', [
            'score' => $finalScore,
            'isPassed' => $isPassed,
            'passing_score' => $quiz->passing_score,
            'module' => $module
        ]);
    }
}