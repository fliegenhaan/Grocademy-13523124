<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Quiz;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CertificateController extends Controller
{
    public function download(Course $course)
    {
        $user = Auth::user();
        $quizzes = Quiz::whereIn('module_id', $course->modules->pluck('id'))->get();
        $totalModules = $course->modules()->count();
        $completedModules = $user->completedModules()->where('course_id', $course->id)->count();
        $highestScores = $user->quizAttempts()
        ->whereIn('quiz_id', $quizzes->pluck('id'))
        ->select('quiz_id', \DB::raw('MAX(score) as max_score'))
        ->groupBy('quiz_id')
        ->pluck('max_score');
        $averageScore = $highestScores->count() > 0 ? $highestScores->avg() : null;

        if ($totalModules == 0 || $completedModules < $totalModules) {
            return back()->with('error', 'Anda belum menyelesaikan kursus ini.');
        }

        $data = [
            'user' => $user,
            'course' => $course,
            'averageScore' => round($averageScore),
            'completionDate' => Carbon::now()->format('d F Y')
        ];
        
        $pdf = Pdf::loadView('certificate.show', $data);
        return $pdf->download('sertifikat-'.$course->title.'.pdf');
    }
}