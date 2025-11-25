@extends('layouts.advanced-dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>{{ $course->title }} - {{ __('teacher.session_attendance') }}</h2>
            <p class="text-muted mb-0">{{ __('teacher.course_code') }}: {{ $course->course_code }}</p>
            @if($course->assignedCompanies && $course->assignedCompanies->isNotEmpty())
                <p class="text-muted mb-0">
                    <span class="badge bg-secondary">{{ $course->assignedCompanies->pluck('name')->join(', ') }}</span>
                </p>
            @endif
        </div>
        <a href="{{ route('course-management.show', $course) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('teacher.back_to_course') }}
        </a>
    </div>

    @if($sessions->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> {{ __('teacher.no_sessions_created') }}
        </div>
    @elseif($enrollments->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> {{ __('teacher.no_students_enrolled_attendance') }}
        </div>
    @else
        <!-- Attendance Grid -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <!-- Search Filters -->
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label small mb-1">{{ __('teacher.student_name') }}</label>
                        <input type="text" id="search-name" class="form-control form-control-sm" placeholder="{{ __('teacher.search') }}...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">{{ __('users.surname') }}</label>
                        <input type="text" id="search-surname" class="form-control form-control-sm" placeholder="{{ __('teacher.search') }}...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">CF</label>
                        <input type="text" id="search-cf" class="form-control form-control-sm" placeholder="{{ __('teacher.search') }}...">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small mb-1">{{ __('teacher.email') }}</label>
                        <input type="text" id="search-email" class="form-control form-control-sm" placeholder="{{ __('teacher.search') }}...">
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-1">
                            <button type="button" id="search-btn" class="btn btn-primary btn-sm flex-fill">
                                <i class="fas fa-search"></i> {{ __('teacher.search') }}
                            </button>
                            <button type="button" id="clear-search-btn" class="btn btn-secondary btn-sm" title="{{ __('teacher.clear_filters') }}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-bordered table-hover attendance-grid" id="attendance-table">
                        <thead>
                            <tr>
                                <th class="sticky-col first-col" style="min-width: 200px;">{{ __('teacher.student_name') }}</th>
                                @foreach($sessions as $session)
                                    <th class="text-center session-header" style="min-width: 120px;">
                                        <div class="session-info">
                                            <div class="fw-bold">{{ $session->session_title }}</div>
                                            <small class="text-muted">{{ $session->session_date->format('d/m/Y') }}</small>
                                            <small class="d-block text-muted">{{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}</small>
                                            <small class="d-block">
                                                <span class="badge bg-{{ $session->status === 'completed' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($session->status) }}
                                                </span>
                                            </small>
                                        </div>
                                    </th>
                                @endforeach
                                <th class="text-center" style="min-width: 150px;">{{ __('teacher.progress') }}</th>
                            </tr>
                        </thead>
                        <tbody id="attendance-tbody">
                            @foreach($enrollments as $enrollment)
                                @php
                                    $nameParts = explode(' ', $enrollment->user->name);
                                    $firstName = strtolower($nameParts[0] ?? '');
                                    $lastName = strtolower(implode(' ', array_slice($nameParts, 1)) ?? '');
                                @endphp
                                <tr class="student-attendance-row"
                                    data-student-name="{{ strtolower($enrollment->user->name) }}"
                                    data-first-name="{{ $firstName }}"
                                    data-last-name="{{ $lastName }}"
                                    data-email="{{ strtolower($enrollment->user->email) }}"
                                    data-cf="{{ strtolower($enrollment->user->tax_code ?? '') }}">
                                    <td class="sticky-col first-col">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                                    {{ strtoupper(substr($enrollment->user->name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $enrollment->user->name }}</div>
                                                <small class="text-muted">{{ $enrollment->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    @foreach($sessions as $session)
                                        @php
                                            $attendance = $attendanceMatrix[$enrollment->id][$session->id] ?? null;
                                            $status = $attendance->status ?? 'not_marked';
                                            $statusConfig = [
                                                'present' => ['icon' => 'fa-check', 'color' => 'success', 'text' => __('teacher.present')],
                                                'absent' => ['icon' => 'fa-times', 'color' => 'danger', 'text' => __('teacher.absent')],
                                                'not_marked' => ['icon' => 'fa-question', 'color' => 'secondary', 'text' => __('teacher.not_marked')]
                                            ];
                                            $config = $statusConfig[$status] ?? $statusConfig['not_marked'];
                                        @endphp
                                        <td class="text-center attendance-cell"
                                            data-session-id="{{ $session->id }}"
                                            data-session-title="{{ $session->session_title }}"
                                            data-student-name="{{ $enrollment->user->name }}"
                                            data-enrollment-id="{{ $enrollment->id }}"
                                            data-user-id="{{ $enrollment->user_id }}"
                                            data-current-status="{{ $status }}"
                                            style="cursor: pointer;">
                                            <div class="attendance-status bg-{{ $config['color'] }} bg-opacity-10 p-2 rounded">
                                                <i class="fas {{ $config['icon'] }} text-{{ $config['color'] }} fa-lg"></i>
                                                <div class="small mt-1">{{ $config['text'] }}</div>
                                                @if($attendance && $attendance->attended_hours > 0)
                                                    <small class="text-muted d-block">{{ $attendance->attended_hours }}h</small>
                                                @endif
                                            </div>
                                        </td>
                                    @endforeach
                                    <td class="text-center">
                                        <div class="progress" style="height: 18px;">
                                            <div class="progress-bar bg-{{ $enrollment->progress_percentage >= 100 ? 'success' : ($enrollment->progress_percentage >= 50 ? 'info' : 'warning') }}"
                                                 role="progressbar"
                                                 style="width: {{ $enrollment->progress_percentage }}%; font-size: 0.7rem;"
                                                 aria-valuenow="{{ $enrollment->progress_percentage }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100">
                                                {{ number_format($enrollment->progress_percentage, 0) }}%
                                            </div>
                                        </div>
                                        <small class="text-muted d-block" style="font-size: 0.7rem; margin-top: 2px;">
                                            {{ $enrollment->sessions_attended }}/{{ $enrollment->total_sessions }} {{ __('teacher.sessions_attended') }}
                                        </small>
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">
                                            {{ number_format($enrollment->attended_hours, 1) }}h / {{ number_format($enrollment->total_required_hours, 1) }}h
                                        </small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Controls -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted" id="pagination-info">
                            {{ __('teacher.showing_entries', ['from' => 0, 'to' => 0, 'total' => 0]) }}
                        </span>
                        <select id="rows-per-page" class="form-select form-select-sm" style="width: 100px;">
                            <option value="10">10</option>
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="pagination-controls">
                            <!-- Pagination buttons will be generated by JavaScript -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Sessions List -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">{{ __('teacher.class_sessions') }}</h5>
            </div>
            <div class="card-body">
                <div class="row row-cols-auto">
                    @foreach($sessions as $session)
                        @php
                            $stats = $session->getAttendanceStats();
                        @endphp
                        <div class="session-card-col mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $session->session_title }}</h6>
                                    <p class="small text-muted mb-2">{{ $session->session_date->format('d/m/Y') }}</p>

                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-success bg-opacity-10 rounded">
                                                <div class="fw-bold text-success">{{ $stats['present'] }}</div>
                                                <small class="text-muted">{{ __('teacher.present') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-danger bg-opacity-10 rounded">
                                                <div class="fw-bold text-danger">{{ $stats['absent'] }}</div>
                                                <small class="text-muted">{{ __('teacher.absent') }}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="progress mb-3" style="height: 20px;">
                                        <div class="progress-bar bg-info"
                                             role="progressbar"
                                             style="width: {{ $stats['total_enrolled'] > 0 ? ($stats['marked'] / $stats['total_enrolled'] * 100) : 0 }}%">
                                            {{ $stats['marked'] }}/{{ $stats['total_enrolled'] }} {{ __('teacher.marked') }}
                                        </div>
                                    </div>

                                    <a href="{{ route('sta.session-attendance-detail', $session) }}" class="btn btn-warning btn-sm w-100 mb-2">
                                        <i class="fas fa-clipboard-check"></i> {{ __('teacher.mark_attendance') }}
                                    </a>

                                    @if($session->status === 'completed')
                                        <button class="btn btn-success btn-sm w-100" disabled>
                                            <i class="fas fa-check"></i> {{ __('teacher.session_completed') }}
                                        </button>
                                    @elseif($session->canBeClosed())
                                        <button class="btn btn-primary btn-sm w-100 close-session-btn"
                                                data-session-id="{{ $session->id }}"
                                                data-session-title="{{ $session->session_title }}">
                                            <i class="fas fa-lock"></i> {{ __('teacher.close_session') }}
                                        </button>
                                    @else
                                        <button class="btn btn-secondary btn-sm w-100" disabled>
                                            <i class="fas fa-exclamation-circle"></i> {{ $stats['not_marked'] }} {{ __('teacher.remaining') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Attendance Status Modal -->
<div class="modal fade" id="attendanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('teacher.mark_attendance_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modal-session-id">
                <input type="hidden" id="modal-enrollment-id">
                <input type="hidden" id="modal-user-id">

                <div class="mb-3">
                    <label class="form-label">{{ __('teacher.student') }}:</label>
                    <div id="modal-student-name" class="fw-bold"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('teacher.session') }}:</label>
                    <div id="modal-session-title" class="fw-bold"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('teacher.attendance_status') }}:</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="attendance-status" id="status-present" value="present">
                        <label class="btn btn-outline-success" for="status-present">
                            <i class="fas fa-check"></i> {{ __('teacher.present') }}
                        </label>

                        <input type="radio" class="btn-check" name="attendance-status" id="status-absent" value="absent">
                        <label class="btn btn-outline-danger" for="status-absent">
                            <i class="fas fa-times"></i> {{ __('teacher.absent') }}
                        </label>
                    </div>
                </div>

                <div class="mb-3" id="hours-input-group">
                    <label class="form-label">{{ __('teacher.hours_attended') }}:</label>
                    <input type="number" class="form-control" id="attended-hours" step="0.5" min="0">
                    <small class="text-muted">{{ __('teacher.leave_empty_default') }}</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('teacher.notes') }} ({{ __('teacher.optional') }}):</label>
                    <textarea class="form-control" id="attendance-notes" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('teacher.cancel') }}</button>
                <button type="button" class="btn btn-primary" id="save-attendance-btn">{{ __('teacher.save_attendance') }}</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .session-card-col {
        flex: 0 0 auto;
        width: 16.666%;
        padding: 0 0.75rem;
    }

    @media (max-width: 991.98px) {
        .session-card-col {
            width: 33.333%;
        }
    }

    @media (max-width: 767.98px) {
        .session-card-col {
            width: 50%;
        }
    }

    .attendance-grid {
        font-size: 0.85rem;
    }

    .attendance-grid td {
        padding: 0.25rem;
        vertical-align: middle;
    }

    .attendance-grid th {
        padding: 0.4rem;
    }

    .attendance-grid thead {
        position: sticky;
        top: 0;
        z-index: 20;
        background-color: #fff;
    }

    .sticky-col {
        position: sticky;
        background-color: #fff;
        z-index: 10;
    }

    .attendance-grid thead .sticky-col {
        z-index: 30;
    }

    .first-col {
        left: 0;
    }

    .session-header {
        background-color: #f8f9fa;
        vertical-align: middle;
    }

    .attendance-cell:hover {
        opacity: 0.8;
        transform: scale(1.05);
        transition: all 0.2s;
    }

    .attendance-status {
        min-height: 40px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 0.25rem !important;
    }

    .attendance-status i {
        font-size: 0.9rem;
    }

    .attendance-status .small {
        font-size: 0.65rem;
    }

    .avatar-sm .rounded-circle {
        width: 24px !important;
        height: 24px !important;
        font-size: 0.7rem;
    }

    .sticky-col.first-col .fw-bold {
        font-size: 0.8rem;
        line-height: 1.2;
    }

    .sticky-col.first-col small {
        font-size: 0.65rem;
        line-height: 1.2;
    }

    .student-attendance-row.hidden {
        display: none;
    }

    .text-orange {
        color: #fd7e14;
    }

    .bg-orange {
        background-color: #fd7e14;
    }

    .avatar-sm {
        flex-shrink: 0;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const attendanceModal = new bootstrap.Modal(document.getElementById('attendanceModal'));

    // Handle attendance cell click
    document.querySelectorAll('.attendance-cell').forEach(cell => {
        cell.addEventListener('click', function() {
            const sessionId = this.dataset.sessionId;
            const sessionTitle = this.dataset.sessionTitle;
            const studentName = this.dataset.studentName;
            const enrollmentId = this.dataset.enrollmentId;
            const userId = this.dataset.userId;
            const currentStatus = this.dataset.currentStatus;

            // Populate modal
            document.getElementById('modal-session-id').value = sessionId;
            document.getElementById('modal-enrollment-id').value = enrollmentId;
            document.getElementById('modal-user-id').value = userId;
            document.getElementById('modal-student-name').textContent = studentName;
            document.getElementById('modal-session-title').textContent = sessionTitle;

            // Set current status if marked
            if (currentStatus !== 'not_marked') {
                const statusInput = document.querySelector(`input[name="attendance-status"][value="${currentStatus}"]`);
                if (statusInput) statusInput.checked = true;
            } else {
                document.querySelectorAll('input[name="attendance-status"]').forEach(input => input.checked = false);
            }

            // Clear inputs
            document.getElementById('attended-hours').value = '';
            document.getElementById('attendance-notes').value = '';

            attendanceModal.show();
        });
    });

    // Show/hide hours input based on status
    document.querySelectorAll('input[name="attendance-status"]').forEach(input => {
        input.addEventListener('change', function() {
            const status = this.value;
            const hoursInputGroup = document.getElementById('hours-input-group');
            if (status === 'present' || status === 'late') {
                hoursInputGroup.style.display = 'block';
            } else {
                hoursInputGroup.style.display = 'none';
                document.getElementById('attended-hours').value = '';
            }
        });
    });

    // Save attendance
    document.getElementById('save-attendance-btn').addEventListener('click', function() {
        const sessionId = document.getElementById('modal-session-id').value;
        const enrollmentId = document.getElementById('modal-enrollment-id').value;
        const userId = document.getElementById('modal-user-id').value;
        const statusInput = document.querySelector('input[name="attendance-status"]:checked');
        const status = statusInput ? statusInput.value : null;
        const attendedHours = document.getElementById('attended-hours').value;
        const notes = document.getElementById('attendance-notes').value;

        if (!status) {
            alert('Please select an attendance status');
            return;
        }

        fetch(`/sta/sessions/${sessionId}/attendance`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                user_id: userId,
                enrollment_id: enrollmentId,
                status: status,
                attended_hours: attendedHours || null,
                notes: notes || null
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Failed to save attendance');
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    });

    // Close session
    document.querySelectorAll('.close-session-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const sessionId = this.dataset.sessionId;
            const sessionTitle = this.dataset.sessionTitle;

            if (!confirm(`Are you sure you want to close the session "${sessionTitle}"? This action cannot be undone.`)) {
                return;
            }

            fetch(`/sta/sessions/${sessionId}/close`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Session closed successfully');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to close session');
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        });
    });

    // Pagination and Search functionality
    let currentPage = 1;
    let rowsPerPage = 25;
    let filteredRows = [];

    function initializePagination() {
        const allRows = document.querySelectorAll('.student-attendance-row');
        filteredRows = Array.from(allRows);
        updatePagination();
    }

    function updatePagination() {
        const totalRows = filteredRows.length;
        const totalPages = Math.ceil(totalRows / rowsPerPage);

        // Hide all rows first
        document.querySelectorAll('.student-attendance-row').forEach(row => row.classList.add('hidden'));

        // Show only rows for current page
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        filteredRows.slice(start, end).forEach(row => row.classList.remove('hidden'));

        // Update pagination info
        const from = totalRows > 0 ? start + 1 : 0;
        const to = Math.min(end, totalRows);
        document.getElementById('pagination-info').textContent = `Showing ${from} to ${to} of ${totalRows} entries`;

        // Generate pagination buttons
        generatePaginationButtons(totalPages);
    }

    function generatePaginationButtons(totalPages) {
        const paginationControls = document.getElementById('pagination-controls');
        paginationControls.innerHTML = '';

        if (totalPages <= 1) return;

        // Previous button
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>`;
        paginationControls.appendChild(prevLi);

        // Page numbers
        const maxButtons = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
        let endPage = Math.min(totalPages, startPage + maxButtons - 1);

        if (endPage - startPage < maxButtons - 1) {
            startPage = Math.max(1, endPage - maxButtons + 1);
        }

        if (startPage > 1) {
            const firstLi = document.createElement('li');
            firstLi.className = 'page-item';
            firstLi.innerHTML = `<a class="page-link" href="#" data-page="1">1</a>`;
            paginationControls.appendChild(firstLi);

            if (startPage > 2) {
                const dotsLi = document.createElement('li');
                dotsLi.className = 'page-item disabled';
                dotsLi.innerHTML = '<span class="page-link">...</span>';
                paginationControls.appendChild(dotsLi);
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            const pageLi = document.createElement('li');
            pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`;
            pageLi.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
            paginationControls.appendChild(pageLi);
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const dotsLi = document.createElement('li');
                dotsLi.className = 'page-item disabled';
                dotsLi.innerHTML = '<span class="page-link">...</span>';
                paginationControls.appendChild(dotsLi);
            }

            const lastLi = document.createElement('li');
            lastLi.className = 'page-item';
            lastLi.innerHTML = `<a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>`;
            paginationControls.appendChild(lastLi);
        }

        // Next button
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>`;
        paginationControls.appendChild(nextLi);

        // Attach click handlers
        paginationControls.querySelectorAll('a.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.dataset.page);
                if (page && page !== currentPage && page >= 1 && page <= totalPages) {
                    currentPage = page;
                    updatePagination();
                }
            });
        });
    }

    // Search functionality
    function performSearch() {
        const searchName = document.getElementById('search-name').value.toLowerCase().trim();
        const searchSurname = document.getElementById('search-surname').value.toLowerCase().trim();
        const searchCF = document.getElementById('search-cf').value.toLowerCase().trim();
        const searchEmail = document.getElementById('search-email').value.toLowerCase().trim();
        const allRows = document.querySelectorAll('.student-attendance-row');

        if (searchName === '' && searchSurname === '' && searchCF === '' && searchEmail === '') {
            filteredRows = Array.from(allRows);
        } else {
            filteredRows = Array.from(allRows).filter(row => {
                const firstName = row.dataset.firstName;
                const lastName = row.dataset.lastName;
                const cf = row.dataset.cf;
                const email = row.dataset.email;

                const nameMatch = searchName === '' || firstName.includes(searchName);
                const surnameMatch = searchSurname === '' || lastName.includes(searchSurname);
                const cfMatch = searchCF === '' || cf.includes(searchCF);
                const emailMatch = searchEmail === '' || email.includes(searchEmail);

                return nameMatch && surnameMatch && cfMatch && emailMatch;
            });
        }

        currentPage = 1;
        updatePagination();
    }

    // Search button click
    document.getElementById('search-btn').addEventListener('click', performSearch);

    // Allow Enter key to trigger search in any search field
    ['search-name', 'search-surname', 'search-cf', 'search-email'].forEach(id => {
        document.getElementById(id).addEventListener('keypress', function(e) {
            if (e.which === 13 || e.keyCode === 13) {
                performSearch();
            }
        });
    });

    // Clear search filters
    document.getElementById('clear-search-btn').addEventListener('click', function() {
        document.getElementById('search-name').value = '';
        document.getElementById('search-surname').value = '';
        document.getElementById('search-cf').value = '';
        document.getElementById('search-email').value = '';
        performSearch();
    });

    // Rows per page change
    document.getElementById('rows-per-page').addEventListener('change', function() {
        rowsPerPage = parseInt(this.value);
        currentPage = 1;
        updatePagination();
    });

    // Initialize on page load
    initializePagination();
});
</script>
@endpush
@endsection
