@component('mail::message')
# Course Assignment Notification

Hello {{ $user->name }},

A new course has been assigned to your company{{ $assignment->company ? ': **' . $assignment->company->name . '**' : '' }}.

@component('mail::panel')
**{{ $course->title }}**

@if($course->course_code)
**Course Code:** {{ $course->course_code }}
@endif

@if($course->description)
{{ Str::limit($course->description, 200) }}
@endif
@endcomponent

## Assignment Details

@component('mail::table')
| | |
|:---|:---|
@if($assignment->is_mandatory)
| **Type** | 🔴 **Mandatory Course** |
@else
| **Type** | Optional Course |
@endif
@if($assignment->assigned_date)
| **Assigned Date** | {{ $assignment->assigned_date->format('M d, Y') }} |
@endif
@if($assignment->due_date)
| **Due Date** | {{ $assignment->due_date->format('M d, Y') }} |
@endif
| **Category** | {{ App\Models\Course::getCategories()[$course->category] ?? $course->category }} |
| **Level** | {{ ucfirst($course->level) }} |
| **Duration** | {{ $course->duration_hours }} hours |
@if($course->teacher)
| **Teacher** | {{ $course->teacher->name }} |
@endif
@endcomponent

@if($course->start_date && $course->end_date)
## Schedule

**Start:** {{ $course->start_date->format('M d, Y') }}@if($course->start_time) at {{ \Carbon\Carbon::parse($course->start_time)->format('g:i A') }}@endif

**End:** {{ $course->end_date->format('M d, Y') }}@if($course->end_time) at {{ \Carbon\Carbon::parse($course->end_time)->format('g:i A') }}@endif
@endif

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

@if($assignment->is_mandatory)
@component('mail::promotion')
This is a **mandatory course** for your company. Please complete it by the due date.
@endcomponent
@endif

@if($assignment->notes)
### Additional Notes

{{ $assignment->notes }}
@endif

If you have any questions about this course assignment, please contact your administrator.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
