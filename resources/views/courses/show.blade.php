@extends('layouts.advanced-dashboard')

@section('title', $course->title)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>{{ $course->title }}</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
                            <li class="breadcrumb-item active">{{ $course->title }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    @can('update', $course)
                    <a href="{{ route('courses.edit', $course) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Course
                    </a>
                    @endcan
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        @can('manageStudents', $course)
                        <div class="col-md-3">
                            <a href="{{ route('courses.enrollments.index', $course) }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-users"></i> Manage Enrollments
                            </a>
                        </div>
                        @endcan
                        @can('update', $course)
                        <div class="col-md-3">
                            <a href="{{ route('courses.events.index', $course) }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-calendar-alt"></i> Manage Events
                            </a>
                        </div>
                        @endcan
                        <div class="col-md-3">
                            <a href="{{ route('courses.schedule', $course) }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-clock"></i> View Schedule
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('courses.planning') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-project-diagram"></i> Course Planning
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Course Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Course Code:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ $course->course_code }}
                        </div>
                    </div>

                    @if($course->description)
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Description:</strong>
                            </div>
                            <div class="col-sm-9">
                                <p>{{ $course->description }}</p>
                            </div>
                        </div>
                    @endif

                    @if($course->objectives)
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Objectives:</strong>
                            </div>
                            <div class="col-sm-9">
                                <p>{{ $course->objectives }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Category:</strong>
                        </div>
                        <div class="col-sm-9">
                            <span class="badge bg-info">{{ App\Models\Course::getCategories()[$course->category] ?? $course->category }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Level:</strong>
                        </div>
                        <div class="col-sm-9">
                            <span class="badge bg-primary">{{ ucfirst($course->level) }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Delivery Method:</strong>
                        </div>
                        <div class="col-sm-9">
                            <span class="badge bg-secondary">{{ App\Models\Course::getDeliveryMethods()[$course->delivery_method] ?? $course->delivery_method }}</span>
                        </div>
                    </div>

                    @if($course->instructor)
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Instructor:</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ $course->instructor }}
                            </div>
                        </div>
                    @endif

                    @if($course->prerequisites)
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Prerequisites:</strong>
                            </div>
                            <div class="col-sm-9">
                                <p>{{ $course->prerequisites }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Course Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Duration</small>
                            <strong>{{ $course->duration_hours }} hours</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Credits</small>
                            <strong>{{ $course->credits ?: 'N/A' }}</strong>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Price</small>
                            <strong>${{ number_format($course->price, 2) }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Max Participants</small>
                            <strong>{{ $course->max_participants ?: 'Unlimited' }}</strong>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <small class="text-muted d-block">Status</small>
                            @if($course->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif

                            @if($course->is_mandatory)
                                <span class="badge bg-warning">Mandatory</span>
                            @endif
                        </div>
                    </div>

                    @if($course->available_from || $course->available_until)
                        <div class="row mb-3">
                            <div class="col-12">
                                <small class="text-muted d-block">Availability</small>
                                @if($course->available_from)
                                    <small>From: {{ $course->available_from->format('M d, Y') }}</small><br>
                                @endif
                                @if($course->available_until)
                                    <small>Until: {{ $course->available_until->format('M d, Y') }}</small>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Course Materials Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Course Materials</h5>
                    @can('update', $course)
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadMaterialModal">
                        <i class="fas fa-upload"></i> Upload Material
                    </button>
                    @endcan
                </div>
                <div class="card-body">
                    @if($course->materials->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>File Name</th>
                                        <th>Size</th>
                                        <th>Uploaded By</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($course->materials as $material)
                                    <tr>
                                        <td>
                                            <strong>{{ $material->title }}</strong>
                                            @if($material->description)
                                                <br><small class="text-muted">{{ Str::limit($material->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ ucfirst($material->material_type) }}</span>
                                        </td>
                                        <td>{{ $material->file_name }}</td>
                                        <td>{{ $material->file_size_formatted }}</td>
                                        <td>{{ $material->uploader?->name ?? 'N/A' }}</td>
                                        <td>{{ $material->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @if($material->is_downloadable)
                                                <a href="{{ route('course-materials.download', $material) }}" class="btn btn-sm btn-success" title="Download">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif
                                            @can('update', $course)
                                                <form action="{{ route('course-materials.destroy', $material) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this material?');">
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
                    @else
                        <p class="text-muted text-center py-4">No materials uploaded yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Material Modal -->
@can('update', $course)
<div class="modal fade" id="uploadMaterialModal" tabindex="-1" aria-labelledby="uploadMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('course-materials.store', $course) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadMaterialModalLabel">Upload Course Material</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="material_type" class="form-label">Material Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('material_type') is-invalid @enderror" id="material_type" name="material_type" required>
                            <option value="">Select Type</option>
                            <option value="pdf" {{ old('material_type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                            <option value="document" {{ old('material_type') == 'document' ? 'selected' : '' }}>Document</option>
                            <option value="presentation" {{ old('material_type') == 'presentation' ? 'selected' : '' }}>Presentation</option>
                            <option value="video" {{ old('material_type') == 'video' ? 'selected' : '' }}>Video</option>
                            <option value="image" {{ old('material_type') == 'image' ? 'selected' : '' }}>Image</option>
                            <option value="other" {{ old('material_type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('material_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="file" class="form-label">File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" required>
                        <small class="text-muted">Max file size: 50MB</small>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="order" class="form-label">Order</label>
                        <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', 0) }}" min="0">
                        @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_downloadable" name="is_downloadable" value="1" {{ old('is_downloadable', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_downloadable">
                            Allow Download
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload Material</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection