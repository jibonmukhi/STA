@extends('layouts.advanced-dashboard')

@section('page-title', __('navigation.calendar'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Calendar Header -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1">{{ __('navigation.calendar') }}</h4>
                            <p class="text-muted mb-0">{{ __('calendar.view_all_course_sessions') }}</p>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <!-- View Mode Selector -->
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary" id="view-day" onclick="changeView('day')">
                                    <i class="fas fa-calendar-day"></i> Day
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="view-week" onclick="changeView('week')">
                                    <i class="fas fa-calendar-week"></i> Week
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="view-month" onclick="changeView('month')">
                                    <i class="fas fa-calendar-alt"></i> Month
                                </button>
                            </div>
                            <!-- Navigation -->
                            <button class="btn btn-outline-primary" onclick="goToPrevious()">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="btn btn-primary" onclick="goToToday()">{{ trans('calendar.today') }}</button>
                            <button class="btn btn-outline-primary" onclick="goToNext()">
                                <i class="fas fa-chevron-right"></i>
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
                    <!-- Month View -->
                    <div id="month-view" class="table-responsive" style="display: none;">
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

                    <!-- Week View -->
                    <div id="week-view" style="display: none;">
                        <div class="week-calendar-container">
                            <div class="week-time-column">
                                <div class="week-time-header"></div>
                                <div class="week-time-slots" id="week-time-slots">
                                    <!-- Time slots will be generated by JavaScript -->
                                </div>
                            </div>
                            <div class="week-days-container" id="week-days-container">
                                <!-- Days will be generated by JavaScript -->
                            </div>
                        </div>
                    </div>

                    <!-- Day View -->
                    <div id="day-view" style="display: block;">
                        <div class="day-calendar-container">
                            <div class="day-time-column">
                                <div class="day-time-slots" id="day-time-slots">
                                    <!-- Time slots will be generated by JavaScript -->
                                </div>
                            </div>
                            <div class="day-events-column" id="day-events-column">
                                <!-- Events will be generated by JavaScript -->
                            </div>
                        </div>
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
                    <h6 class="mb-0">{{ __('calendar.todays_sessions') }}</h6>
                </div>
                <div class="card-body">
                    @if($todaysEvents->count() > 0)
                        <div class="session-list">
                            @foreach($todaysEvents as $index => $session)
                                @php
                                    $course = $session->course;
                                    $courseColor = $course->color ?? 'info';
                                    $isCompleted = $session->status === 'completed';
                                @endphp
                                <div class="list-group-item list-group-item-action session-item session-today-sidebar {{ $isCompleted ? 'session-completed' : '' }}">
                                    <div class="d-flex align-items-start" style="width: 100%;">
                                        <span class="session-number-small me-2 flex-shrink-0">{{ $index + 1 }}</span>
                                        <div class="flex-grow-1" style="min-width: 0; max-width: 100%; overflow: hidden;">
                                            <div class="mb-1" style="width: 100%;">
                                                <div class="d-flex flex-wrap gap-1" style="width: 100%;">
                                                    <span class="badge bg-{{ $courseColor }}" style="max-width: 100%;">{{ $course->title }}</span>
                                                    <span class="badge bg-info" style="max-width: 100%;">{{ $session->session_title }}</span>
                                                    @if($course->assignedCompanies && $course->assignedCompanies->isNotEmpty())
                                                        <span class="badge bg-secondary" style="max-width: 100%;">{{ $course->assignedCompanies->pluck('name')->join(', ') }}</span>
                                                    @endif
                                                    @if($isCompleted)
                                                        <span class="badge bg-success" style="max-width: 100%;">
                                                            <i class="fas fa-check"></i> {{ __('calendar.completed') }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning" style="max-width: 100%;">
                                                            <i class="fas fa-star"></i> {{ __('calendar.today') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <p class="mb-0 text-muted small" style="width: 100%; overflow: hidden;">
                                                <i class="fas fa-clock text-primary"></i> {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                                <span class="d-inline-block ms-2">
                                                    <i class="fas fa-hourglass-half text-success"></i> {{ $session->duration_hours }}h
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small mb-0">{{ __('calendar.no_sessions_today') }}</p>
                    @endif
                </div>
            </div>

            <!-- Upcoming Sessions -->
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0">{{ __('calendar.upcoming_sessions') }}</h6>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @if($upcomingEvents->count() > 0)
                        <div class="session-list">
                            @foreach($upcomingEvents as $index => $session)
                                @php
                                    $course = $session->course;
                                    $courseColor = $course->color ?? 'info';
                                    $categoryColor = dataVaultColor('course_category', $course->category) ?? 'info';
                                    $isCompleted = $session->status === 'completed';
                                    $isToday = $session->session_date->isToday();
                                @endphp
                                <div class="list-group-item list-group-item-action session-item {{ $isToday ? 'session-today-sidebar' : '' }} {{ $isCompleted ? 'session-completed' : '' }}">
                                    <div class="d-flex align-items-start" style="width: 100%;">
                                        <span class="session-number-small me-2 flex-shrink-0">{{ $index + 1 }}</span>
                                        <div class="flex-grow-1" style="min-width: 0; max-width: 100%; overflow: hidden;">
                                            <div class="mb-1" style="width: 100%;">
                                                <div class="d-flex flex-wrap gap-1" style="width: 100%;">
                                                    <span class="badge bg-{{ $courseColor }}" style="max-width: 100%;">{{ $course->title }}</span>
                                                    <span class="badge bg-info" style="max-width: 100%;">{{ $session->session_title }}</span>
                                                    @if($course->assignedCompanies && $course->assignedCompanies->isNotEmpty())
                                                        <span class="badge bg-secondary" style="max-width: 100%;">{{ $course->assignedCompanies->pluck('name')->join(', ') }}</span>
                                                    @endif
                                                    @if($isCompleted)
                                                        <span class="badge bg-success" style="max-width: 100%;">
                                                            <i class="fas fa-check"></i> {{ __('calendar.completed') }}
                                                        </span>
                                                    @elseif($isToday)
                                                        <span class="badge bg-warning" style="max-width: 100%;">
                                                            <i class="fas fa-star"></i> {{ __('calendar.today') }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-info" style="max-width: 100%;">
                                                            <i class="fas fa-arrow-right"></i> {{ __('calendar.upcoming') }}
                                                        </span>
                                                    @endif
                                                    <span class="badge bg-{{ $categoryColor }}" style="max-width: 100%;">{{ dataVaultLabel('course_category', $course->category) }}</span>
                                                </div>
                                            </div>
                                            <p class="mb-0 text-muted small" style="width: 100%; overflow: hidden;">
                                                <i class="fas fa-calendar text-info"></i> {{ $session->session_date->format('d/m/Y') }}
                                                <span class="d-inline-block ms-2">
                                                    <i class="fas fa-clock text-primary"></i> {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small mb-0">{{ __('calendar.no_upcoming_sessions') }}</p>
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
        padding: 4px 6px;
        margin-bottom: 2px;
        border-radius: 2px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        flex-wrap: wrap;
        gap: 2px;
        align-items: center;
    }

    .event-item:hover {
        transform: translateX(2px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .event-item .badge {
        line-height: 1.2;
        padding: 2px 6px;
        white-space: nowrap;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .event-item.course {
        border-color: #007bff;
        background-color: rgba(0, 123, 255, 0.1);
    }

    /* Session Status Colors */
    .event-status-completed {
        border-color: #28a745 !important;
        border-left-width: 5px !important;
        background-color: rgba(40, 167, 69, 0.15) !important;
        opacity: 0.95;
    }

    .event-status-completed:hover {
        background-color: rgba(40, 167, 69, 0.25) !important;
        opacity: 1;
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

    /* Prevent horizontal scroll in sidebar */
    .col-lg-3 .card-body {
        overflow-x: hidden;
    }

    /* Session List Enhancements */
    .session-list {
        overflow-x: hidden;
    }

    .session-list .session-item {
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        border-radius: 0.375rem;
        cursor: pointer;
        border: 1px solid #e9ecef;
        overflow-x: hidden;
        word-wrap: break-word;
    }

    .session-list .session-item:hover {
        border-left-color: #667eea;
        background-color: rgba(102, 126, 234, 0.05);
        transform: translateX(3px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .session-list .session-item .flex-grow-1 {
        min-width: 0;
        overflow-x: hidden;
    }

    .session-list .session-item .badge {
        display: inline-block;
        margin-bottom: 4px;
        white-space: normal;
        line-height: 1.3;
        word-break: break-word;
        max-width: 100%;
        font-size: 0.7rem;
    }

    .session-list .session-item .d-flex.flex-wrap {
        max-width: 100%;
        width: 100%;
    }

    .session-list .session-item p {
        overflow-x: hidden;
        text-overflow: ellipsis;
        white-space: normal;
    }

    .session-today-sidebar {
        border-left-color: #ffc107 !important;
        background-color: rgba(255, 193, 7, 0.1);
        animation: pulse-subtle 2s infinite ease-in-out;
    }

    .session-completed {
        opacity: 0.9;
        background-color: rgba(40, 167, 69, 0.08);
        border-left-color: #28a745 !important;
        border-left-width: 5px !important;
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

    /* Additional Bootstrap color classes */
    .bg-purple {
        background-color: #6f42c1 !important;
    }

    .bg-pink {
        background-color: #d63384 !important;
    }

    .bg-indigo {
        background-color: #6610f2 !important;
    }

    .bg-teal {
        background-color: #20c997 !important;
    }

    .bg-orange {
        background-color: #fd7e14 !important;
    }

    .badge.bg-purple,
    .badge.bg-pink,
    .badge.bg-indigo,
    .badge.bg-teal,
    .badge.bg-orange {
        color: #fff !important;
    }

    /* Day View Styles */
    .day-calendar-container {
        display: flex;
        min-height: 600px;
        background: #fff;
    }

    .day-time-column {
        width: 80px;
        border-right: 1px solid #dee2e6;
        flex-shrink: 0;
    }

    .day-time-slots, .week-time-slots {
        position: relative;
    }

    .time-slot {
        height: 60px;
        padding: 8px 12px;
        font-size: 0.75rem;
        color: #6c757d;
        border-bottom: 1px solid #e9ecef;
        text-align: right;
    }

    .day-events-column {
        flex: 1;
        position: relative;
        border-right: 1px solid #dee2e6;
    }

    .event-slot {
        height: 60px;
        border-bottom: 1px solid #e9ecef;
        position: relative;
        display: flex;
        flex-direction: row;
        gap: 4px;
        align-items: flex-start;
        padding: 2px;
    }

    .day-events-column .event-slot {
        display: flex;
        flex-direction: row;
        gap: 4px;
        align-items: flex-start;
        padding: 2px;
    }

    .calendar-event {
        position: absolute;
        width: calc(100% - 8px);
        margin: 0 4px;
        padding: 6px 8px;
        border-radius: 4px;
        border-left: 3px solid;
        background-color: #fff;
        font-size: 0.75rem;
        overflow: visible;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 0.2s ease;
        z-index: 1;
        display: flex;
        flex-wrap: wrap;
        gap: 2px;
        align-items: center;
    }

    .day-events-column .calendar-event {
        position: relative !important;
        flex: 1;
        width: auto !important;
        margin: 0 !important;
        min-height: 54px;
        height: auto !important;
    }

    .calendar-event:hover {
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        transform: translateX(2px);
        z-index: 10;
    }

    .calendar-event.event-status-completed {
        border-left-color: #28a745;
        background-color: rgba(40, 167, 69, 0.05);
    }

    .calendar-event.event-status-processing {
        border-left-color: #ffc107;
        background-color: rgba(255, 193, 7, 0.05);
    }

    .calendar-event.event-status-default {
        border-left-color: #667eea;
    }

    .event-time-info {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .event-time-info i {
        font-size: 0.7rem;
    }

    .calendar-event .badge {
        white-space: normal;
        line-height: 1.3;
        display: inline-block;
    }

    /* Week View Styles */
    .week-calendar-container {
        display: flex;
        min-height: 600px;
        background: #fff;
    }

    .week-time-column {
        width: 70px;
        border-right: 1px solid #dee2e6;
        flex-shrink: 0;
    }

    .week-time-header {
        height: 60px;
        border-bottom: 2px solid #dee2e6;
    }

    .week-days-container {
        flex: 1;
        display: flex;
    }

    .week-day-column {
        flex: 1;
        border-right: 1px solid #dee2e6;
        min-width: 0;
    }

    .week-day-column:last-child {
        border-right: none;
    }

    .week-day-header {
        height: 60px;
        padding: 8px;
        text-align: center;
        border-bottom: 2px solid #dee2e6;
        background: #f8f9fa;
    }

    .week-day-header.today {
        background: rgba(79, 70, 229, 0.1);
    }

    .day-name {
        font-size: 0.7rem;
        color: #6c757d;
        text-transform: uppercase;
        font-weight: 600;
    }

    .day-number {
        font-size: 1.25rem;
        font-weight: 700;
        color: #2d3748;
        margin-top: 4px;
    }

    .week-day-header.today .day-number {
        color: #667eea;
    }

    .week-day-events {
        position: relative;
    }

    .week-day-events .event-slot {
        padding: 2px;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .week-day-events .calendar-event {
        font-size: 0.6rem;
        padding: 4px 5px;
        position: relative !important;
        width: 100%;
        margin: 0 !important;
        min-height: auto;
        height: auto !important;
        margin-top: 0 !important;
    }

    .week-day-events .calendar-event .badge {
        font-size: 0.55rem !important;
        padding: 2px 4px;
        white-space: normal !important;
        line-height: 1.3;
        display: inline-block;
        max-width: 100%;
    }

    .week-day-events .event-time-info {
        font-size: 0.6rem !important;
        margin-top: 2px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .day-time-column, .week-time-column {
            width: 60px;
        }

        .time-slot {
            font-size: 0.65rem;
            padding: 6px 8px;
        }

        .calendar-event {
            font-size: 0.65rem;
            padding: 4px 6px;
        }

        .week-day-column {
            min-width: 80px;
        }
    }
</style>

<script>
    const monthNames = @json($monthNames);
    const dayNames = @json($dayNames);
    const dayNamesShort = @json($dayNamesShort);

    let currentDate = new Date({{ $currentYear }}, {{ $currentMonth - 1 }}, new Date().getDate());
    let currentView = 'day'; // Default to day view
    let courseEvents = @json($formattedEvents);

    // Helper function to convert Date object to local date string (Y-m-d format)
    function getLocalDateString(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

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
                    event.date === getLocalDateString(cellDate)
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
                    eventEl.style.cursor = 'pointer';

                    // Create badges for course, session, and company
                    const courseBadge = document.createElement('span');
                    courseBadge.className = `badge bg-${event.courseColor || 'primary'} me-1 mb-1`;
                    courseBadge.style.fontSize = '0.65rem';
                    courseBadge.textContent = event.courseTitle;

                    const sessionBadge = document.createElement('span');
                    sessionBadge.className = 'badge bg-info me-1 mb-1';
                    sessionBadge.style.fontSize = '0.65rem';
                    sessionBadge.textContent = event.sessionTitle;

                    eventEl.appendChild(courseBadge);
                    eventEl.appendChild(sessionBadge);

                    // Add company badge if company names exist
                    if (event.companyNames && event.companyNames.trim() !== '' && event.companyNames !== 'N/A') {
                        const companyBadge = document.createElement('span');
                        companyBadge.className = 'badge bg-secondary mb-1';
                        companyBadge.style.fontSize = '0.65rem';
                        companyBadge.textContent = event.companyNames;
                        eventEl.appendChild(companyBadge);
                    }

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

    // View switching functions
    function changeView(view) {
        currentView = view;

        // Update button states
        document.getElementById('view-day').classList.remove('active', 'btn-primary');
        document.getElementById('view-week').classList.remove('active', 'btn-primary');
        document.getElementById('view-month').classList.remove('active', 'btn-primary');
        document.getElementById('view-day').classList.add('btn-outline-primary');
        document.getElementById('view-week').classList.add('btn-outline-primary');
        document.getElementById('view-month').classList.add('btn-outline-primary');

        document.getElementById(`view-${view}`).classList.remove('btn-outline-primary');
        document.getElementById(`view-${view}`).classList.add('active', 'btn-primary');

        // Show/hide views
        document.getElementById('day-view').style.display = view === 'day' ? 'block' : 'none';
        document.getElementById('week-view').style.display = view === 'week' ? 'block' : 'none';
        document.getElementById('month-view').style.display = view === 'month' ? 'block' : 'none';

        // Render appropriate view
        if (view === 'day') {
            renderDayView();
        } else if (view === 'week') {
            renderWeekView();
        } else {
            generateCalendar();
        }
    }

    // Day view rendering
    function renderDayView() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        const day = currentDate.getDate();

        // Update header
        document.getElementById('currentMonth').textContent =
            `${dayNames[currentDate.getDay()]}, ${monthNames[month]} ${day}, ${year}`;

        // Generate time slots (6 AM to 10 PM)
        const timeSlots = document.getElementById('day-time-slots');
        const eventsColumn = document.getElementById('day-events-column');

        timeSlots.innerHTML = '';
        eventsColumn.innerHTML = '';

        for (let hour = 6; hour <= 22; hour++) {
            // Time slot
            const timeSlot = document.createElement('div');
            timeSlot.className = 'time-slot';
            timeSlot.textContent = `${hour.toString().padStart(2, '0')}:00`;
            timeSlots.appendChild(timeSlot);

            // Event slot
            const eventSlot = document.createElement('div');
            eventSlot.className = 'event-slot';
            eventSlot.dataset.hour = hour;
            eventsColumn.appendChild(eventSlot);
        }

        // Add events for this day
        const dateString = getLocalDateString(currentDate);
        const dayEvents = courseEvents.filter(event => event.date === dateString);

        dayEvents.forEach(event => {
            const startHour = parseInt(event.startTime.split(':')[0]);

            if (startHour >= 6 && startHour <= 22) {
                const eventSlot = eventsColumn.querySelector(`[data-hour="${startHour}"]`);
                if (eventSlot) {
                    const eventEl = createEventElement(event);
                    eventSlot.appendChild(eventEl);
                }
            }
        });
    }

    // Week view rendering
    function renderWeekView() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        // Get start of week (Sunday)
        const startOfWeek = new Date(currentDate);
        startOfWeek.setDate(currentDate.getDate() - currentDate.getDay());

        // Get end of week
        const endOfWeek = new Date(startOfWeek);
        endOfWeek.setDate(startOfWeek.getDate() + 6);

        // Update header
        document.getElementById('currentMonth').textContent =
            `${monthNames[startOfWeek.getMonth()]} ${startOfWeek.getDate()} - ${monthNames[endOfWeek.getMonth()]} ${endOfWeek.getDate()}, ${year}`;

        // Generate time slots
        const timeSlots = document.getElementById('week-time-slots');
        timeSlots.innerHTML = '';

        for (let hour = 6; hour <= 22; hour++) {
            const timeSlot = document.createElement('div');
            timeSlot.className = 'time-slot';
            timeSlot.textContent = `${hour.toString().padStart(2, '0')}:00`;
            timeSlots.appendChild(timeSlot);
        }

        // Generate days
        const daysContainer = document.getElementById('week-days-container');
        daysContainer.innerHTML = '';

        for (let i = 0; i < 7; i++) {
            const dayDate = new Date(startOfWeek);
            dayDate.setDate(startOfWeek.getDate() + i);

            const dayColumn = document.createElement('div');
            dayColumn.className = 'week-day-column';

            // Day header
            const dayHeader = document.createElement('div');
            dayHeader.className = 'week-day-header';
            if (isToday(dayDate)) {
                dayHeader.classList.add('today');
            }
            dayHeader.innerHTML = `
                <div class="day-name">${dayNamesShort[i]}</div>
                <div class="day-number">${dayDate.getDate()}</div>
            `;
            dayColumn.appendChild(dayHeader);

            // Event slots for this day
            const dayEventsContainer = document.createElement('div');
            dayEventsContainer.className = 'week-day-events';

            for (let hour = 6; hour <= 22; hour++) {
                const eventSlot = document.createElement('div');
                eventSlot.className = 'event-slot';
                eventSlot.dataset.hour = hour;
                dayEventsContainer.appendChild(eventSlot);
            }

            // Add events for this day
            const dateString = getLocalDateString(dayDate);
            const dayEvents = courseEvents.filter(event => event.date === dateString);

            dayEvents.forEach(event => {
                const startHour = parseInt(event.startTime.split(':')[0]);

                if (startHour >= 6 && startHour <= 22) {
                    const eventSlot = dayEventsContainer.querySelector(`[data-hour="${startHour}"]`);
                    if (eventSlot) {
                        const eventEl = createEventElement(event, true);
                        eventSlot.appendChild(eventEl);
                    }
                }
            });

            dayColumn.appendChild(dayEventsContainer);
            daysContainer.appendChild(dayColumn);
        }
    }

    // Create event element helper
    function createEventElement(event, compact = false) {
        const eventEl = document.createElement('div');
        const sessionStatus = event.sessionStatus || event.status;

        let statusClass = 'event-status-default';
        if (sessionStatus === 'completed') {
            statusClass = 'event-status-completed';
        } else if (sessionStatus === 'in_progress' || sessionStatus === 'scheduled') {
            statusClass = 'event-status-processing';
        }

        eventEl.className = `calendar-event ${statusClass}`;
        eventEl.style.cursor = 'pointer';

        // Create badges like month view
        const courseBadge = document.createElement('span');
        courseBadge.className = `badge bg-${event.courseColor || 'primary'} me-1 mb-1`;
        courseBadge.style.fontSize = compact ? '0.6rem' : '0.7rem';
        courseBadge.textContent = event.courseTitle;

        const sessionBadge = document.createElement('span');
        sessionBadge.className = 'badge bg-info me-1 mb-1';
        sessionBadge.style.fontSize = compact ? '0.6rem' : '0.7rem';
        sessionBadge.textContent = event.sessionTitle;

        eventEl.appendChild(courseBadge);
        eventEl.appendChild(sessionBadge);

        // Add company badge if exists
        if (event.companyNames && event.companyNames.trim() !== '' && event.companyNames !== 'N/A') {
            const companyBadge = document.createElement('span');
            companyBadge.className = 'badge bg-secondary mb-1';
            companyBadge.style.fontSize = compact ? '0.6rem' : '0.7rem';
            companyBadge.textContent = event.companyNames;
            eventEl.appendChild(companyBadge);
        }

        // Add time info
        const timeDiv = document.createElement('div');
        timeDiv.className = 'event-time-info';
        timeDiv.style.fontSize = compact ? '0.65rem' : '0.75rem';
        timeDiv.style.marginTop = '4px';
        timeDiv.style.color = '#6c757d';
        timeDiv.innerHTML = `<i class="fas fa-clock"></i> ${event.startTime} - ${event.endTime}`;
        eventEl.appendChild(timeDiv);

        eventEl.title = `${event.courseTitle}\nSession: ${event.sessionTitle}\nCompany: ${event.companyNames}\nTime: ${event.startTime} - ${event.endTime}\nStatus: ${sessionStatus}`;

        return eventEl;
    }

    // Navigation functions
    function goToPrevious() {
        if (currentView === 'day') {
            currentDate.setDate(currentDate.getDate() - 1);
            renderDayView();
        } else if (currentView === 'week') {
            currentDate.setDate(currentDate.getDate() - 7);
            renderWeekView();
        } else {
            currentDate.setMonth(currentDate.getMonth() - 1);
            window.location.href = `?month=${currentDate.getMonth() + 1}&year=${currentDate.getFullYear()}`;
        }
    }

    function goToNext() {
        if (currentView === 'day') {
            currentDate.setDate(currentDate.getDate() + 1);
            renderDayView();
        } else if (currentView === 'week') {
            currentDate.setDate(currentDate.getDate() + 7);
            renderWeekView();
        } else {
            currentDate.setMonth(currentDate.getMonth() + 1);
            window.location.href = `?month=${currentDate.getMonth() + 1}&year=${currentDate.getFullYear()}`;
        }
    }

    function goToToday() {
        currentDate = new Date();
        if (currentView === 'day') {
            renderDayView();
        } else if (currentView === 'week') {
            renderWeekView();
        } else {
            window.location.href = `?month=${currentDate.getMonth() + 1}&year=${currentDate.getFullYear()}`;
        }
    }

    function goToDate(dateString) {
        const date = new Date(dateString);
        currentDate = date;
        if (currentView === 'day') {
            renderDayView();
        } else if (currentView === 'week') {
            renderWeekView();
        } else {
            window.location.href = `?month=${date.getMonth() + 1}&year=${date.getFullYear()}`;
        }
    }

    // Initialize calendar on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Set default view to day
        changeView('day');
    });
</script>
@endsection