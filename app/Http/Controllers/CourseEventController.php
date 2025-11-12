<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEvent;
use Illuminate\Http\Request;

class CourseEventController extends Controller
{
    public function index(Course $course)
    {
        $this->authorize('view', $course);

        $events = $course->courseEvents()
            ->orderBy('start_date')
            ->orderBy('start_time')
            ->paginate(15);

        return view('courses.events.index', compact('course', 'events'));
    }

    public function create(Course $course)
    {
        $this->authorize('update', $course);

        return view('courses.events.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'required',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'max_participants' => 'nullable|integer|min:1',
        ]);

        $event = $course->courseEvents()->create([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'start_time' => $request->start_time,
            'end_date' => $request->end_date,
            'end_time' => $request->end_time,
            'location' => $request->location,
            'status' => $request->status,
            'max_participants' => $request->max_participants,
            'registered_participants' => 0,
        ]);

        logActivity('course_event_created', 'CourseEvent', $event->id, [
            'course_title' => $course->title,
            'event_title' => $event->title,
        ]);

        return redirect()->route('courses.events.index', $course)
            ->with('success', 'Course event created successfully.');
    }

    public function edit(CourseEvent $event)
    {
        $this->authorize('update', $event->course);

        $course = $event->course;
        return view('courses.events.edit', compact('course', 'event'));
    }

    public function update(Request $request, CourseEvent $event)
    {
        $this->authorize('update', $event->course);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'required',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'max_participants' => 'nullable|integer|min:1',
        ]);

        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'start_time' => $request->start_time,
            'end_date' => $request->end_date,
            'end_time' => $request->end_time,
            'location' => $request->location,
            'status' => $request->status,
            'max_participants' => $request->max_participants,
        ]);

        logActivity('course_event_updated', 'CourseEvent', $event->id, [
            'course_title' => $event->course->title,
            'event_title' => $event->title,
        ]);

        return redirect()->route('courses.events.index', $event->course)
            ->with('success', 'Course event updated successfully.');
    }

    public function destroy(CourseEvent $event)
    {
        $this->authorize('update', $event->course);

        $course = $event->course;

        logActivity('course_event_deleted', 'CourseEvent', $event->id, [
            'course_title' => $course->title,
            'event_title' => $event->title,
        ]);

        $event->delete();

        return redirect()->route('courses.events.index', $course)
            ->with('success', 'Course event deleted successfully.');
    }
}
