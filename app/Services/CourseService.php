<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class CourseService
{
    public function buyCourse(User $user, Course $course) : bool
    {
        if ($user->courses()->where('course_id', $course->id)->exists()) {
            throw new Exception('Anda sudah memiliki course ini.');
        }

        if ($user->balance < $course->price) {
            throw new Exception('Saldo Anda tidak mencukupi untuk membeli kursus ini.');
        }

        DB::transaction(function () use ($user, $course) {
            $user->balance -= $course->price;
            $user->save();

            $user->courses()->attach($course->id);
        });

        return true;
    }

}