@extends('layouts.advanced-dashboard')

@section('title', 'Course Management')

@section('content')
<script>
    // Pass translations to JavaScript
    window.translations = {
        confirm_status_change: {!! json_encode(trans('courses.confirm_status_change')) !!},
        confirm_send_email: {!! json_encode(trans('courses.confirm_send_email')) !!},
        email_sent_success: {!! json_encode(trans('courses.email_sent_success')) !!},
        sending_emails: {!! json_encode(trans('courses.sending_emails')) !!}
    };
</script>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>{{ trans('courses.course_management') }}</h2>
                    <p class="text-muted">{{ trans('courses.all_started_courses') }}</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ trans('courses.dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ trans('courses.course_management') }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('calendar') }}" class="btn btn-outline-primary">
                        <i class="fas fa-calendar-alt"></i> {{ trans('courses.view_calendar') }}
                    </a>
                    @can('create', App\Models\Course::class)
                    <a href="{{ route('course-management.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ trans('courses.start_new') }}
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('course-management.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">{{ trans('courses.search') }}</label>
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                                       placeholder="{{ trans('courses.search_courses') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ trans('courses.category') }}</label>
                                <select class="form-select" name="category">
                                    <option value="">{{ trans('courses.all_categories') }}</option>
                                    @foreach($categories as $key => $value)
                                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                            {{ trans('courses.categories.' . $key, [], null, $value) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ trans('courses.delivery_method') }}</label>
                                <select class="form-select" name="delivery_method">
                                    <option value="">{{ trans('courses.all_methods') }}</option>
                                    @foreach($deliveryMethods as $key => $value)
                                        <option value="{{ $key }}" {{ request('delivery_method') == $key ? 'selected' : '' }}>
                                            {{ trans('courses.delivery_methods.' . $key, [], null, $value) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ trans('courses.status') }}</label>
                                <select class="form-select" name="status">
                                    <option value="">All Statuses</option>
                                    @foreach($statuses as $key => $value)
                                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                            {{ dataVaultLabel('course_status', $key) ?? ucfirst($value) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row g-3 mt-0">
                            <div class="col-md-3">
                                <label class="form-label">Company</label>
                                <div class="dropdown-wrapper">
                                    <input type="text" class="form-control" id="companySearch" placeholder="Type to search..." autocomplete="off">
                                    <input type="hidden" name="company_id" id="companyValue" value="{{ request('company_id') }}">
                                    <div class="dropdown-list" id="companyList">
                                        <div class="dropdown-item" data-value="">Select Company</div>
                                        @foreach($companies as $company)
                                            <div class="dropdown-item" data-value="{{ $company->id }}" data-label="{{ $company->name }}">
                                                {{ $company->name }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Teacher</label>
                                <div class="dropdown-wrapper">
                                    <input type="text" class="form-control" id="teacherSearch" placeholder="Type to search..." autocomplete="off">
                                    <input type="hidden" name="teacher_id" id="teacherValue" value="{{ request('teacher_id') }}">
                                    <div class="dropdown-list" id="teacherList">
                                        <div class="dropdown-item" data-value="">Select Teacher</div>
                                        @foreach($teachers as $teacher)
                                            <div class="dropdown-item" data-value="{{ $teacher->id }}" data-label="{{ $teacher->name }}">
                                                {{ $teacher->name }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Start Date</label>
                                <input type="text" class="form-control datepicker" id="filter_start_date" placeholder="DD/MM/YYYY" autocomplete="off" readonly>
                                <input type="hidden" name="start_date" id="filter_start_date_hidden" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">End Date</label>
                                <input type="text" class="form-control datepicker" id="filter_end_date" placeholder="DD/MM/YYYY" autocomplete="off" readonly>
                                <input type="hidden" name="end_date" id="filter_end_date_hidden" value="{{ request('end_date') }}">
                            </div>
                        </div>
                        <div class="row g-3 mt-0">
                            <div class="col-md-12 d-flex justify-content-end">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> {{ trans('courses.filter') }}
                                    </button>
                                    <a href="{{ route('course-management.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> {{ trans('courses.clear_filters') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    @if($courses->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">{{ trans('courses.no_courses_found') }}</h4>
                            <p class="text-muted">{{ trans('courses.no_courses_message') }}</p>
                            @can('create', App\Models\Course::class)
                            <a href="{{ route('course-management.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ trans('courses.start_new_course') }}
                            </a>
                            @endcan
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ trans('courses.course_code') }}</th>
                                        <th>{{ trans('courses.title') }}</th>
                                        <th>{{ trans('courses.category') }}</th>
                                        <th>{{ trans('courses.teacher') }}</th>
                                        <th>Company</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>{{ trans('courses.hours') }}</th>
                                        <th>{{ trans('courses.participation') }}</th>
                                        <th>{{ trans('courses.status') }}</th>
                                        <th class="text-end">{{ trans('courses.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courses as $course)
                                        <tr class="course-row">
                                            <td>
                                                <strong class="text-primary">{{ $course->course_code }}</strong>
                                            </td>
                                            <td>
                                                <strong>{{ $course->title }}</strong>
                                                @if($course->is_mandatory)
                                                    <span class="badge bg-warning ms-1">{{ trans('courses.mandatory') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $categoryColor = dataVaultColor('course_category', $course->category) ?? 'info';
                                                    $categoryLabel = dataVaultLabel('course_category', $course->category) ?? ($categories[$course->category] ?? $course->category);
                                                @endphp
                                                <span class="badge bg-{{ $categoryColor }}">
                                                    {{ $categoryLabel }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($course->teachers && $course->teachers->count() > 0)
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach($course->teachers as $teacher)
                                                            <div class="d-flex align-items-center">
                                                                @if($teacher->photo_url && $teacher->photo_url !== '/storage/')
                                                                    <img src="{{ $teacher->photo_url }}" alt="{{ $teacher->full_name }}"
                                                                         class="rounded-circle me-1" style="object-fit: cover; width: 24px; height: 24px;">
                                                                @endif
                                                                <span class="small">{{ $teacher->full_name }}</span>
                                                                @if(!$loop->last)<span class="text-muted mx-1">|</span>@endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @elseif($course->teacher)
                                                    <div class="d-flex align-items-center">
                                                        @if($course->teacher->photo_url && $course->teacher->photo_url !== '/storage/')
                                                            <img src="{{ $course->teacher->photo_url }}" alt="{{ $course->teacher->full_name }}"
                                                                 class="rounded-circle me-2" style="object-fit: cover; width: 24px; height: 24px;">
                                                        @endif
                                                        <span>{{ $course->teacher->full_name }}</span>
                                                    </div>
                                                @elseif($course->instructor)
                                                    <span class="text-muted">{{ $course->instructor }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($course->assignedCompanies && $course->assignedCompanies->count() > 0)
                                                    <span class="badge bg-info">{{ $course->assignedCompanies->first()->name }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($course->start_date)
                                                    <small>{{ $course->start_date->format('d/m/Y') }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($course->end_date)
                                                    <small>{{ $course->end_date->format('d/m/Y') }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($course->duration_hours)
                                                    <small>{{ $course->duration_hours }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ trans('courses.delivery_methods.' . $course->delivery_method, [], null, $deliveryMethods[$course->delivery_method] ?? $course->delivery_method) }}
                                                </span>
                                            </td>
                                            <td>
                                                @can('update', $course)
                                                <select class="form-select form-select-sm status-dropdown"
                                                        data-course-id="{{ $course->id }}"
                                                        style="min-width: 130px;">
                                                    @foreach($statuses as $statusKey => $statusLabel)
                                                        @php
                                                            $statusColor = dataVaultColor('course_status', $statusKey) ?? 'secondary';
                                                            $statusIcon = dataVaultIcon('course_status', $statusKey) ?? 'fas fa-circle';
                                                        @endphp
                                                        <option value="{{ $statusKey }}"
                                                                {{ $course->status == $statusKey ? 'selected' : '' }}
                                                                data-color="{{ $statusColor }}"
                                                                data-icon="{{ $statusIcon }}">
                                                            {{ $statusLabel }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @else
                                                @php
                                                    $statusColor = dataVaultColor('course_status', $course->status) ?? 'secondary';
                                                    $statusIcon = dataVaultIcon('course_status', $course->status) ?? 'fas fa-circle';
                                                    $statusLabel = dataVaultLabel('course_status', $course->status) ?? ucfirst($course->status);
                                                @endphp
                                                <span class="badge bg-{{ $statusColor }}">
                                                    <i class="{{ $statusIcon }}"></i> {{ $statusLabel }}
                                                </span>
                                                @endcan
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('course-management.show', $course) }}" class="btn btn-sm btn-outline-info" title="{{ trans('courses.view') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @can('update', $course)
                                                    <a href="{{ route('course-management.edit', $course) }}" class="btn btn-sm btn-outline-primary" title="{{ trans('courses.edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-success send-email-btn"
                                                            data-course-id="{{ $course->id }}"
                                                            data-course-title="{{ $course->title }}"
                                                            title="{{ trans('courses.send_notification') }}">
                                                        <i class="fas fa-envelope"></i>
                                                    </button>
                                                    @endcan
                                                    @can('delete', $course)
                                                    <form action="{{ route('course-management.destroy', $course) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ trans('courses.delete') }}"
                                                                onclick="return confirm('{{ trans('courses.confirm_delete') }}');">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer bg-light">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <div class="d-flex align-items-center">
                                        <label class="me-2 mb-0 text-nowrap">{{ trans('courses.rows_per_page') }}:</label>
                                        <select class="form-select form-select-sm" id="perPageSelect" style="width: auto;">
                                            <option value="10" {{ request('per_page', 25) == 10 ? 'selected' : '' }}>10</option>
                                            <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                                            <option value="50" {{ request('per_page', 25) == 50 ? 'selected' : '' }}>50</option>
                                            <option value="100" {{ request('per_page', 25) == 100 ? 'selected' : '' }}>100</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 text-center my-2 my-md-0">
                                    <small class="text-muted">
                                        {{ trans('courses.showing_entries', [
                                            'from' => $courses->firstItem() ?? 0,
                                            'to' => $courses->lastItem() ?? 0,
                                            'total' => $courses->total()
                                        ]) }}
                                    </small>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex justify-content-end">
                                        {{ $courses->withQueryString()->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.course-row {
    transition: background-color 0.2s;
}

.course-row:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.table thead th {
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #dee2e6;
    padding: 0.5rem;
}

.table tbody td {
    vertical-align: middle;
    padding: 0.5rem;
    font-size: 0.9rem;
}

.table tbody td img {
    width: 28px;
    height: 28px;
}

.table .badge {
    font-size: 0.75rem;
    padding: 0.25em 0.5em;
}

.pagination {
    margin-bottom: 0;
}

.card {
    position: unset !important;
}

.dropdown-wrapper {
    position: relative;
}

.dropdown-list {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    max-height: 250px;
    overflow-y: auto;
    display: none;
    z-index: 1000;
    margin-top: 2px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.dropdown-list.show {
    display: block;
}

.dropdown-item {
    padding: 0.5rem 0.75rem;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
}

.dropdown-item:last-child {
    border-bottom: none;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

.dropdown-item.selected {
    background-color: #0d6efd;
    color: white;
}

.dropdown-item.hidden {
    display: none;
}
.ui-datepicker {
    z-index: 9999 !important;
}
</style>

@push('styles')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize start date datepicker
    $('#filter_start_date').datepicker({
        dateFormat: 'dd/mm/yy',
        altField: '#filter_start_date_hidden',
        altFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        yearRange: '-10:+10',
        showButtonPanel: true
    });

    // Initialize end date datepicker
    $('#filter_end_date').datepicker({
        dateFormat: 'dd/mm/yy',
        altField: '#filter_end_date_hidden',
        altFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        yearRange: '-10:+10',
        showButtonPanel: true
    });

    // Set initial display values if dates are present
    var startDateValue = $('#filter_start_date_hidden').val();
    if (startDateValue) {
        var startDateParts = startDateValue.split('-');
        $('#filter_start_date').val(startDateParts[2] + '/' + startDateParts[1] + '/' + startDateParts[0]);
    }

    var endDateValue = $('#filter_end_date_hidden').val();
    if (endDateValue) {
        var endDateParts = endDateValue.split('-');
        $('#filter_end_date').val(endDateParts[2] + '/' + endDateParts[1] + '/' + endDateParts[0]);
    }
});
</script>
@endpush

<script>
document.addEventListener('DOMContentLoaded', function() {
    const perPageSelect = document.getElementById('perPageSelect');

    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            url.searchParams.delete('page'); // Reset to first page when changing per_page
            window.location.href = url.toString();
        });
    }

    // Custom dropdown functionality
    function initCustomDropdown(searchInputId, valueInputId, listId) {
        const searchInput = document.getElementById(searchInputId);
        const valueInput = document.getElementById(valueInputId);
        const list = document.getElementById(listId);

        if (!searchInput || !valueInput || !list) return;

        const items = list.querySelectorAll('.dropdown-item');

        // Set initial display value if there's a selected value
        const selectedValue = valueInput.value;
        if (selectedValue) {
            items.forEach(item => {
                if (item.dataset.value === selectedValue) {
                    searchInput.value = item.dataset.label || item.textContent.trim();
                    item.classList.add('selected');
                }
            });
        }

        // Show dropdown on focus
        searchInput.addEventListener('focus', function() {
            list.classList.add('show');
        });

        // Filter items on input
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            items.forEach(item => {
                const label = (item.dataset.label || item.textContent).toLowerCase();
                if (label.includes(searchTerm)) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        });

        // Handle item selection
        items.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Remove previous selection
                items.forEach(i => i.classList.remove('selected'));

                // Set new selection
                this.classList.add('selected');
                valueInput.value = this.dataset.value;
                searchInput.value = this.dataset.label || this.textContent.trim();

                // Hide dropdown
                list.classList.remove('show');
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !list.contains(e.target)) {
                list.classList.remove('show');
            }
        });

        // Prevent dropdown from closing when clicking inside it
        list.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Initialize both dropdowns
    initCustomDropdown('companySearch', 'companyValue', 'companyList');
    initCustomDropdown('teacherSearch', 'teacherValue', 'teacherList');

    // Handle status dropdown change
    document.querySelectorAll('.status-dropdown').forEach(function(dropdown) {
        dropdown.addEventListener('change', function() {
            const courseId = this.getAttribute('data-course-id');
            const newStatus = this.value;
            const originalValue = this.querySelector('option[selected]')?.value || this.value;
            const newStatusLabel = this.options[this.selectedIndex].text;
            const originalStatusLabel = this.querySelector('option[selected]')?.text || this.options[this.selectedIndex].text;

            // Show confirmation dialog with localized message
            const confirmMessage = window.translations.confirm_status_change
                .replace(':old_status', originalStatusLabel)
                .replace(':new_status', newStatusLabel);

            if (!confirm(confirmMessage)) {
                // User cancelled, revert to original value
                this.value = originalValue;
                return;
            }

            // Disable dropdown during update
            this.disabled = true;

            // Send AJAX request to update status
            fetch(`/course-management/${courseId}/update-status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
                    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                    alertDiv.innerHTML = `
                        <i class="fas fa-check-circle"></i> ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(alertDiv);

                    // Remove alert after 3 seconds
                    setTimeout(() => {
                        alertDiv.remove();
                    }, 3000);

                    // Update the selected option
                    dropdown.querySelector('option[selected]')?.removeAttribute('selected');
                    dropdown.querySelector(`option[value="${newStatus}"]`).setAttribute('selected', 'selected');
                } else {
                    // Revert to original value on error
                    dropdown.value = originalValue;
                    alert('Error updating status. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert to original value on error
                dropdown.value = originalValue;
                alert('Error updating status. Please try again.');
            })
            .finally(() => {
                // Re-enable dropdown
                dropdown.disabled = false;
            });
        });
    });

    // Handle send email button click
    document.querySelectorAll('.send-email-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const courseId = this.getAttribute('data-course-id');
            const courseTitle = this.getAttribute('data-course-title');

            // Show confirmation dialog with localized message
            const confirmMessage = window.translations.confirm_send_email
                .replace(':course_title', courseTitle);

            if (!confirm(confirmMessage)) {
                return;
            }

            // Disable button and show loading state
            const originalHTML = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            // Send AJAX request to send notifications
            fetch(`/course-management/${courseId}/send-notifications`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
                    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                    alertDiv.innerHTML = `
                        <i class="fas fa-check-circle"></i> ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(alertDiv);

                    // Remove alert after 5 seconds
                    setTimeout(() => {
                        alertDiv.remove();
                    }, 5000);
                } else {
                    alert('Error: ' + (data.message || 'Failed to send notification emails'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error sending notification emails. Please try again.');
            })
            .finally(() => {
                // Re-enable button and restore original content
                button.disabled = false;
                button.innerHTML = originalHTML;
            });
        });
    });
});
</script>
@endsection