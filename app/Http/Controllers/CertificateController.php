<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\CertificateService;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    public function __construct(private CertificateService $certificateService)
    {
    }

    public function download(Course $course)
    {
        try {
            return $this->certificateService->generateCertificate($course, Auth::user());
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}