<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Module;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
{
    public function index(Course $course)
    {
        $user = Auth::user();

        if (!$user->courses()->where('course_id', $course->id)->exists()) {
            return redirect()->route('course.show', $course)->with('error', 'Anda harus membeli kursus ini untuk mengakses modul.');
        }

        $modules = $course->modules()->with('quiz')->orderBy('order', 'asc')->get();
        $completedModulesIds = $user->completedModules()->whereIn('module_id', $modules->pluck('id'))->pluck('module_id')->toArray();
        $totalModules = $modules->count();
        $completedCount = count($completedModulesIds);
        $progressPercentage = ($totalModules > 0) ? ($completedCount / $totalModules) * 100 : 0;
        $highestScores = $user->quizAttempts()
        ->select('quiz_id', \DB::raw('MAX(score) as max_score'))
        ->whereIn('quiz_id', $modules->pluck('quiz.id')->filter())
        ->groupBy('quiz_id')
        ->pluck('max_score', 'quiz_id');

        $isPreviousModulePassed = true;
        foreach ($modules as $module) {
            $module->is_locked = !$isPreviousModulePassed;

            if ($module->quiz) {
                $score = $highestScores[$module->quiz->id] ?? 0;
                $isPreviousModulePassed = $score >= $module->quiz->passing_score;
            } else {
                $isPreviousModulePassed = in_array($module->id, $completedModulesIds);
            }
        }

        return view('modules.index', [
            'course' => $course,
            'modules' => $modules,
            'completedModulesIds' => $completedModulesIds,
            'progressPercentage' => $progressPercentage
        ]);
    }

    public function complete(Module $module)
    {
        $user = Auth::user();

        if(!$user->completedModules()->where('module_id', $module->id)->exists()) {
            $user->completedModules()->attach($module->id);
        }

        return redirect()->route('modules.index', $module->course_id)->with('success', 'Modul berhasil ditandai selesai!');
    }

    public function uncomplete(Module $module)
    {
        $user = Auth::user();
    
        if($user->completedModules()->where('module_id', $module->id)->exists()) {
            $user->completedModules()->detach($module->id);
        }

        if ($module->quiz) {
            $user->quizAttempts()->where('quiz_id', $module->quiz->id)->delete();
        }

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
