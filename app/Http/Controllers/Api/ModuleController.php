<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModuleResource; 
use App\Models\Course;                  
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class ModuleController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/courses/{course}/modules",
     * tags={"Modules"},
     * summary="Get modules for a specific course",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="course", in="path", required=true, @OA\Schema(type="integer"), description="Course ID"),
     * @OA\Response(response=200, description="Modules retrieved successfully", @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Modules retrieved successfully."),
     * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Module"))
     * )),
     * @OA\Response(response=404, description="Course not found")
     * )
     */
    public function index(Course $course)
    {
        $modules = $course->modules()->orderBy('order', 'asc')->get();
        return ModuleResource::collection($modules)
            ->additional([
                'status' => 'success',
                'message' => 'Modules retrieved successfully.'
            ]);
    }

    /**
     * @OA\Get(
     * path="/api/modules/{module}",
     * tags={"Modules"},
     * summary="Get a specific module by ID",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="module", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Module retrieved successfully", @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Module retrieved successfully."),
     * @OA\Property(property="data", ref="#/components/schemas/Module")
     * )),
     * @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(Module $module)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Module retrieved successfully.',
            'data' => new ModuleResource($module)
        ]);
    }

    /**
     * @OA\Post(
     * path="/api/courses/{course}/modules",
     * tags={"Modules"},
     * summary="Create a new module for a course",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="course", in="path", required=true, @OA\Schema(type="integer"), description="Course ID"),
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * required={"title", "description"},
     * @OA\Property(property="title", type="string", example="Introduction to Laravel"),
     * @OA\Property(property="description", type="string", example="This is the first module."),
     * @OA\Property(property="pdf_content", type="string", format="binary"),
     * @OA\Property(property="video_content", type="string", format="binary")
     * )
     * )
     * ),
     * @OA\Response(response=201, description="Module created successfully", @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Module created successfully."),
     * @OA\Property(property="data", ref="#/components/schemas/Module")
     * )),
     * @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request, Course $course)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'pdf_content' => 'nullable|file|mimes:pdf|max:10240',
            'video_content' => 'nullable|file|mimes:mp4,mpeg,quicktime,webm,mkv|max:102400',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        $data = $validator->validated();
        $data['order'] = $course->modules()->count() + 1;

        if ($request->hasFile('pdf_content')) {
            $path = $request->file('pdf_content')->store('modules/pdfs', 'public');
            $data['pdf_content'] = $path;
        }

        if ($request->hasFile('video_content')) {
            $path = $request->file('video_content')->store('modules/videos', 'public');
            $data['video_content'] = $path;
        }

        $module = $course->modules()->create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Module created successfully.',
            'data' => new ModuleResource($module)
        ], 201);
    }

    /**
     * @OA\Put(
     * path="/api/modules/{module}",
     * tags={"Modules"},
     * summary="Update a module",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="module", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * required={"title", "description"},
     * @OA\Property(property="title", type="string"),
     * @OA\Property(property="description", type="string"),
     * @OA\Property(property="pdf_content", type="string", format="binary"),
     * @OA\Property(property="video_content", type="string", format="binary")
     * )
     * )
     * ),
     * @OA\Response(response=200, description="Module updated successfully", @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Module updated successfully."),
     * @OA\Property(property="data", ref="#/components/schemas/Module")
     * )),
     * @OA\Response(response=422, description="Validation error"),
     * @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(Request $request, Module $module)  // praktiknya pake post buat ngedit module
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'pdf_content' => 'nullable|file|mimes:pdf|max:10240',
            'video_content' => 'nullable|file|mimes:mp4,mov,webm,mkv|max:102400',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }
        
        $data = $validator->validated();

        if ($request->hasFile('pdf_content')) {
            if ($module->pdf_content) {
                Storage::disk('public')->delete($module->pdf_content);
            }
            $path = $request->file('pdf_content')->store('modules/pdfs', 'public');
            $data['pdf_content'] = $path;
        }

        if ($request->hasFile('video_content')) {
            if ($module->video_content) {
                Storage::disk('public')->delete($module->video_content);
            }
            $path = $request->file('video_content')->store('modules/videos', 'public');
            $data['video_content'] = $path;
        }

        $module->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Module updated successfully.',
            'data' => new ModuleResource($module)
        ]);
    }

    /**
     * @OA\Delete(
     * path="/api/modules/{module}",
     * tags={"Modules"},
     * summary="Delete a module",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="module", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=204, description="No Content"),
     * @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(Module $module)
    {
        if ($module->pdf_content) {
            Storage::disk('public')->delete($module->pdf_content);
        }

        $module->delete();
        return response()->json(null, 204);
    }
}
