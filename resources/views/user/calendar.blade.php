@extends('layouts.advanced-dashboard')

@section('page-title', trans('calendar.calendar'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Calendar Header -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1">{{ trans('calendar.calendar') }}</h4>
                            <p class="text-muted mb-0">{{ trans('courses.view_scheduled_courses') }}</p>
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
                                    <p class="mb-0">{{ trans('calendar.events_today') }}</p>
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
                                    <p class="mb-0">{{ trans('calendar.upcoming_events') }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-calendar-check fa-2x"></i>
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
                                    <p class="mb-0">{{ trans('calendar.completed_events') }}</p>
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

        <!-- Calendar View Controls -->
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0" id="currentMonth">{{ now()->setMonth($currentMonth)->format('F Y') }}</h5>
                        </div>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary active" onclick="changeView('month')">Month</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="changeView('week')">Week</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="changeView('day')">Day</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Calendar -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body p-0">
                    <!-- Calendar Grid -->
                    <div id="calendar-container">
                        <table class="table table-bordered mb-0" id="calendar-table">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th class="text-center py-3">{{ trans('calendar.days.sunday') }}</th>
                                    <th class="text-center py-3">{{ trans('calendar.days.monday') }}</th>
                                    <th class="text-center py-3">{{ trans('calendar.days.tuesday') }}</th>
                                    <th class="text-center py-3">{{ trans('calendar.days.wednesday') }}</th>
                                    <th class="text-center py-3">{{ trans('calendar.days.thursday') }}</th>
                                    <th class="text-center py-3">{{ trans('calendar.days.friday') }}</th>
                                    <th class="text-center py-3">{{ trans('calendar.days.saturday') }}</th>
                                </tr>
                            </thead>
                            <tbody id="calendar-body">
                                <!-- Calendar cells will be generated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar - Events & Quick Actions -->
        <div class="col-lg-3">
            <!-- Mini Calendar -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">Mini Calendar</h6>
                </div>
                <div class="card-body p-2">
                    <div id="mini-calendar" class="mini-calendar">
                        <!-- Mini calendar will be generated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Today's Events -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">{{ trans('calendar.todays_events') }}</h6>
                </div>
                <div class="card-body">
                    <div id="todays-events">
                        @forelse($todaysEvents as $event)
                            <div class="event-item mb-2 p-2 border-left border-{{ $event->event_type_color }} bg-light">
                                <div class="fw-bold text-sm">{{ $event->title }}</div>
                                <div class="text-muted small">
                                    {{ $event->formatted_time }}
                                    @if($event->is_online)
                                        <span class="badge bg-info ms-1">Online</span>
                                    @endif
                                    @if($event->is_mandatory)
                                        <span class="badge bg-danger ms-1">Mandatory</span>
                                    @endif
                                </div>
                                @if($event->location)
                                    <div class="text-muted small">
                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $event->location }}
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center mt-3">
                                <small class="text-muted">{{ trans('calendar.no_events_today') }}</small>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Upcoming Events -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">Upcoming Events</h6>
                </div>
                <div class="card-body">
                    @forelse($upcomingEvents as $event)
                        <div class="event-item mb-2 p-2 border-left border-{{ $event->event_type_color }} bg-light">
                            <div class="fw-bold text-sm">{{ $event->title }}</div>
                            <div class="text-muted small">
                                {{ $event->formatted_date }} - {{ $event->formatted_time }}
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <span class="badge bg-{{ $event->event_type_color }}">{{ ucfirst($event->event_type) }}</span>
                                @if($event->credits)
                                    <small class="text-muted">{{ $event->credits }} credits</small>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center mt-3">
                            <small class="text-muted">No upcoming events</small>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Calendar Legend -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Event Types</h6>
                </div>
                <div class="card-body">
                    <div class="legend-item d-flex align-items-center mb-2">
                        <div class="legend-color bg-primary me-2" style="width: 12px; height: 12px; border-radius: 2px;"></div>
                        <small>Courses</small>
                    </div>
                    <div class="legend-item d-flex align-items-center mb-2">
                        <div class="legend-color bg-success me-2" style="width: 12px; height: 12px; border-radius: 2px;"></div>
                        <small>Training</small>
                    </div>
                    <div class="legend-item d-flex align-items-center mb-2">
                        <div class="legend-color bg-danger me-2" style="width: 12px; height: 12px; border-radius: 2px;"></div>
                        <small>Exams</small>
                    </div>
                    <div class="legend-item d-flex align-items-center mb-2">
                        <div class="legend-color bg-info me-2" style="width: 12px; height: 12px; border-radius: 2px;"></div>
                        <small>Webinars</small>
                    </div>
                    <div class="legend-item d-flex align-items-center mb-2">
                        <div class="legend-color bg-warning me-2" style="width: 12px; height: 12px; border-radius: 2px;"></div>
                        <small>Workshops</small>
                    </div>
                    <div class="legend-item d-flex align-items-center mb-2">
                        <div class="legend-color bg-secondary me-2" style="width: 12px; height: 12px; border-radius: 2px;"></div>
                        <small>Meetings</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Details Modal (Read-Only) -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle">{{ trans('calendar.event_details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="eventModalBody">
                <!-- Event details will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('courses.close') }}</button>
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

    .event-item.training {
        border-color: #28a745;
        background-color: rgba(40, 167, 69, 0.1);
    }

    .event-item.exam {
        border-color: #dc3545;
        background-color: rgba(220, 53, 69, 0.1);
    }

    .event-item.webinar {
        border-color: #17a2b8;
        background-color: rgba(23, 162, 184, 0.1);
    }

    .event-item.workshop {
        border-color: #ffc107;
        background-color: rgba(255, 193, 7, 0.1);
    }

    .event-item.meeting {
        border-color: #6c757d;
        background-color: rgba(108, 117, 125, 0.1);
    }

    .mini-calendar {
        font-size: 0.75rem;
    }

    .mini-calendar table {
        width: 100%;
    }

    .mini-calendar td {
        text-align: center;
        padding: 2px;
        cursor: pointer;
    }

    .mini-calendar td:hover {
        background-color: rgba(79, 70, 229, 0.1);
    }

    .mini-calendar .today {
        background-color: #4f46e5;
        color: white;
        border-radius: 50%;
    }

    .border-left {
        border-left: 3px solid !important;
    }
