@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.show', $course) }}">{{ $course->title }}</a></li>
                    <li class="breadcrumb-item active">Send Bulk Invitation</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Send Bulk Invitation</h4>
                    <p class="text-muted mb-0 small">Send course invitations to multiple users with optional temporary passwords</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('courses.send-bulk-invite', $course) }}" method="POST">
                        @csrf

                        <!-- Recipient Type Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Recipient Type <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="recipient_type"
                                               id="recipient_companies" value="companies"
                                               {{ old('recipient_type') == 'companies' ? 'checked' : '' }}
                                               onchange="updateRecipientFields()">
                                        <label class="form-check-label" for="recipient_companies">
                                            <i class="bi bi-building"></i> Companies
                                            <p class="text-muted small mb-0">Send to all users in selected companies</p>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="recipient_type"
                                               id="recipient_teachers" value="teachers"
                                               {{ old('recipient_type') == 'teachers' ? 'checked' : '' }}
                                               onchange="updateRecipientFields()">
                                        <label class="form-check-label" for="recipient_teachers">
                                            <i class="bi bi-person-video3"></i> Teachers
                                            <p class="text-muted small mb-0">Send to selected teachers</p>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="recipient_type"
                                               id="recipient_individual" value="individual_users"
                                               {{ old('recipient_type') == 'individual_users' ? 'checked' : '' }}
                                               onchange="updateRecipientFields()">
                                        <label class="form-check-label" for="recipient_individual">
                                            <i class="bi bi-person"></i> Individual Users
                                            <p class="text-muted small mb-0">Send to specific users</p>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @error('recipient_type')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Companies Selection (shown when recipient_type is 'companies') -->
                        <div id="companies_selection" class="mb-4" style="display: none;">
                            <label class="form-label fw-bold">Select Companies <span class="text-danger">*</span></label>
                            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                <div class="mb-2">
                                    <input type="checkbox" id="select_all_companies" onchange="toggleAllCheckboxes('company_ids[]', this.checked)">
                                    <label for="select_all_companies" class="fw-bold">Select All</label>
                                </div>
                                <hr>
                                @foreach($companies as $company)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="company_ids[]"
                                               value="{{ $company->id }}" id="company_{{ $company->id }}"
                                               {{ in_array($company->id, old('company_ids', [])) ? 'checked' : '' }}
                                               {{ in_array($company->id, $assignedCompanyIds) ? '' : '' }}>
                                        <label class="form-check-label" for="company_{{ $company->id }}">
                                            {{ $company->name }}
                                            @if(in_array($company->id, $assignedCompanyIds))
                                                <span class="badge bg-success small">Assigned</span>
                                            @endif
                                            <span class="text-muted small">({{ $company->users()->count() }} users)</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('company_ids')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Teachers Selection (shown when recipient_type is 'teachers') -->
                        <div id="teachers_selection" class="mb-4" style="display: none;">
                            <label class="form-label fw-bold">Select Teachers <span class="text-danger">*</span></label>
                            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                <div class="mb-2">
                                    <input type="checkbox" id="select_all_teachers" onchange="toggleAllCheckboxes('teacher_ids[]', this.checked)">
                                    <label for="select_all_teachers" class="fw-bold">Select All</label>
                                </div>
                                <hr>
                                @foreach($teachers as $teacher)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="teacher_ids[]"
                                               value="{{ $teacher->id }}" id="teacher_{{ $teacher->id }}"
                                               {{ in_array($teacher->id, old('teacher_ids', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="teacher_{{ $teacher->id }}">
                                            {{ $teacher->name }} ({{ $teacher->email }})
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('teacher_ids')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Individual Users Selection (shown when recipient_type is 'individual_users') -->
                        <div id="individual_users_selection" class="mb-4" style="display: none;">
                            <label class="form-label fw-bold">Select Users <span class="text-danger">*</span></label>
                            <div class="mb-3">
                                <input type="text" class="form-control" id="user_search" placeholder="Search users by name or email..." onkeyup="filterUsers()">
                            </div>
                            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                <div class="mb-2">
                                    <input type="checkbox" id="select_all_users" onchange="toggleAllCheckboxes('user_ids[]', this.checked)">
                                    <label for="select_all_users" class="fw-bold">Select All</label>
                                </div>
                                <hr>
                                @foreach($companies as $company)
                                    @php
                                        $companyUsers = $company->users()->orderBy('name')->get();
                                    @endphp
                                    @if($companyUsers->count() > 0)
                                        <div class="mb-3">
                                            <div class="fw-bold text-primary mb-2">{{ $company->name }}</div>
                                            @foreach($companyUsers as $user)
                                                <div class="form-check mb-2 user-item" data-user-name="{{ strtolower($user->name) }}" data-user-email="{{ strtolower($user->email) }}">
                                                    <input class="form-check-input" type="checkbox" name="user_ids[]"
                                                           value="{{ $user->id }}" id="user_{{ $user->id }}"
                                                           {{ in_array($user->id, old('user_ids', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="user_{{ $user->id }}">
                                                        {{ $user->name }} ({{ $user->email }})
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            @error('user_ids')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Temporary Password Option -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="generate_temp_password"
                                       value="1" id="generate_temp_password"
                                       {{ old('generate_temp_password') ? 'checked' : '' }}>
                                <label class="form-check-label" for="generate_temp_password">
                                    Generate temporary passwords for recipients
                                </label>
                                <div class="form-text">
                                    <i class="bi bi-info-circle"></i> If checked, a temporary password will be generated and sent via email. Users will be required to change it on first login.
                                </div>
                            </div>
                        </div>

                        <!-- Optional Message -->
                        <div class="mb-4">
                            <label for="message" class="form-label">Additional Message (Optional)</label>
                            <textarea class="form-control" id="message" name="message" rows="3"
                                      placeholder="Add a custom message to include in the invitation email...">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('courses.show', $course) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i> Send Invitations
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Course Information</h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold">{{ $course->title }}</h6>
                    <p class="text-muted small mb-2">{{ $course->course_code }}</p>

                    @if($course->description)
                        <p class="mb-3">{{ Str::limit($course->description, 150) }}</p>
                    @endif

                    <hr>

                    <div class="mb-2">
                        <strong>Category:</strong> {{ \App\Models\Course::getCategories()[$course->category] ?? $course->category }}
                    </div>
                    <div class="mb-2">
                        <strong>Level:</strong> {{ ucfirst($course->level) }}
                    </div>
                    <div class="mb-2">
                        <strong>Duration:</strong> {{ $course->duration_hours }} hours
                    </div>
                    @if($course->teacher)
                        <div class="mb-2">
                            <strong>Teacher:</strong> {{ $course->teacher->name }}
                        </div>
                    @endif

                    @if($course->start_date && $course->end_date)
                        <hr>
                        <div class="mb-2">
                            <strong>Start:</strong> {{ $course->start_date->format('M d, Y') }}
                            @if($course->start_time)
                                at {{ \Carbon\Carbon::parse($course->start_time)->format('g:i A') }}
                            @endif
                        </div>
                        <div class="mb-2">
                            <strong>End:</strong> {{ $course->end_date->format('M d, Y') }}
                            @if($course->end_time)
                                at {{ \Carbon\Carbon::parse($course->end_time)->format('g:i A') }}
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Help</h5>
                </div>
                <div class="card-body">
                    <p class="small mb-2"><strong>Companies:</strong> Sends invitations to all users associated with the selected companies.</p>
                    <p class="small mb-2"><strong>Teachers:</strong> Sends invitations to selected teacher accounts.</p>
                    <p class="small mb-2"><strong>Individual Users:</strong> Sends invitations to specific users you select.</p>
                    <hr>
                    <p class="small mb-0"><i class="bi bi-lightbulb"></i> <strong>Tip:</strong> Use temporary passwords for new users who haven't set up their accounts yet.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateRecipientFields() {
    const recipientType = document.querySelector('input[name="recipient_type"]:checked')?.value;

    // Hide all selection fields
    document.getElementById('companies_selection').style.display = 'none';
    document.getElementById('teachers_selection').style.display = 'none';
    document.getElementById('individual_users_selection').style.display = 'none';

    // Show the selected field
    if (recipientType === 'companies') {
        document.getElementById('companies_selection').style.display = 'block';
    } else if (recipientType === 'teachers') {
        document.getElementById('teachers_selection').style.display = 'block';
    } else if (recipientType === 'individual_users') {
        document.getElementById('individual_users_selection').style.display = 'block';
    }
}

function toggleAllCheckboxes(name, checked) {
    const checkboxes = document.querySelectorAll(`input[name="${name}"]`);
    checkboxes.forEach(checkbox => {
        if (checkbox.closest('.user-item')?.style.display !== 'none') {
            checkbox.checked = checked;
        }
    });
}

function filterUsers() {
    const searchTerm = document.getElementById('user_search').value.toLowerCase();
    const userItems = document.querySelectorAll('.user-item');

    userItems.forEach(item => {
        const userName = item.getAttribute('data-user-name');
        const userEmail = item.getAttribute('data-user-email');

        if (userName.includes(searchTerm) || userEmail.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateRecipientFields();
});
</script>
@endsection
