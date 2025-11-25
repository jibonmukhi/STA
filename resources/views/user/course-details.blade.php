@extends('layouts.advanced-dashboard')

@section('page-title', $course->title)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>{{ $course->title }}</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('user.my-courses') }}">I Miei Corsi Iscritti</a></li>
                            <li class="breadcrumb-item active">{{ $course->title }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('user.my-courses') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Torna ai Corsi
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Course Details -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-book"></i> Dettagli Corso</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Codice Corso:</strong>
                        </div>
                        <div class="col-sm-9">
                            <span class="badge bg-secondary" style="font-size: 0.95rem;">{{ $course->course_code }}</span>
                        </div>
                    </div>

                    @if($course->description)
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Descrizione:</strong>
                            </div>
                            <div class="col-sm-9">
                                <p class="mb-0">{{ $course->description }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Categoria:</strong>
                        </div>
                        <div class="col-sm-9">
                            @php
                                $categoryColor = dataVaultColor('course_category', $course->category) ?? 'info';
                                $categoryLabel = dataVaultLabel('course_category', $course->category) ?? $course->category;
                            @endphp
                            <span class="badge bg-{{ $categoryColor }}">{{ $categoryLabel }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Modalità:</strong>
                        </div>
                        <div class="col-sm-9">
                            @php
                                $deliveryLabel = match($course->delivery_method) {
                                    'online' => 'Online',
                                    'offline' => 'In Presenza',
                                    'hybrid' => 'Ibrido',
                                    default => ucfirst($course->delivery_method)
                                };
                            @endphp
                            <span class="badge bg-secondary">{{ $deliveryLabel }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Stato:</strong>
                        </div>
                        <div class="col-sm-9">
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

                    @if($course->teachers->count() > 0)
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Docente{{ $course->teachers->count() > 1 ? 'i' : '' }}:</strong>
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
                                <strong>Azienda:</strong>
                            </div>
                            <div class="col-sm-9">
                                @php
                                    $assignedCompany = $course->assignedCompanies->first();
                                @endphp
                                <span class="badge bg-info" style="font-size: 0.875rem; padding: 0.5rem 0.75rem;">
                                    <i class="fas fa-building me-1"></i>{{ $assignedCompany->name }}
                                    @if($assignedCompany->pivot->is_mandatory)
                                        <span class="badge bg-warning ms-1">Obbligatorio</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endif

                    @if($course->start_date || $course->end_date)
                        <hr class="my-4">
                        <h6 class="mb-3"><i class="fas fa-clock"></i> Calendario Corso</h6>

                        @if($course->start_date)
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong>Data Inizio:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $course->start_date->format('d/m/Y') }}
                                    @if($course->start_time)
                                        alle {{ \Carbon\Carbon::parse($course->start_time)->format('H:i') }}
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($course->end_date)
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong>Data Fine:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $course->end_date->format('d/m/Y') }}
                                    @if($course->end_time)
                                        alle {{ \Carbon\Carbon::parse($course->end_time)->format('H:i') }}
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- My Enrollment Status -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-check"></i> Il Mio Stato di Iscrizione</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="border-end">
                                <h3 class="text-primary mb-1">
                                    @php
                                        $statusLabel = match($enrollment->status) {
                                            'enrolled' => 'Iscritto',
                                            'in_progress' => 'In Corso',
                                            'completed' => 'Completato',
                                            'dropped' => 'Abbandonato',
                                            default => ucfirst($enrollment->status)
                                        };
                                        $statusColor = match($enrollment->status) {
                                            'enrolled' => 'secondary',
                                            'in_progress' => 'warning',
                                            'completed' => 'success',
                                            'dropped' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}">{{ $statusLabel }}</span>
                                </h3>
                                <small class="text-muted">Stato</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border-end">
                                <h3 class="text-info mb-1">{{ number_format($enrollment->progress_percentage ?? 0, 0) }}%</h3>
                                <small class="text-muted">Progresso</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-success mb-1">{{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('d/m/Y') : 'N/A' }}</h3>
                            <small class="text-muted">Data Iscrizione</small>
                        </div>
                    </div>

                    <hr class="my-3">
                    <div class="text-center">
                        <small class="text-muted">Progresso Complessivo</small>
                        <div class="progress mt-2" style="height: 25px;">
                            <div class="progress-bar bg-{{ $enrollment->progress_percentage == 100 ? 'success' : 'info' }}" role="progressbar"
                                 style="width: {{ $enrollment->progress_percentage ?? 0 }}%;"
                                 aria-valuenow="{{ $enrollment->progress_percentage ?? 0 }}" aria-valuemin="0" aria-valuemax="100">
                                <strong>{{ number_format($enrollment->progress_percentage ?? 0, 0) }}%</strong>
                            </div>
                        </div>
                    </div>

                    @if($enrollment->completed_at)
                        <hr class="my-3">
                        <div class="alert alert-success mb-0">
                            <i class="fas fa-check-circle"></i> Corso completato il {{ $enrollment->completed_at->format('d/m/Y') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Class Sessions Section -->
            @if($course->sessions->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt"></i> Sessioni del Corso
                    </h5>
                    <span class="badge bg-white text-primary">{{ $course->sessions->count() }}</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Ore Totali</small>
                        <strong>{{ $course->sessions->sum('duration_hours') }} ore</strong>
                    </div>

                    <div class="list-group session-list">
                        @foreach($course->sessions->take(10) as $session)
                        @php
                            $isCompleted = $session->status === 'completed';
                            $isPast = $session->session_date < now();
                            $isToday = $session->session_date->isToday();
                            $isFuture = $session->session_date > now();
                        @endphp
                        <div class="list-group-item session-item {{ $isToday ? 'session-today' : '' }} {{ $isCompleted ? 'session-completed' : '' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="session-number me-2">{{ $loop->iteration }}</span>
                                        <h6 class="mb-0 session-title">{{ $session->session_title }}</h6>
                                    </div>
                                    <div class="session-details">
                                        <small class="d-flex align-items-center mb-1">
                                            <i class="fas fa-calendar text-primary me-2" style="width: 16px;"></i>
                                            <span class="fw-medium">{{ $session->session_date->format('d/m/Y') }}</span>
                                            @if($isToday)
                                                <span class="badge bg-warning ms-2 pulse">Oggi</span>
                                            @endif
                                        </small>
                                        <small class="d-flex align-items-center mb-1">
                                            <i class="fas fa-clock text-success me-2" style="width: 16px;"></i>
                                            <span>{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</span>
                                        </small>
                                        <small class="d-flex align-items-center">
                                            <i class="fas fa-hourglass-half text-info me-2" style="width: 16px;"></i>
                                            <span>{{ $session->duration_hours }} ore</span>
                                        </small>
                                        @if($session->location)
                                            <small class="d-flex align-items-center mt-1">
                                                <i class="fas fa-map-marker-alt text-danger me-2" style="width: 16px;"></i>
                                                <span>{{ $session->location }}</span>
                                            </small>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-end">
                                    @if($isCompleted)
                                        <span class="badge bg-success mb-2">
                                            <i class="fas fa-check"></i> Completato
                                        </span>
                                    @elseif($isToday)
                                        <span class="badge bg-warning mb-2">
                                            <i class="fas fa-star"></i> Oggi
                                        </span>
                                    @elseif($isPast)
                                        <span class="badge bg-secondary mb-2">
                                            <i class="fas fa-history"></i> Passato
                                        </span>
                                    @else
                                        <span class="badge bg-info mb-2">
                                            <i class="fas fa-clock"></i> Prossimo
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($course->sessions->count() > 10)
                        <div class="text-center mt-3">
                            <small class="text-muted">Mostrate {{ $course->sessions->take(10)->count() }} di {{ $course->sessions->count() }} sessioni</small>
                        </div>
                    @endif
                </div>
            </div>
            @else
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informazioni Corso</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <small class="text-muted d-block">Ore Totali</small>
                            <strong>{{ $course->duration_hours }} ore</strong>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <small class="text-muted d-block">Stato</small>
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

            <!-- Course Materials -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-folder"></i> Materiali del Corso</h5>
                </div>
                <div class="card-body">
                    @if($course->materials->count() > 0)
                        <div class="list-group">
                            @foreach($course->materials as $material)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $material->title }}</h6>
                                        @if($material->description)
                                            <p class="text-muted small mb-2">{{ $material->description }}</p>
                                        @endif
                                        <small class="text-muted d-block">
                                            <span class="badge bg-info">{{ ucfirst($material->material_type) }}</span>
                                            @if($material->file_size_formatted)
                                                {{ $material->file_size_formatted }}
                                            @endif
                                        </small>
                                    </div>
                                    @if($material->is_downloadable)
                                        <a href="{{ route('course-materials.download', $material) }}" class="btn btn-sm btn-success" title="Scarica">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-3 mb-0">Nessun materiale disponibile</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
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

.list-group-item {
    border-left: 3px solid transparent;
    transition: all 0.2s;
}

.list-group-item:hover {
    border-left-color: #667eea;
    background-color: rgba(102, 126, 234, 0.05);
}

/* Session List Enhancements */
.session-list .session-item {
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
    padding: 1rem;
    margin-bottom: 0.5rem;
    border-radius: 0.375rem;
}

.session-list .session-item:hover {
    border-left-color: #667eea;
    background-color: rgba(102, 126, 234, 0.05);
    transform: translateX(5px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.session-today {
    border-left-color: #ffc107 !important;
    background-color: rgba(255, 193, 7, 0.1);
    box-shadow: 0 2px 8px rgba(255, 193, 7, 0.2);
}

.session-today:hover {
    background-color: rgba(255, 193, 7, 0.15);
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
}

.session-completed {
    opacity: 0.75;
}

.session-completed .session-title {
    text-decoration: line-through;
    color: #6c757d;
}

.session-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: bold;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.session-title {
    font-size: 1rem;
    font-weight: 600;
    color: #2d3748;
}

.session-details {
    margin-left: 38px;
}

.session-details small {
    color: #718096;
}

.session-details .fw-medium {
    font-weight: 500;
    color: #2d3748;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.7;
        transform: scale(1.05);
    }
}

.pulse {
    animation: pulse 2s infinite ease-in-out;
}
</style>
@endpush
