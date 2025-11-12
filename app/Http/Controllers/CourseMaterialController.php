<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CourseMaterialController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'material_type' => 'required|in:pdf,video,document,presentation,image,other',
            'file' => 'required|file|max:51200',
            'is_downloadable' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        $file = $request->file('file');
        $path = $file->store('course_materials', 'public');

        $material = $course->materials()->create([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'material_type' => $request->material_type,
            'order' => $request->order ?? 0,
            'is_downloadable' => $request->boolean('is_downloadable', true),
            'uploaded_by' => Auth::id(),
        ]);

        logActivity('course_material_uploaded', 'CourseMaterial', $material->id, [
            'course_id' => $course->id,
            'course_title' => $course->title,
            'material_title' => $material->title,
        ]);

        return redirect()->back()->with('success', 'Course material uploaded successfully.');
    }

    public function download(CourseMaterial $material)
    {
        $this->authorize('view', $material->course);

        if (!$material->is_downloadable) {
            abort(403, 'This material is not downloadable.');
        }

        return Storage::disk('public')->download($material->file_path, $material->file_name);
    }

    public function destroy(CourseMaterial $material)
    {
        $this->authorize('update', $material->course);

        logActivity('course_material_deleted', 'CourseMaterial', $material->id, [
            'course_id' => $material->course_id,
            'course_title' => $material->course->title,
            'material_title' => $material->title,
        ]);

        $material->deleteFile();
        $material->delete();

        return redirect()->back()->with('success', 'Course material deleted successfully.');
    }
}
