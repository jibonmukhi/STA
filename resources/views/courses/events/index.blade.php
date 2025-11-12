@extends('layouts.advanced-dashboard')

@section('title', 'Course Events - ' . $course->title)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Course Events & Schedule</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('courses.show', $course) }}">{{ $course->title }}</a></li>
                            <li class="breadcrumb-item active">Events</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    @can('update', $course)
                    <a href="{{ route('courses.events.create', $course) }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Event
                    </a>
                    @endcan
                    <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Course
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Scheduled Events for {{ $course->title }}</h5>
                </div>
                <div class="card-body">
                    @if($events->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Event Title</th>
                                        <th>Start Date & Time</th>
                                        <th>End Date & Time</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Participants</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($events as $event)
                                    <tr>
                                        <td>
                                            <strong>{{ $event->title }}</strong>
                                            @if($event->description)
                                                <br><small class="text-muted">{{ Str::limit($event->description, 60) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}<br>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}</small>
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($event->end_date)->format('M d, Y') }}<br>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }}</small>
                                        </td>
                                        <td>{{ $event->location ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'scheduled' => 'primary',
                                                    'in_progress' => 'info',
                                                    'completed' => 'success',
                                                    'cancelled' => 'danger'
                                                ];
                                                $color = $statusColors[$event->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $color }}">{{ ucfirst($event->status) }}</span>
                                        </td>
                                        <td>
                                            {{ $event->registered_participants ?? 0 }}
                                            @if($event->max_participants)
                                                / {{ $event->max_participants }}
                                            @endif
                                        </td>
                                        <td>
                                            @can('update', $course)
                                            <a href="{{ route('events.edit', $event) }}" class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('events.destroy', $event) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this event?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $events->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No events scheduled for this course yet.</p>
                            @can('update', $course)
                            <a href="{{ route('courses.events.create', $course) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add First Event
                            </a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
