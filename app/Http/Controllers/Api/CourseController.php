<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::withCount('modules')->paginate(10);
        return CourseResource::collection($courses)
                ->additional([
                    'status' => 'success',
                    'message' => 'Courses retrieved successfully.'
                ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'instructor' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'topics' => 'required|array',
            'thumbnail_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'data' => null
            ], 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('thumbnail_image')) {
            $path = $request->file('thumbnail_image')->store('thumbnails', 'public');
            $data['thumbnail_image'] = $path;
        }

        $course = Course::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Course created successfully.',
            'data' => new CourseResource($course)
        ], 201);
    }

    public function show(Course $course)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Course retrieved successfully.',
            'data' => new CourseResource($course->loadCount('modules'))
        ]);
    }

    public function update(Request $request, Course $course)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'instructor' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'topics' => 'required|array',
            'thumbnail_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'data' => null
            ], 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('thumbnail_image')) {
            if ($course->thumbnail_image) {
                Storage::disk('public')->delete($course->thumbnail_image);
            }
            $path = $request->file('thumbnail_image')->store('thumbnails', 'public');
            $data['thumbnail_image'] = $path;
        }

        $course->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Course updated successfully.',
            'data' => new CourseResource($course)
        ]);
    }

    public function destroy(Course $course)
    {
        if ($course->thumbnail_image) {
            Storage::disk('public')->delete($course->thumbnail_image);
        }

        $course->delete();
        return response()->json(null, 204);
    }
}