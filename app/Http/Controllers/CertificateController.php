<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CertificateController extends Controller
{
    public function download(Course $course)
    {
        $user = Auth::user();
        
        $totalModules = $course->modules()->count();
        $completedModules = $user->completedModules()->where('course_id', $course->id)->count();

        if ($totalModules == 0 || $completedModules < $totalModules) {
            return back()->with('error', 'Anda belum menyelesaikan kursus ini.');
        }

        $data = [
            'user' => $user,
            'course' => $course,
            'completionDate' => Carbon::now()->format('d F Y')
        ];
        
        $pdf = Pdf::loadView('certificate.show', $data);
        return $pdf->download('sertifikat-'.$course->title.'.pdf');
    }
}