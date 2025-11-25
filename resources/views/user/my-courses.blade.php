@extends('layouts.advanced-dashboard')

@section('page-title', __('navigation.my_enrolled_courses'))

@section('content')
<div class="container-fluid">
    {{-- Header Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>{{ __('navigation.my_enrolled_courses') }}</h2>
                    <p class="text-muted">Corsi assegnati a te</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('user.my-courses') }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Cerca</label>
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                                       placeholder="Cerca corsi...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Categoria</label>
                                <select class="form-select" name="category">
                                    <option value="">Tutte le Categorie</option>
                                    @foreach($categories as $key => $value)
                                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Stato</label>
                                <select class="form-select" name="status">
                                    <option value="">Tutti gli Stati</option>
                                    <option value="enrolled" {{ request('status') == 'enrolled' ? 'selected' : '' }}>Iscritto</option>
                                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Corso</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completato</option>
                                    <option value="dropped" {{ request('status') == 'dropped' ? 'selected' : '' }}>Abbandonato</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-3 mt-0">
                            <div class="col-md-12 d-flex justify-content-end">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Filtra
                                    </button>
                                    <a href="{{ route('user.my-courses') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Cancella Filtri
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Courses Table --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    @if($enrollments->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-book fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">Nessun Corso Trovato</h4>
                            <p class="text-muted">Non sei ancora iscritto a nessun corso.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Codice Corso</th>
                                        <th>Titolo</th>
                                        <th>Categoria</th>
                                        <th>Azienda</th>
                                        <th>Data Inizio</th>
                                        <th>Data Fine</th>
                                        <th>Ore</th>
                                        <th>Modalità</th>
                                        <th>Progresso</th>
                                        <th>Stato</th>
                                        <th class="text-end">Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrollments as $enrollment)
                                        @php
                                            $course = $enrollment->course;
                                            $categoryColor = dataVaultColor('course_category', $course->category) ?? 'info';
                                            $categoryLabel = dataVaultLabel('course_category', $course->category) ?? $course->category;

                                            $statusColor = match($enrollment->status) {
                                                'enrolled' => 'secondary',
                                                'in_progress' => 'warning',
                                                'completed' => 'success',
                                                'dropped' => 'danger',
                                                default => 'secondary'
                                            };
                                            $statusLabel = match($enrollment->status) {
                                                'enrolled' => 'Iscritto',
                                                'in_progress' => 'In Corso',
                                                'completed' => 'Completato',
                                                'dropped' => 'Abbandonato',
                                                default => ucfirst($enrollment->status)
                                            };
                                            $progressPercentage = $enrollment->progress_percentage ?? 0;
                                        @endphp
                                        <tr class="course-row">
                                            <td>
                                                <strong class="text-primary">{{ $course->course_code }}</strong>
                                            </td>
                                            <td>
                                                <strong>{{ $course->title }}</strong>
                                                @if($course->is_mandatory)
                                                    <span class="badge bg-warning ms-1">Obbligatorio</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $categoryColor }}">
                                                    {{ $categoryLabel }}
                                                </span>
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
                                                    @if($course->delivery_method == 'online')
                                                        Online
                                                    @elseif($course->delivery_method == 'offline')
                                                        In Presenza
                                                    @else
                                                        Ibrido
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2" style="height: 8px; min-width: 60px;">
                                                        <div class="progress-bar bg-{{ $progressPercentage == 100 ? 'success' : 'primary' }}"
                                                             role="progressbar"
                                                             style="width: {{ $progressPercentage }}%;"
                                                             aria-valuenow="{{ $progressPercentage }}"
                                                             aria-valuemin="0"
                                                             aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted text-nowrap">{{ number_format($progressPercentage, 0) }}%</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $statusColor }}">
                                                    {{ $statusLabel }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('user.course-details', $course) }}" class="btn btn-sm btn-outline-info" title="Vedi Dettagli">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
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
                                        <label class="me-2 mb-0 text-nowrap">Righe per pagina:</label>
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
                                        Mostrando {{ $enrollments->firstItem() ?? 0 }} a {{ $enrollments->lastItem() ?? 0 }}
                                        di {{ $enrollments->total() }} corsi
                                    </small>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex justify-content-end">
                                        {{ $enrollments->withQueryString()->links('pagination::bootstrap-4') }}
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
    padding: 0.75rem 0.5rem;
}

.table tbody td {
    vertical-align: middle;
    padding: 0.75rem 0.5rem;
    font-size: 0.9rem;
}

.table .badge {
    font-size: 0.75rem;
    padding: 0.25em 0.5em;
}

.pagination {
    margin-bottom: 0;
}

.progress {
    border-radius: 10px;
    background-color: #e9ecef;
}

.progress-bar {
    border-radius: 10px;
}
</style>

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
});
</script>
@endsection
