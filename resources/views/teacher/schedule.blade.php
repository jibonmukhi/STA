@extends('layouts.advanced-dashboard')

@section('page-title', __('teacher.my_schedule'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('teacher.my_schedule') }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ __('teacher.schedule_description') }}</p>
                    <!-- Calendar implementation would go here - reuse the calendar from user dashboard -->
                    <div class="text-center py-5">
                        <i class="far fa-calendar-alt fa-4x text-muted mb-3"></i>
                        <p>{{ __('teacher.upcoming_events') }}: {{ $stats['upcoming_events'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
