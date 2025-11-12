@extends('layouts.advanced-dashboard')

@section('title', 'Enroll Users - ' . $course->title)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Enroll Users in Course</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('courses.show', $course) }}">{{ $course->title }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('courses.enrollments.index', $course) }}">Enrollments</a></li>
                            <li class="breadcrumb-item active">Enroll Users</li>
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
        <div class="col-lg-8 offset-lg-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Select Users to Enroll</h5>
                </div>
                <div class="card-body">
                    @if($availableUsers->count() > 0)
                        <form action="{{ route('courses.enrollments.store', $course) }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Course</label>
                                <input type="text" class="form-control" value="{{ $course->title }} ({{ $course->course_code }})" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Select Users <span class="text-danger">*</span></label>
                                <div class="mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">Select All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAll">Deselect All</button>
                                </div>
                                <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                    @foreach($availableUsers as $user)
                                        <div class="form-check">
                                            <input class="form-check-input user-checkbox" type="checkbox" name="user_ids[]" value="{{ $user->id }}" id="user{{ $user->id }}">
                                            <label class="form-check-label" for="user{{ $user->id }}">
                                                <strong>{{ $user->name }}</strong> ({{ $user->email }})
                                                @if($user->company)
                                                    <br><small class="text-muted">{{ $user->company->name }}</small>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('user_ids')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Initial Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                    <option value="enrolled" {{ old('status', 'enrolled') == 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('courses.enrollments.index', $course) }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-user-plus"></i> Enroll Selected Users
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> All available users are already enrolled in this course.
                        </div>
                        <a href="{{ route('courses.enrollments.index', $course) }}" class="btn btn-secondary">Back to Enrollments</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('selectAll').addEventListener('click', function() {
        document.querySelectorAll('.user-checkbox').forEach(function(checkbox) {
            checkbox.checked = true;
        });
    });

    document.getElementById('deselectAll').addEventListener('click', function() {
        document.querySelectorAll('.user-checkbox').forEach(function(checkbox) {
            checkbox.checked = false;
        });
    });
</script>
@endpush
@endsection
