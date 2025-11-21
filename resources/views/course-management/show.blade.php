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
                            <li class="breadcrumb-item"><a href="{{ route('course-management.index') }}">Course Management</a></li>
                            <li class="breadcrumb-item active">{{ $course->title }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    @can('update', $course)
                    <a href="{{ route('course-management.edit', $course) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Course
                    </a>
                    @endcan
                    <a href="{{ route('course-management.index') }}" class="btn btn-outline-secondary">
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
                            <a href="{{ route('courses.enrollments.create', $course) }}" class="btn btn-outline-primary w-100">
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
                        <div class="col-md-3">
                            <a href="{{ route('course-management.bulk-invite', $course) }}" class="btn btn-outline-warning w-100">
                                <i class="fas fa-envelope"></i> Send Invitations
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
                <div class="card-header bg-primary text-white">
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
                                <strong>{{ trans('courses.course_programme') }}:</strong>
                            </div>
                            <div class="col-sm-9">
                                <p>{{ $course->description }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Category:</strong>
                        </div>
                        <div class="col-sm-9">
                            @php
                                $categoryColor = dataVaultColor('course_category', $course->category) ?? 'info';
                                $categoryLabel = dataVaultLabel('course_category', $course->category) ?? (App\Models\Course::getCategories()[$course->category] ?? $course->category);
                            @endphp
                            <span class="badge bg-{{ $categoryColor }}">{{ $categoryLabel }}</span>
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

                    @if($course->teachers->count() > 0)
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Assigned Teachers:</strong>
                            </div>
                            <div class="col-sm-9">
                                @foreach($course->teachers as $teacher)
                                    <div class="card bg-light border-0 mb-2">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $teacher->photo_url }}" alt="{{ $teacher->full_name }}"
                                                     class="rounded-circle me-3" style="width: 48px; height: 48px; object-fit: cover; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="fas fa-chalkboard-teacher text-primary me-2"></i>
                                                        <strong class="mb-0">{{ $teacher->full_name }}</strong>
                                                    </div>
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-envelope me-1"></i>{{ $teacher->email }}
                                                    </small>
                                                    @if($teacher->phone)
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-phone me-1"></i>{{ $teacher->phone }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($course->assignedCompanies && $course->assignedCompanies->count() > 0)
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Assigned Company:</strong>
                            </div>
                            <div class="col-sm-9">
                                @php
                                    $assignedCompany = $course->assignedCompanies->first();
                                @endphp
                                <span class="badge bg-info" style="font-size: 0.875rem; padding: 0.5rem 0.75rem;">
                                    <i class="fas fa-building me-1"></i>{{ $assignedCompany->name }}
                                    @if($assignedCompany->pivot->is_mandatory)
                                        <span class="badge bg-warning ms-1">Mandatory</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endif

                    @if($course->start_date || $course->end_date)
                        <hr class="my-4">
                        <h6 class="mb-3"><i class="fas fa-clock"></i> Course Schedule</h6>

                        @if($course->start_date)
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong>Start:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $course->start_date->format('M d, Y') }}
                                    @if($course->start_time)
                                        at {{ \Carbon\Carbon::parse($course->start_time)->format('g:i A') }}
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($course->end_date)
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong>End:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $course->end_date->format('M d, Y') }}
                                    @if($course->end_time)
                                        at {{ \Carbon\Carbon::parse($course->end_time)->format('g:i A') }}
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Class Sessions Section -->
            @if($course->sessions->count() > 0)
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt"></i> {{ trans('courses.class_sessions') }}
                    </h5>
                    <span class="badge bg-white text-primary">{{ $course->sessions->count() }}</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">{{ trans('courses.total_hours') }}</small>
                        <strong>{{ $course->sessions->sum('duration_hours') }} {{ trans('courses.hours') }}</strong>
                    </div>

                    <div class="list-group">
                        @foreach($course->sessions as $session)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $session->session_title }}</h6>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-calendar me-1"></i>{{ $session->session_date->format('d/m/Y') }}
                                    </small>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-clock me-1"></i>{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                    </small>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-hourglass-half me-1"></i>{{ $session->duration_hours }} {{ trans('courses.hours') }}
                                    </small>
                                </div>
                                <span class="badge bg-info">{{ $loop->iteration }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @else
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Course Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <small class="text-muted d-block">Duration</small>
                            <strong>{{ $course->duration_hours }} hours</strong>
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
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Enrolled Students Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-users"></i> Enrolled Students
                        <span class="badge bg-white text-primary ms-2">{{ $course->enrollments->count() }}</span>
                    </h5>
                    @can('manageStudents', $course)
                    <a href="{{ route('courses.enrollments.create', $course) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-cog"></i> Manage All
                    </a>
                    @endcan
                </div>
                <div class="card-body">
                    @if($course->enrollments->count() > 0)
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
                                    @foreach($course->enrollments as $enrollment)
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
                                            @can('manageStudents', $course)
                                            <form action="{{ route('enrollments.destroy', $enrollment) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to remove this enrollment?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete Enrollment">
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
                        <p class="text-muted text-center py-4">No students enrolled yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Course Materials Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Course Materials</h5>
                    @can('update', $course)
                    <button type="button" class="btn btn-sm btn-light" onclick="toggleUploadForm()">
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
                                <label class="form-label">{{ trans('courses.course_programme') }}</label>
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

@push('styles')
<style>
/* Gradient Card Headers */
.card-header.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border: none;
}

.card-header.bg-primary.text-white {
    color: #ffffff !important;
}
</style>
@endpush

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