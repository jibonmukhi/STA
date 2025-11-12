@extends('layouts.advanced-dashboard')

@section('title', 'Edit Enrollment')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Edit Enrollment</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('courses.show', $course) }}">{{ $course->title }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('courses.enrollments.index', $course) }}">Enrollments</a></li>
                            <li class="breadcrumb-item active">Edit Enrollment</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('courses.enrollments.index', $course) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Enrollments
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-edit"></i> Update Student Progress
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('enrollments.update', $enrollment) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label">Progress Percentage <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number"
                                       class="form-control @error('progress_percentage') is-invalid @enderror"
                                       name="progress_percentage"
                                       value="{{ old('progress_percentage', $enrollment->progress_percentage) }}"
                                       min="0"
                                       max="100"
                                       required>
                                <span class="input-group-text">%</span>
                                @error('progress_percentage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Enter the student's progress percentage (0-100)</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                <option value="enrolled" {{ old('status', $enrollment->status) == 'enrolled' ? 'selected' : '' }}>
                                    Enrolled
                                </option>
                                <option value="in_progress" {{ old('status', $enrollment->status) == 'in_progress' ? 'selected' : '' }}>
                                    In Progress
                                </option>
                                <option value="completed" {{ old('status', $enrollment->status) == 'completed' ? 'selected' : '' }}>
                                    Completed
                                </option>
                                <option value="dropped" {{ old('status', $enrollment->status) == 'dropped' ? 'selected' : '' }}>
                                    Dropped
                                </option>
                                <option value="failed" {{ old('status', $enrollment->status) == 'failed' ? 'selected' : '' }}>
                                    Failed
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Final Score</label>
                                <div class="input-group">
                                    <input type="number"
                                           class="form-control @error('final_score') is-invalid @enderror"
                                           name="final_score"
                                           value="{{ old('final_score', $enrollment->final_score) }}"
                                           min="0"
                                           max="100"
                                           step="0.01">
                                    <span class="input-group-text">points</span>
                                    @error('final_score')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Optional: Final score (0-100)</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Grade</label>
                                <input type="text"
                                       class="form-control @error('grade') is-invalid @enderror"
                                       name="grade"
                                       value="{{ old('grade', $enrollment->grade) }}"
                                       maxlength="10"
                                       placeholder="e.g., A+, B, Pass">
                                @error('grade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Optional: Letter grade or pass/fail</small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      name="notes"
                                      rows="4"
                                      placeholder="Add any additional notes or comments about this enrollment...">{{ old('notes', $enrollment->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Enrollment
                            </button>
                            <a href="{{ route('courses.enrollments.index', $course) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i> Student Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img src="{{ $enrollment->user->photo_url }}"
                             alt="{{ $enrollment->user->full_name }}"
                             class="rounded-circle"
                             style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #dee2e6;">
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Student Name</label>
                        <div><strong>{{ $enrollment->user->full_name }}</strong></div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Email</label>
                        <div>{{ $enrollment->user->email }}</div>
                    </div>

                    @if($enrollment->company)
                        <div class="mb-3">
                            <label class="text-muted small">Company</label>
                            <div>{{ $enrollment->company->name }}</div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="text-muted small">Enrolled Date</label>
                        <div>{{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('M d, Y') : 'N/A' }}</div>
                    </div>

                    @if($enrollment->completed_at)
                        <div class="mb-3">
                            <label class="text-muted small">Completed Date</label>
                            <div>{{ $enrollment->completed_at->format('M d, Y') }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-book"></i> Course Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <label class="text-muted small">Course Title</label>
                        <div><strong>{{ $course->title }}</strong></div>
                    </div>
                    <div class="mb-2">
                        <label class="text-muted small">Course Code</label>
                        <div>{{ $course->course_code }}</div>
                    </div>
                    <div class="mb-2">
                        <label class="text-muted small">Duration</label>
                        <div>{{ $course->duration_hours }} hours</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
