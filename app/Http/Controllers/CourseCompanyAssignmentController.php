<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Company;
use App\Models\CourseCompanyAssignment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Services\AuditLogService;

class CourseCompanyAssignmentController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_ids' => 'required|array|min:1',
            'course_ids.*' => 'exists:courses,id',
            'company_ids' => 'required|array|min:1',
            'company_ids.*' => 'exists:companies,id',
            'due_date' => 'nullable|date|after_or_equal:today',
            'is_mandatory' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $assignedCount = 0;
        $skippedCount = 0;

        foreach ($validated['course_ids'] as $courseId) {
            foreach ($validated['company_ids'] as $companyId) {
                // Check if assignment already exists
                $exists = CourseCompanyAssignment::where('course_id', $courseId)
                                                 ->where('company_id', $companyId)
                                                 ->exists();

                if (!$exists) {
                    CourseCompanyAssignment::create([
                        'course_id' => $courseId,
                        'company_id' => $companyId,
                        'assigned_by' => auth()->id(),
                        'assigned_date' => now(),
                        'due_date' => $validated['due_date'] ?? null,
                        'is_mandatory' => $validated['is_mandatory'] ?? false,
                        'notes' => $validated['notes'] ?? null,
                    ]);
                    $assignedCount++;
                } else {
                    $skippedCount++;
                }
            }
        }

        // Log the assignment
        $courseNames = Course::whereIn('id', $validated['course_ids'])->pluck('title')->implode(', ');
        $companyNames = Company::whereIn('id', $validated['company_ids'])->pluck('name')->implode(', ');

        AuditLogService::logCustom(
            'courses_assigned_to_companies',
            "Assigned courses [{$courseNames}] to companies [{$companyNames}]",
            'course_company_assignments',
            'info',
            [
                'course_count' => count($validated['course_ids']),
                'company_count' => count($validated['company_ids']),
                'assigned_count' => $assignedCount,
                'skipped_count' => $skippedCount,
                'assigned_by' => auth()->id(),
            ]
        );

        $message = "Successfully assigned {$assignedCount} course-company combinations.";
        if ($skippedCount > 0) {
            $message .= " {$skippedCount} assignments were skipped (already exist).";
        }

        return redirect()->back()->with('success', $message);
    }

    public function destroy(CourseCompanyAssignment $assignment): RedirectResponse
    {
        AuditLogService::logCustom(
            'course_company_assignment_removed',
            "Removed assignment of course '{$assignment->course->title}' from company '{$assignment->company->name}'",
            'course_company_assignments',
            'warning',
            [
                'course_id' => $assignment->course_id,
                'company_id' => $assignment->company_id,
                'removed_by' => auth()->id(),
            ]
        );

        $assignment->delete();

        return redirect()->back()->with('success', 'Course assignment removed successfully.');
    }
}
