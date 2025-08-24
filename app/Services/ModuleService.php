<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Module;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class ModuleService
{
    public function getModuleIndexData(Course $course, User $user): array
    {
        $cacheKey = "modules:user.{$user->id}.course.{$course->id}";

        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($course, $user) {
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

                $currentModuleIsPassed = false;
                if ($module->quiz) {
                    $score = $highestScores[$module->quiz->id] ?? 0;
                    $currentModuleIsPassed = $score >= $module->quiz->passing_score;
                } else {
                    $currentModuleIsPassed = in_array($module->id, $completedModulesIds);
                }

                $isPreviousModulePassed = $isPreviousModulePassed && $currentModuleIsPassed;
            }

            return [
                'modules' => $modules,
                'completedModulesIds' => $completedModulesIds,
                'progressPercentage' => $progressPercentage,
            ];
        });
    }

    public function completeModule(Module $module, User $user): void
    {
        if (!$user->completedModules()->where('module_id', $module->id)->exists()) {
            $user->completedModules()->attach($module->id);

            $cacheKey = "modules:user.{$user->id}.course.{$module->course_id}";
            Cache::forget($cacheKey);
        }
    }

    public function uncompleteModule(Module $module, User $user): void
    {
        if ($user->completedModules()->where('module_id', $module->id)->exists()) {
            $user->completedModules()->detach($module->id);
        }
        
        if ($module->quiz) {
            $user->quizAttempts()->where('quiz_id', $module->quiz->id)->delete();
        }
        
        $cacheKey = "modules:user.{$user->id}.course.{$module->course_id}";
        Cache::forget($cacheKey);
    }
}