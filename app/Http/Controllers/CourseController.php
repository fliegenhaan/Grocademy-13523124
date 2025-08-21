<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::query();

        if ($request->has('search') && $request->input('search') != '') {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('instructor', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $perPage = $request->input('per_page', 9);
        if (!in_array($perPage, [9, 18, 36])) {
            $perPage = 9;
        }

        $courses = $query->paginate($perPage);

        $purchasedCoursesIds = [];
        if (Auth::check()) {
            $purchasedCoursesIds = Auth::user()->courses()->pluck('course_id')->toArray();
        }

        return view('courses.index', [
            'courses' => $courses,
            'purchasedCoursesIds' => $purchasedCoursesIds
        ]);
    }

    public function show(Course $course) 
    {
        $isPurchased = false;
        if (Auth::check()) {
            $user = Auth::user();
            $isPurchased = $user->courses()->where('course_id', $course->id)->exists();
        }

        return view('courses.show', [
            'course' => $course,
            'isPurchased' => $isPurchased
        ]);
    }

    public function buy(Course $course)
    {
        $user = Auth::user();
        if ($user->courses()->where('course_id', $course->id)->exists()) {
            return back()->with('error', 'Anda sudah memiliki couse ini.');
        }

        if ($user->balance < $course->price) {
            return back()->with('error', 'Saldo Anda tidak mencukupi untuk membeli kursus ini.');
        }

        try {
            DB::transaction(function () use ($user, $course) {
                $user->balance -= $course->price;
                $user->save();

                $user->courses()->attach($course->id);
            });
        } catch (\Throwable $th) {
            dd($th);
        }

        return redirect()->route('courses.show', $course)->with('success', 'Selamat! Kursus berhasil dibeli.');
    }

    public function myCourses(Request $request)
    {
        $user = Auth::user();

        $query = $user->courses();

        if ($request->has('search') && $request->input('search') != '') {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', '%'. $searchTerm . '%')
                  ->orWhere('instructor', 'LIKE','%'. $searchTerm . '%');
            });
        }

        $courses = $query->paginate(9);

        return view('courses.my-courses', [
            'courses' => $courses
        ]);
    }
}