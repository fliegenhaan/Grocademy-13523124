<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Module;
use App\Models\User;
use App\Services\ModuleService;
use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
{
    public function __construct(private ModuleService $moduleService)
    {
    }

    public function index(Course $course)
    {
        $user = Auth::user();

        if (!$user->courses()->where('course_id', $course->id)->exists()) {
            return redirect()->route('course.show', $course)->with('error', 'Anda harus membeli kursus ini untuk mengakses modul.');
        }

        $data = $this->moduleService->getModuleIndexData($course, $user);

        return view('modules.index', [
            'course' => $course,
            'modules' => $data['modules'],
            'completedModulesIds' => $data['completedModulesIds'],
            'progressPercentage' => $data['progressPercentage']
        ]);
    }

    public function complete(Module $module)
    {
        $this->moduleService->completeModule($module, Auth::user());

        return redirect()->route('modules.index', $module->course_id)->with('success', 'Modul berhasil ditandai selesai!');
    }

    public function uncomplete(Module $module)
    {
        $this->moduleService->uncompleteModule($module, Auth::user());
        
        return redirect()->route('modules.index', $module->course_id)->with('success', 'Status modul berhasil dikembalikan!');
    }

    public function show(Module $module)
    {
        $course = $module->course;
        if (!Auth::user()->courses()->where('course_id', $course->id)->exists()) {
            abort(403, 'Akses Ditolak');
        }

        return view('modules.show', ['module' => $module]);
    }
}
