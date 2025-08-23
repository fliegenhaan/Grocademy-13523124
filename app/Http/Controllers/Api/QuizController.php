<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;

class QuizController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/modules/{module}/quizzes",
     * tags={"Quizzes"},
     * summary="Create quiz for a module",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="module", in="path", required=true, @OA\Schema(type="integer"), description="Module ID"),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"title","passing_score"},
     * @OA\Property(property="title", type="string", example="Quiz for Module 1"),
     * @OA\Property(property="description", type="string", example="A short quiz to test your knowledge."),
     * @OA\Property(property="passing_score", type="integer", example=75)
     * )
     * ),
     * @OA\Response(response=201, description="Quiz created successfully", @OA\JsonContent(ref="#/components/schemas/Quiz")),
     * @OA\Response(response=404, description="Module not found"),
     * @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request, Module $module)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'passing_score' => 'required|integer|min:0|max:100',
        ]);

        $quiz = $module->quiz()->create($validated);

        return response()->json($quiz, 201);
    }
    
    /**
     * @OA\Post(
     * path="/api/quizzes/{quiz}/questions",
     * tags={"Quizzes"},
     * summary="Add a question to a quiz",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="quiz", in="path", required=true, @OA\Schema(type="integer"), description="Quiz ID"),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"question_text","answers"},
     * @OA\Property(property="question_text", type="string", example="What is Laravel?"),
     * @OA\Property(property="answers", type="array", @OA\Items(
     * @OA\Property(property="answer_text", type="string", example="A PHP framework"),
     * @OA\Property(property="is_correct", type="boolean", example=true)
     * ))
     * )
     * ),
     * @OA\Response(response=201, description="Question added successfully", @OA\JsonContent(ref="#/components/schemas/Question")),
     * @OA\Response(response=404, description="Quiz not found"),
     * @OA\Response(response=422, description="Validation error")
     * )
     */
    public function addQuestion(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'answers' => 'required|array|min:2',
            'answers.*.answer_text' => 'required|string',
            'answers.*.is_correct' => 'required|boolean',
        ]);

        $question = $quiz->questions()->create(['question_text' => $validated['question_text']]);

        foreach ($validated['answers'] as $answerData) {
            $question->answers()->create($answerData);
        }

        return response()->json($question->load('answers'), 201);
    }
}