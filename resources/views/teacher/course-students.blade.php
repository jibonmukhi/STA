@extends('layouts.advanced-dashboard')

@section('page-title', __('teacher.course_students'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ $course->title }} - {{ __('teacher.students') }}</h5>
                    <p class="text-muted small mb-0">{{ $course->course_code }}</p>
                </div>
                <div class="card-body">
                    <!-- Stats -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-primary">{{ $stats['total_enrolled'] }}</h3>
                                <p class="text-muted small">{{ __('teacher.total_enrolled') }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-info">{{ $stats['in_progress'] }}</h3>
                                <p class="text-muted small">{{ __('teacher.in_progress') }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-success">{{ $stats['completed'] }}</h3>
                                <p class="text-muted small">{{ __('teacher.completed') }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-warning">{{ number_format($stats['average_progress'], 1) }}%</h3>
                                <p class="text-muted small">{{ __('teacher.average_progress') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Students Table -->
                    @if($enrollments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('teacher.student_name') }}</th>
                                        <th>{{ __('teacher.email') }}</th>
                                        <th>{{ __('teacher.enrolled_date') }}</th>
                                        <th>{{ __('teacher.progress') }}</th>
                                        <th>{{ __('teacher.status') }}</th>
                                        <th>{{ __('teacher.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrollments as $enrollment)
                                        <tr>
                                            <td>{{ $enrollment->user->full_name ?? 'N/A' }}</td>
                                            <td>{{ $enrollment->user->email ?? 'N/A' }}</td>
                                            <td>{{ $enrollment->enrolled_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-{{ $enrollment->progress_percentage >= 70 ? 'success' : ($enrollment->progress_percentage >= 40 ? 'warning' : 'danger') }}"
                                                         role="progressbar"
                                                         style="width: {{ $enrollment->progress_percentage }}%"
                                                         aria-valuenow="{{ $enrollment->progress_percentage }}"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100">
                                                        {{ number_format($enrollment->progress_percentage, 0) }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{!! $enrollment->status_badge !!}</td>
                                            <td>
                                                @if($enrollment->status === 'completed' && $enrollment->user_id)
                                                    <a href="{{ route('certificates.create', ['user_id' => $enrollment->user_id, 'course_id' => $course->id]) }}"
                                                       class="btn btn-sm btn-success">
                                                        <i class="fas fa-certificate"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $enrollments->links() }}
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-4x text-muted mb-3"></i>
                            <p class="text-muted">{{ __('teacher.no_students_enrolled') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
