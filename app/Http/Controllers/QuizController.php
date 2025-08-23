<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Services\QuizService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function __construct(private QuizService $quizService)
    {
    }

    public function show(Module $module)
    {
        $quiz = $module->quiz()->with('questions.answers')->firstOrFail();

        return view('quiz.show', compact('quiz', 'module'));
    }

    public function submit(Request $request, Module $module)
    {
        $answers = $request->input('answers', []);
        
        $result = $this->quizService->processQuizSubmission($module, Auth::user(), $answers);

        return view('quiz.result', [
            'score' => $result['score'],
            'isPassed' => $result['isPassed'],
            'passing_score' => $result['passing_score'],
            'module' => $module
        ]);
    }
}