@extends('layouts.advanced-dashboard')

@section('title', 'Edit Course')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Edit Course: {{ $course->title }}</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('courses.show', $course) }}">{{ $course->title }}</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Course
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('courses.update', $course) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Course Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Course Title *</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                       name="title" value="{{ old('title', $course->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Course Code *</label>
                                <input type="text" class="form-control @error('course_code') is-invalid @enderror"
                                       name="course_code" value="{{ old('course_code', $course->course_code) }}" required>
                                @error('course_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Category *</label>
                                <select class="form-select @error('category') is-invalid @enderror" name="category" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $key => $value)
                                        <option value="{{ $key }}" {{ old('category', $course->category) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Delivery Method *</label>
                                <select class="form-select @error('delivery_method') is-invalid @enderror" name="delivery_method" required>
                                    <option value="">Select Method</option>
                                    @foreach($deliveryMethods as $key => $value)
                                        <option value="{{ $key }}" {{ old('delivery_method', $course->delivery_method) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('delivery_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status *</label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                    @foreach($statuses as $key => $value)
                                        <option value="{{ $key }}" {{ old('status', $course->status) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      name="description" rows="3">{{ old('description', $course->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Assigned Teachers</label>
                            <div class="mb-2">
                                <input type="text" class="form-control form-control-sm" id="teacherSearch" placeholder="Search teachers..." onkeyup="filterTeachers()">
                            </div>
                            <div class="border rounded p-3" style="max-height: 250px; overflow-y: auto; background: white;">
                                <div class="mb-2">
                                    <small class="text-muted">Select one or more teachers (check to assign)</small>
                                </div>
                                @php
                                    $assignedTeacherIds = $course->teachers->pluck('id')->toArray();
                                @endphp
                                @foreach($teachers as $teacher)
                                    <div class="form-check mb-2 teacher-search-item" data-teacher-name="{{ strtolower($teacher->full_name) }}" data-teacher-email="{{ strtolower($teacher->email) }}">
                                        <input class="form-check-input" type="checkbox" name="teacher_ids[]" value="{{ $teacher->id }}" id="teacher_{{ $teacher->id }}"
                                               {{ in_array($teacher->id, old('teacher_ids', $assignedTeacherIds)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="teacher_{{ $teacher->id }}">
                                            {{ $teacher->full_name }} ({{ $teacher->email }})
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('teacher_ids')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Course Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Duration (Hours) *</label>
                            <input type="number" class="form-control @error('duration_hours') is-invalid @enderror"
                                   name="duration_hours" value="{{ old('duration_hours', $course->duration_hours) }}" min="1" required>
                            @error('duration_hours')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <h5 class="mt-4 mb-3">Course Schedule (Start to End Time)</h5>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                       name="start_date" value="{{ old('start_date', $course->start_date?->format('Y-m-d')) }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label">Start Time</label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                                       name="start_time" value="{{ old('start_time', $course->start_time) }}">
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                       name="end_date" value="{{ old('end_date', $course->end_date?->format('Y-m-d')) }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label">End Time</label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror"
                                       name="end_time" value="{{ old('end_time', $course->end_time) }}">
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                       {{ old('is_active', $course->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    Active Course
                                </label>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Course
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function filterTeachers() {
    const searchTerm = document.getElementById('teacherSearch').value.toLowerCase();
    const teacherItems = document.querySelectorAll('.teacher-search-item');

    teacherItems.forEach(item => {
        const teacherName = item.getAttribute('data-teacher-name');
        const teacherEmail = item.getAttribute('data-teacher-email');

        if (teacherName.includes(searchTerm) || teacherEmail.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>
@endsection