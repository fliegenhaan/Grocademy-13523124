<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(title="Grocademy API", version="1.0")
 * @OA\SecurityScheme(
 * securityScheme="bearerAuth",
 * type="http",
 * scheme="bearer"
 * )
 */
class CourseController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/courses",
     * tags={"Courses"},
     * summary="Get list of courses",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="q", in="query", required=false, @OA\Schema(type="string"), description="Search query for title, topic, or instructor"),
     * @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", default=1), description="Page number"),
     * @OA\Parameter(name="limit", in="query", required=false, @OA\Schema(type="integer", default=10), description="Items per page"),
     * @OA\Response(response=200, description="Courses retrieved successfully", @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Courses retrieved successfully."),
     * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Course")),
     * @OA\Property(property="pagination", ref="#/components/schemas/Pagination")
     * ))
     * )
     */
    public function index()
    {
        $courses = Course::withCount('modules')->paginate(10);
        return CourseResource::collection($courses)
                ->additional([
                    'status' => 'success',
                    'message' => 'Courses retrieved successfully.'
                ]);
    }

    /**
     * @OA\Post(
     * path="/api/courses",
     * tags={"Courses"},
     * summary="Create a new course",
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * required={"title","description","instructor","price","topics"},
     * @OA\Property(property="title", type="string", example="Advanced Web Dev"),
     * @OA\Property(property="description", type="string", example="Learn advanced web development techniques."),
     * @OA\Property(property="instructor", type="string", example="John Doe"),
     * @OA\Property(property="price", type="number", format="float", example=299000),
     * @OA\Property(property="topics[]", type="array", @OA\Items(type="string"), example={"PHP", "Laravel"}),
     * @OA\Property(property="thumbnail_image", type="string", format="binary")
     * )
     * )
     * ),
     * @OA\Response(response=201, description="Course created successfully", @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Course created successfully."),
     * @OA\Property(property="data", ref="#/components/schemas/Course")
     * )),
     * @OA\Response(response=422, description="Validation error")
     * )
     */
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

    /**
     * @OA\Get(
     * path="/api/courses/{id}",
     * tags={"Courses"},
     * summary="Get a specific course by ID",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Course retrieved successfully", @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Course retrieved successfully."),
     * @OA\Property(property="data", ref="#/components/schemas/Course")
     * )),
     * @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(Course $course)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Course retrieved successfully.',
            'data' => new CourseResource($course->loadCount('modules'))
        ]);
    }

    /**
     * @OA\Put(
     * path="/api/courses/{id}",
     * tags={"Courses"},
     * summary="Update a course",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * required={"title","description","instructor","price","topics"},
     * @OA\Property(property="title", type="string"),
     * @OA\Property(property="description", type="string"),
     * @OA\Property(property="instructor", type="string"),
     * @OA\Property(property="price", type="number", format="float"),
     * @OA\Property(property="topics[]", type="array", @OA\Items(type="string")),
     * @OA\Property(property="thumbnail_image", type="string", format="binary")
     * )
     * )
     * ),
     * @OA\Response(response=200, description="Course updated successfully", @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Course updated successfully."),
     * @OA\Property(property="data", ref="#/components/schemas/Course")
     * )),
     * @OA\Response(response=422, description="Validation error"),
     * @OA\Response(response=404, description="Not Found")
     * )
     */
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

    /**
     * @OA\Delete(
     * path="/api/courses/{id}",
     * tags={"Courses"},
     * summary="Delete a course",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=204, description="No Content"),
     * @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(Course $course)
    {
        if ($course->thumbnail_image) {
            Storage::disk('public')->delete($course->thumbnail_image);
        }

        $course->delete();
        return response()->json(null, 204);
    }
}