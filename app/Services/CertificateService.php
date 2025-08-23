<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;

class CertificateService
{
    public function generateCertificate(Course $course, User $user)
    {
        $totalModules = $course->modules()->count();
        if ($totalModules == 0) {
            throw new Exception('Kursus ini tidak memiliki modul.');
        }

        $completedModules = $user->completedModules()->where('course_id', $course->id)->count();
        
        if ($completedModules < $totalModules) {
            throw new Exception('Anda belum menyelesaikan semua modul di kursus ini.');
        }

        $quizzes = Quiz::whereIn('module_id', $course->modules->pluck('id'))->get();
        $highestScores = $user->quizAttempts()
            ->whereIn('quiz_id', $quizzes->pluck('id'))
            ->select('quiz_id', \DB::raw('MAX(score) as max_score'))
            ->groupBy('quiz_id')
            ->pluck('max_score');
        $averageScore = $highestScores->count() > 0 ? $highestScores->avg() : 0;

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