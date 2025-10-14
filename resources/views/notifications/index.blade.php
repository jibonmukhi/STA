@extends('layouts.advanced-dashboard')

@section('page-title', __('notifications.page_title'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-bell me-2"></i>{{ __('notifications.title') }}
                </h2>
                <div class="d-flex gap-2">
                    @if(Auth::user()->unreadNotifications()->count() > 0)
                        <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-check-double me-1"></i>{{ __('notifications.mark_all_read') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="btn-group" role="group">
                <a href="{{ route('notifications.index', ['filter' => 'all']) }}"
                   class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-list me-1"></i>{{ __('notifications.filter_all') }}
                </a>
                <a href="{{ route('notifications.index', ['filter' => 'unread']) }}"
                   class="btn btn-sm {{ $filter === 'unread' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-envelope me-1"></i>{{ __('notifications.filter_unread') }} ({{ Auth::user()->unreadNotifications()->count() }})
                </a>
                <a href="{{ route('notifications.index', ['filter' => 'read']) }}"
                   class="btn btn-sm {{ $filter === 'read' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-envelope-open me-1"></i>{{ __('notifications.filter_read') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    @if($notifications->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">{{ __('notifications.no_notifications_found') }}</h5>
                            <p class="text-muted">
                                @if($filter === 'unread')
                                    {{ __('notifications.no_unread_notifications') }}
                                @elseif($filter === 'read')
                                    {{ __('notifications.no_read_notifications') }}
                                @else
                                    {{ __('notifications.no_notifications_yet') }}
                                @endif
                            </p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($notifications as $notification)
                                @php
                                    $data = $notification->data;
                                    $isUnread = is_null($notification->read_at);
                                    $type = $data['type'] ?? 'info';

                                    $icon = match($type) {
                                        'user_approval_request' => 'fas fa-user-clock',
                                        'user_approved' => 'fas fa-user-check',
                                        'user_rejected' => 'fas fa-user-times',
                                        'company_invitation' => 'fas fa-envelope',
                                        'course_enrollment' => 'fas fa-graduation-cap',
                                        'certificate_issued' => 'fas fa-certificate',
                                        'system_update' => 'fas fa-cog',
                                        default => 'fas fa-bell',
                                    };

                                    $color = match($type) {
                                        'user_approval_request' => 'warning',
                                        'user_approved' => 'success',
                                        'user_rejected' => 'danger',
                                        'company_invitation' => 'info',
                                        'course_enrollment' => 'primary',
                                        'certificate_issued' => 'success',
                                        'system_update' => 'info',
                                        default => 'secondary',
                                    };
                                @endphp

                                <div class="list-group-item notification-list-item {{ $isUnread ? 'notification-unread' : '' }}">
                                    <div class="d-flex align-items-start">
                                        <div class="notification-icon-wrapper bg-{{ $color }}-subtle text-{{ $color }} me-3">
                                            <i class="{{ $icon }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <h6 class="mb-0 fw-bold">
                                                    {{ $data['title'] ?? __('notifications.title') }}
                                                    @if($isUnread)
                                                        <span class="badge bg-primary badge-sm ms-2">{{ __('notifications.new_badge') }}</span>
                                                    @endif
                                                </h6>
                                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-2 text-muted">{{ $data['message'] ?? '' }}</p>
                                            <div class="d-flex gap-2">
                                                @if(isset($data['action_url']))
                                                    <a href="{{ $data['action_url'] }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-external-link-alt me-1"></i>{{ __('notifications.view_details') }}
                                                    </a>
                                                @endif

                                                @if($isUnread)
                                                    <form action="{{ route('notifications.mark-as-read', $notification->id) }}"
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                            <i class="fas fa-check me-1"></i>{{ __('notifications.mark_as_read') }}
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('notifications.destroy', $notification->id) }}"
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('{{ __('notifications.confirm_delete') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash me-1"></i>{{ __('common.delete') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="card-footer bg-white">
                            {{ $notifications->appends(['filter' => $filter])->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .notification-list-item {
        border-left: 3px solid transparent;
        transition: all 0.2s ease;
        padding: 1.25rem;
    }

    .notification-list-item:hover {
        background-color: #f8f9fa;
        border-left-color: #4f46e5;
    }

    .notification-unread {
        background-color: #f0f4ff;
        border-left-color: #4f46e5;
    }

    .notification-unread:hover {
        background-color: #e0e7ff;
    }

    .notification-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .bg-warning-subtle {
        background-color: #fef3c7;
    }

    .bg-success-subtle {
        background-color: #d1fae5;
    }

    .bg-danger-subtle {
        background-color: #fee2e2;
    }

    .bg-info-subtle {
        background-color: #dbeafe;
    }

    .bg-primary-subtle {
        background-color: #e0e7ff;
    }

    .bg-secondary-subtle {
        background-color: #f3f4f6;
    }

    .text-warning {
        color: #f59e0b !important;
    }

    .text-success {
        color: #10b981 !important;
    }

    .text-danger {
        color: #ef4444 !important;
    }

    .text-info {
        color: #0ea5e9 !important;
    }

    .text-primary {
        color: #4f46e5 !important;
    }

    .text-secondary {
        color: #6b7280 !important;
    }
</style>
@endsection