</style>

<script>
    let currentDate = new Date({{ $currentYear }}, {{ $currentMonth - 1 }}, 1);
    let currentView = 'month';

    // Localized month and day names from controller
    const monthNames = @json($monthNames);
    const dayNames = @json($dayNames);
    const dayNamesShort = @json($dayNamesShort);

    // Course events from server
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
                    eventEl.className = `event-item ${event.eventType}`;
                    eventEl.textContent = event.title;
                    eventEl.onclick = (e) => {
                        e.stopPropagation();
                        viewEventDetails(event);
                    };
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
        const lastDay = new Date(year, month + 1, 0);
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
        // Reload page with new month parameter
        window.location.href = `?month=${currentDate.getMonth() + 1}&year=${currentDate.getFullYear()}`;
    }

    function goToNextMonth() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        // Reload page with new month parameter
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

    function changeView(view) {
        currentView = view;
        document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');

        // In a real implementation, you would switch between different calendar views here
        alert(`Switching to ${view} view - this feature can be implemented based on your needs`);
    }

    function viewEventDetails(event) {
        document.getElementById('eventModalTitle').textContent = event.title;

        let modalBody = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Event Information</h6>
                    <table class="table table-borderless table-sm">
                        <tr><td><strong>Course Code:</strong></td><td>${event.courseCode || 'N/A'}</td></tr>
                        <tr><td><strong>Type:</strong></td><td><span class="badge bg-${getEventTypeColor(event.eventType)}">${event.eventType.charAt(0).toUpperCase() + event.eventType.slice(1)}</span></td></tr>
                        <tr><td><strong>Status:</strong></td><td><span class="badge bg-${getStatusColor(event.status)}">${event.status.charAt(0).toUpperCase() + event.status.slice(1)}</span></td></tr>
                        <tr><td><strong>Date:</strong></td><td>${new Date(event.date).toLocaleDateString()}</td></tr>
                        <tr><td><strong>Time:</strong></td><td>${event.startTime && event.endTime ? event.startTime + ' - ' + event.endTime : 'All Day'}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Details</h6>
                    <table class="table table-borderless table-sm">
                        <tr><td><strong>Instructor:</strong></td><td>${event.instructor || 'N/A'}</td></tr>
                        <tr><td><strong>Location:</strong></td><td>${event.location || 'N/A'}</td></tr>
                        <tr><td><strong>Duration:</strong></td><td>${event.durationHours ? event.durationHours + ' hours' : 'N/A'}</td></tr>
                        <tr><td><strong>Credits:</strong></td><td>${event.credits || 'N/A'}</td></tr>
                        <tr><td><strong>Mandatory:</strong></td><td>${event.isMandatory ? 'Yes' : 'No'}</td></tr>
                        <tr><td><strong>Online:</strong></td><td>${event.isOnline ? 'Yes' : 'No'}</td></tr>
                    </table>
                </div>
            </div>
        `;

        if (event.description) {
            modalBody += `<div class="mt-3"><h6>Description</h6><p>${event.description}</p></div>`;
        }

        if (event.isOnline && event.meetingUrl) {
            modalBody += `<div class="mt-3"><h6>Meeting Link</h6><a href="${event.meetingUrl}" target="_blank" class="btn btn-primary btn-sm"><i class="fas fa-external-link-alt me-1"></i>Join Meeting</a></div>`;
        }

        document.getElementById('eventModalBody').innerHTML = modalBody;
        new bootstrap.Modal(document.getElementById('eventModal')).show();
    }

    function getEventTypeColor(type) {
        const colors = {
            'course': 'primary',
            'training': 'success',
            'exam': 'danger',
            'webinar': 'info',
            'workshop': 'warning',
            'meeting': 'secondary'
        };
        return colors[type] || 'primary';
    }

    function getStatusColor(status) {
        const colors = {
            'scheduled': 'primary',
            'ongoing': 'warning',
            'completed': 'success',
            'cancelled': 'danger'
        };
        return colors[status] || 'primary';
    }

    // Initialize calendar on page load
    document.addEventListener('DOMContentLoaded', function() {
        generateCalendar();
    });
</script>
@endsection