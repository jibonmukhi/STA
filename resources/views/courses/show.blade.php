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

                    @if($course->teacher)
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Assigned Teacher:</strong>
                            </div>
                            <div class="col-sm-9">
                                <div class="card bg-light border-0">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $course->teacher->photo_url }}" alt="{{ $course->teacher->full_name }}"
                                                 class="rounded-circle me-3" style="width: 48px; height: 48px; object-fit: cover; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-1">
                                                    <i class="fas fa-chalkboard-teacher text-primary me-2"></i>
                                                    <strong class="mb-0">{{ $course->teacher->full_name }}</strong>
                                                </div>
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-envelope me-1"></i>{{ $course->teacher->email }}
                                                </small>
                                                @if($course->teacher->phone)
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-phone me-1"></i>{{ $course->teacher->phone }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

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
                            @php
                                $statusColor = dataVaultColor('course_status', $course->status) ?? 'secondary';
                                $statusIcon = dataVaultIcon('course_status', $course->status) ?? 'fas fa-circle';
                                $statusLabel = dataVaultLabel('course_status', $course->status) ?? ucfirst($course->status);
                            @endphp
                            <span class="badge bg-{{ $statusColor }}">
                                <i class="{{ $statusIcon }}"></i> {{ $statusLabel }}
                            </span>

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

    <!-- Enrolled Students Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-users"></i> Enrolled Students
                        <span class="badge bg-primary ms-2">{{ $course->enrollments->count() }}</span>
                    </h5>
                    @can('manageStudents', $course)
                    <a href="{{ route('courses.enrollments.index', $course) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-cog"></i> Manage All
                    </a>
                    @endcan
                </div>
                <div class="card-body">
                    @if($course->enrollments->count() > 0)
                        <div class="row">
                            @foreach($course->enrollments->take(8) as $enrollment)
                            <div class="col-md-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $enrollment->user->photo_url }}"
                                         alt="{{ $enrollment->user->full_name }}"
                                         class="rounded-circle me-2"
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold small">{{ $enrollment->user->name }}</div>
                                        <div class="small text-muted">
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
                                            <span class="badge badge-sm bg-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $enrollment->status)) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        @if($course->enrollments->count() > 8)
                        <div class="text-center mt-3">
                            <a href="{{ route('courses.enrollments.index', $course) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-ellipsis-h"></i> View All {{ $course->enrollments->count() }} Students
                            </a>
                        </div>
                        @endif
                    @else
                        <p class="text-muted text-center py-4 mb-0">No students enrolled yet.</p>
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
                    <button type="button" class="btn btn-sm btn-primary" onclick="toggleUploadForm()">
                        <i class="fas fa-upload"></i> Upload Material
                    </button>
                    @endcan
                </div>
                <div class="card-body">
                    <!-- Inline Upload Form -->
                    <div id="uploadMaterialForm" style="display: none;" class="bg-light p-3 mb-3 rounded">
                        <h6 class="mb-3"><i class="fas fa-upload"></i> Upload New Material</h6>
                        <form action="{{ route('course-materials.store', $course) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" name="title" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Material Type <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm" name="material_type" required>
                                        <option value="pdf">PDF</option>
                                        <option value="video">Video</option>
                                        <option value="document">Document</option>
                                        <option value="presentation">Presentation</option>
                                        <option value="image">Image</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">File <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control form-control-sm" name="file" required>
                                    <small class="text-muted">Max size: 50MB</small>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Order</label>
                                    <input type="number" class="form-control form-control-sm" name="order" value="0" min="0">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Downloadable</label>
                                    <select class="form-select form-select-sm" name="is_downloadable">
                                        <option value="1" selected>Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control form-control-sm" name="description" rows="2"
                                          placeholder="Optional description..."></textarea>
                            </div>

                            <div>
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-upload"></i> Upload
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="toggleUploadForm()">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </div>
                        </form>
                    </div>

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
@endsection

@push('scripts')
<script>
function toggleUploadForm() {
    const form = document.getElementById('uploadMaterialForm');
    if (form.style.display === 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}
</script>
@endpush