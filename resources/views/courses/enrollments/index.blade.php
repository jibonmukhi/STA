@extends('layouts.advanced-dashboard')

@section('title', 'Manage Enrollments - ' . $course->title)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Manage Enrollments</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('courses.show', $course) }}">{{ $course->title }}</a></li>
                            <li class="breadcrumb-item active">Enrollments</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('courses.enrollments.create', $course) }}" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Enroll Users
                    </a>
                    <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Course
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Enrolled</h5>
                    <h2 class="mb-0">{{ $stats['total'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">In Progress</h5>
                    <h2 class="mb-0">{{ $stats['in_progress'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Completed</h5>
                    <h2 class="mb-0">{{ $stats['completed'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5 class="card-title">Avg Progress</h5>
                    <h2 class="mb-0">{{ number_format($stats['average_progress'], 1) }}%</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Enrollments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Enrolled Students</h5>
                </div>
                <div class="card-body">
                    @if($enrollments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Company</th>
                                        <th>Enrolled Date</th>
                                        <th>Progress</th>
                                        <th>Status</th>
                                        <th>Score</th>
                                        <th>Grade</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrollments as $enrollment)
                                    <tr>
                                        <td>
                                            <strong>{{ $enrollment->user->name }}</strong><br>
                                            <small class="text-muted">{{ $enrollment->user->email }}</small>
                                        </td>
                                        <td>{{ $enrollment->company?->name ?? 'N/A' }}</td>
                                        <td>{{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            <div class="progress" style="min-width: 100px;">
                                                <div class="progress-bar" role="progressbar" style="width: {{ $enrollment->progress_percentage }}%;"
                                                     aria-valuenow="{{ $enrollment->progress_percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                    {{ $enrollment->progress_percentage }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'enrolled' => 'secondary',
                                                    'in_progress' => 'info',
                                                    'completed' => 'success',
                                                    'dropped' => 'warning',
                                                    'failed' => 'danger'
                                                ];
                                                $color = $statusColors[$enrollment->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $enrollment->status)) }}</span>
                                        </td>
                                        <td>{{ $enrollment->final_score ?? '-' }}</td>
                                        <td>{{ $enrollment->grade ?? '-' }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#updateProgressModal{{ $enrollment->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('enrollments.destroy', $enrollment) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to remove this enrollment?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Update Progress Modal -->
                                    <div class="modal fade" id="updateProgressModal{{ $enrollment->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('enrollments.update-progress', $enrollment) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Update Progress - {{ $enrollment->user->name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Progress %</label>
                                                            <input type="number" class="form-control" name="progress_percentage"
                                                                   value="{{ $enrollment->progress_percentage }}" min="0" max="100" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Status</label>
                                                            <select class="form-select" name="status" required>
                                                                <option value="enrolled" {{ $enrollment->status == 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                                                                <option value="in_progress" {{ $enrollment->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                                <option value="completed" {{ $enrollment->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                                <option value="dropped" {{ $enrollment->status == 'dropped' ? 'selected' : '' }}>Dropped</option>
                                                                <option value="failed" {{ $enrollment->status == 'failed' ? 'selected' : '' }}>Failed</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Final Score</label>
                                                            <input type="number" class="form-control" name="final_score"
                                                                   value="{{ $enrollment->final_score }}" min="0" max="100" step="0.01">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Grade</label>
                                                            <input type="text" class="form-control" name="grade"
                                                                   value="{{ $enrollment->grade }}" maxlength="10">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Notes</label>
                                                            <textarea class="form-control" name="notes" rows="3">{{ $enrollment->notes }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Update Progress</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $enrollments->links() }}
                        </div>
                    @else
                        <p class="text-muted text-center py-4">No students enrolled yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
