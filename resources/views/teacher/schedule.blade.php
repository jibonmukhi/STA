@extends('layouts.advanced-dashboard')

@section('page-title', __('teacher.my_schedule'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Calendar Header -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1">{{ __('teacher.my_schedule') }}</h4>
                            <p class="text-muted mb-0">{{ __('teacher.schedule_description') }}</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary" onclick="goToPreviousMonth()">
                                <i class="fas fa-chevron-left"></i> {{ trans('calendar.previous') }}
                            </button>
                            <button class="btn btn-primary" onclick="goToToday()">{{ trans('calendar.today') }}</button>
                            <button class="btn btn-outline-primary" onclick="goToNextMonth()">
                                {{ trans('calendar.next') }} <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Stats -->
        <div class="col-12 mb-3">
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h3 class="mb-0">{{ $stats['total_events'] }}</h3>
                                    <p class="mb-0">{{ trans('calendar.events_this_month') }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-calendar-alt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h3 class="mb-0">{{ $stats['todays_events'] }}</h3>
                                    <p class="mb-0">{{ trans('calendar.todays_sessions') }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h3 class="mb-0">{{ $stats['upcoming_events'] }}</h3>
                                    <p class="mb-0">{{ trans('calendar.upcoming_sessions') }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-arrow-right fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h3 class="mb-0">{{ $stats['completed_events'] }}</h3>
                                    <p class="mb-0">{{ trans('calendar.completed_sessions') }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Calendar -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" id="currentMonth"></h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0" id="calendar">
                            <thead>
                                <tr class="bg-light">
                                    @foreach($dayNames as $day)
                                        <th class="text-center py-3">{{ $day }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody id="calendar-body">
                                <!-- Calendar will be generated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            <!-- Mini Calendar -->
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">{{ trans('calendar.mini_calendar') }}</h6>
                </div>
                <div class="card-body">
                    <div id="mini-calendar"></div>
                </div>
            </div>

            <!-- Today's Sessions -->
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">{{ __('teacher.today') }}'s {{ __('teacher.class_sessions') }}</h6>
                </div>
                <div class="card-body">
                    @if($todaysEvents->count() > 0)
                        <div class="session-list">
                            @foreach($todaysEvents as $index => $session)
                                @php
                                    $course = $session->course;
                                    $categoryColor = dataVaultColor('course_category', $course->category) ?? 'info';
                                    $isCompleted = $session->status === 'completed';
                                @endphp
                                <a href="{{ route('teacher.session-attendance-detail', $session) }}" class="list-group-item list-group-item-action session-item session-today-sidebar {{ $isCompleted ? 'session-completed' : '' }}" style="text-decoration: none; color: inherit;">
                                    <div class="d-flex align-items-start">
                                        <span class="session-number-small me-2">{{ $index + 1 }}</span>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 session-title-small">{{ $session->session_title }}</h6>
                                            <p class="mb-1 text-muted small">{{ $course->title }}</p>
                                            <p class="mb-0 text-muted small">
                                                <i class="fas fa-clock text-primary"></i> {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                                <span class="ms-2">
                                                    <i class="fas fa-hourglass-half text-success"></i> {{ $session->duration_hours }}{{ __('teacher.hours') }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            @if($isCompleted)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check"></i> {{ __('teacher.completed') }}
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-star"></i> {{ __('teacher.today') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small mb-0">{{ __('teacher.no_upcoming_events') }}</p>
                    @endif
                </div>
            </div>

            <!-- Upcoming Sessions -->
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0">{{ __('teacher.upcoming') }} {{ __('teacher.class_sessions') }}</h6>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @if($upcomingEvents->count() > 0)
                        <div class="session-list">
                            @foreach($upcomingEvents as $index => $session)
                                @php
                                    $course = $session->course;
                                    $categoryColor = dataVaultColor('course_category', $course->category) ?? 'info';
                                    $isCompleted = $session->status === 'completed';
                                    $isToday = $session->session_date->isToday();
                                    $isPast = $session->session_date < now();
                                @endphp
                                <a href="{{ route('teacher.session-attendance-detail', $session) }}" class="list-group-item list-group-item-action session-item {{ $isToday ? 'session-today-sidebar' : '' }} {{ $isCompleted ? 'session-completed' : '' }}" style="text-decoration: none; color: inherit;">
                                    <div class="d-flex align-items-start">
                                        <span class="session-number-small me-2">{{ $index + 1 }}</span>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 session-title-small">{{ $session->session_title }}</h6>
                                            <p class="mb-1 text-muted small">{{ $course->title }}</p>
                                            <p class="mb-0 text-muted small">
                                                <i class="fas fa-calendar text-info"></i> {{ $session->session_date->format('d/m/Y') }}
                                                <span class="ms-2">
                                                    <i class="fas fa-clock text-primary"></i> {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            @if($isCompleted)
                                                <span class="badge bg-success mb-1">
                                                    <i class="fas fa-check"></i> {{ __('teacher.completed') }}
                                                </span>
                                            @elseif($isToday)
                                                <span class="badge bg-warning mb-1">
                                                    <i class="fas fa-star"></i> {{ __('teacher.today') }}
                                                </span>
                                            @else
                                                <span class="badge bg-info mb-1">
                                                    <i class="fas fa-arrow-right"></i> {{ __('teacher.upcoming') }}
                                                </span>
                                            @endif
                                            <div>
                                                <span class="badge bg-{{ $categoryColor }} small">{{ dataVaultLabel('course_category', $course->category) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small mb-0">{{ __('teacher.no_upcoming_events') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>



<style>
    .calendar-day {
        height: 120px;
        vertical-align: top;
        position: relative;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .calendar-day:hover {
        background-color: rgba(79, 70, 229, 0.05) !important;
    }

    .calendar-day.today {
        background-color: rgba(79, 70, 229, 0.1) !important;
    }

    .calendar-day.other-month {
        color: #ccc;
        background-color: #f8f9fa;
    }

    .day-number {
        font-weight: bold;
        margin-bottom: 5px;
    }

    .event-item {
        border-left: 3px solid;
        font-size: 0.75rem;
        padding: 2px 5px;
        margin-bottom: 2px;
        border-radius: 2px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .event-item:hover {
        transform: translateX(2px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .event-item.course {
        border-color: #007bff;
        background-color: rgba(0, 123, 255, 0.1);
    }

    /* Session Status Colors */
    .event-status-completed {
        border-color: #28a745 !important;
        background-color: rgba(40, 167, 69, 0.15) !important;
        opacity: 0.85;
    }

    .event-status-completed:hover {
        background-color: rgba(40, 167, 69, 0.25) !important;
    }

    .event-status-processing {
        border-color: #ffc107 !important;
        background-color: rgba(255, 193, 7, 0.15) !important;
    }

    .event-status-processing:hover {
        background-color: rgba(255, 193, 7, 0.25) !important;
    }

    .event-status-default {
        border-color: #007bff !important;
        background-color: rgba(0, 123, 255, 0.1) !important;
    }

    .mini-calendar {
        font-size: 0.75rem;
    }

    .mini-calendar table {
        width: 100%;
    }

    .mini-calendar td {
        text-align: center;
        padding: 5px;
        cursor: pointer;
        border-radius: 4px;
    }

    .mini-calendar td:hover {
        background-color: rgba(79, 70, 229, 0.1);
    }

    .mini-calendar .today {
        background-color: rgba(79, 70, 229, 0.2);
        font-weight: bold;
    }

    /* Session List Enhancements */
    .session-list .session-item {
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        border-radius: 0.375rem;
        cursor: pointer;
        border: 1px solid #e9ecef;
    }

    .session-list .session-item:hover {
        border-left-color: #667eea;
        background-color: rgba(102, 126, 234, 0.05);
        transform: translateX(3px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .session-today-sidebar {
        border-left-color: #ffc107 !important;
        background-color: rgba(255, 193, 7, 0.1);
        animation: pulse-subtle 2s infinite ease-in-out;
    }

    .session-completed {
        opacity: 0.8;
        background-color: rgba(40, 167, 69, 0.05);
        border-left-color: #28a745 !important;
    }

    .session-number-small {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: bold;
        font-size: 0.75rem;
        flex-shrink: 0;
    }

    .session-title-small {
        font-size: 0.875rem;
        font-weight: 600;
        color: #495057;
    }

    @keyframes pulse-subtle {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.85;
            transform: scale(1.01);
        }
    }

    .session-list .session-item:last-child {
        margin-bottom: 0;
    }
</style>

<script>
    const monthNames = @json($monthNames);
    const dayNames = @json($dayNames);
    const dayNamesShort = @json($dayNamesShort);

    let currentDate = new Date({{ $currentYear }}, {{ $currentMonth - 1 }}, 1);
    let currentView = 'month';
    let courseEvents = @json($formattedEvents);

    function generateCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        // Update month display using localized names
        document.getElementById('currentMonth').textContent =
            monthNames[month] + ' ' + year;

        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - firstDay.getDay());

        const tbody = document.getElementById('calendar-body');
        tbody.innerHTML = '';

        for (let week = 0; week < 6; week++) {
            const row = document.createElement('tr');

            for (let day = 0; day < 7; day++) {
                const cellDate = new Date(startDate);
                cellDate.setDate(startDate.getDate() + (week * 7 + day));

                const cell = document.createElement('td');
                cell.className = 'calendar-day p-2';

                if (cellDate.getMonth() !== month) {
                    cell.classList.add('other-month');
                }

                if (isToday(cellDate)) {
                    cell.classList.add('today');
                }

                const dayNumber = document.createElement('div');
                dayNumber.className = 'day-number';
                dayNumber.textContent = cellDate.getDate();
                cell.appendChild(dayNumber);

                // Add course events for this date
                const dayEvents = courseEvents.filter(event =>
                    event.date === cellDate.toISOString().split('T')[0]
                );

                dayEvents.forEach(event => {
                    const eventEl = document.createElement('div');
                    const sessionStatus = event.sessionStatus || event.status;

                    // Add status-specific classes
                    let statusClass = 'event-status-default';
                    if (sessionStatus === 'completed') {
                        statusClass = 'event-status-completed';
                    } else if (sessionStatus === 'in_progress' || sessionStatus === 'scheduled') {
                        statusClass = 'event-status-processing';
                    }

                    eventEl.className = `event-item ${event.eventType} ${statusClass}`;

                    // Show both course title and session title
                    const displayText = `${event.courseTitle} - ${event.sessionTitle}`;
                    eventEl.textContent = displayText;
                    eventEl.style.cursor = 'pointer';

                    // Add comprehensive title attribute for hover tooltip
                    const tooltipContent = `${event.courseTitle}\nSession: ${event.sessionTitle}\nCompany: ${event.companyNames}\nTeacher: ${event.instructor}\nTime: ${event.startTime} - ${event.endTime}\nStatus: ${sessionStatus ? sessionStatus.charAt(0).toUpperCase() + sessionStatus.slice(1) : 'N/A'}`;
                    eventEl.title = tooltipContent;

                    cell.appendChild(eventEl);
                });

                row.appendChild(cell);
            }

            tbody.appendChild(row);
        }

        generateMiniCalendar();
    }

    function generateMiniCalendar() {
        const miniCalendar = document.getElementById('mini-calendar');
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        const firstDay = new Date(year, month, 1);
        const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - firstDay.getDay());

        let html = '<table class="table table-sm mb-0"><tbody>';

        for (let week = 0; week < 6; week++) {
            html += '<tr>';
            for (let day = 0; day < 7; day++) {
                const cellDate = new Date(startDate);
                cellDate.setDate(startDate.getDate() + (week * 7 + day));

                let classes = '';
                if (cellDate.getMonth() !== month) classes += 'text-muted ';
                if (isToday(cellDate)) classes += 'today ';

                html += `<td class="${classes}" onclick="goToDate('${cellDate.toISOString()}')">${cellDate.getDate()}</td>`;
            }
            html += '</tr>';
        }

        html += '</tbody></table>';
        miniCalendar.innerHTML = html;
    }

    function isToday(date) {
        const today = new Date();
        return date.toDateString() === today.toDateString();
    }

    function goToPreviousMonth() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        window.location.href = `?month=${currentDate.getMonth() + 1}&year=${currentDate.getFullYear()}`;
    }

    function goToNextMonth() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        window.location.href = `?month=${currentDate.getMonth() + 1}&year=${currentDate.getFullYear()}`;
    }

    function goToToday() {
        const today = new Date();
        window.location.href = `?month=${today.getMonth() + 1}&year=${today.getFullYear()}`;
    }

    function goToDate(dateString) {
        const date = new Date(dateString);
        window.location.href = `?month=${date.getMonth() + 1}&year=${date.getFullYear()}`;
    }


    function getStatusColor(status) {
        const colors = {
            'scheduled': 'primary',
            'in_progress': 'warning',
            'completed': 'success',
            'cancelled': 'danger',
            'postponed': 'secondary'
        };
        return colors[status] || 'primary';
    }

    // Initialize calendar on page load
    document.addEventListener('DOMContentLoaded', function() {
        generateCalendar();
    });

</script>
@endsection
