@component('mail::message')
# Course Enrollment Confirmation

Hello {{ $user->name }},

You have been successfully enrolled in the following course:

@component('mail::panel')
**{{ $course->title }}**

@if($course->course_code)
**Course Code:** {{ $course->course_code }}
@endif

@if($course->description)
{{ Str::limit($course->description, 200) }}
@endif
@endcomponent

## Course Details

@component('mail::table')
| | |
|:---|:---|
| **Category** | {{ App\Models\Course::getCategories()[$course->category] ?? $course->category }} |
| **Level** | {{ ucfirst($course->level) }} |
| **Duration** | {{ $course->duration_hours }} hours |
@if($course->teacher)
| **Teacher** | {{ $course->teacher->name }} |
@endif
@if($course->start_date)
| **Start Date** | {{ $course->start_date->format('M d, Y') }}@if($course->start_time) at {{ \Carbon\Carbon::parse($course->start_time)->format('g:i A') }}@endif |
@endif
@if($course->end_date)
| **End Date** | {{ $course->end_date->format('M d, Y') }}@if($course->end_time) at {{ \Carbon\Carbon::parse($course->end_time)->format('g:i A') }}@endif |
@endif
| **Enrollment Status** | {{ ucfirst(str_replace('_', ' ', $enrollment->status)) }} |
@endcomponent

@if($tempPassword)
## Login Credentials

To access the course, please use the following credentials:

**Email:** {{ $user->email }}
**Temporary Password:** `{{ $tempPassword }}`

⚠️ **Important:** Please change your password after your first login for security.
@endif

@component('mail::button', ['url' => route('courses.show', $course->id)])
View Course Details
@endcomponent

@if($course->materials->count() > 0)
### Course Materials Available

This course has {{ $course->materials->count() }} material(s) available for download once you access the course.
@endif

If you have any questions about this course, please contact your administrator or the course teacher.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
