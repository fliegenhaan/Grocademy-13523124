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
     * Display a listing of the resource.
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
     * Display the specified resource.
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
     * Store a newly created resource in storage.
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, Module $module)
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
     * Remove the specified resource from storage.
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
