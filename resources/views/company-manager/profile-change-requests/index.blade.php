@extends('layouts.advanced-dashboard')

@section('page-title', trans('profile.profile_change_requests'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h4 class="page-title">{{ trans('profile.profile_change_requests') }}</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">{{ trans('profile.dashboard') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ trans('profile.profile_change_requests') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-edit me-2"></i>{{ trans('profile.pending_changes') }}
                    </h5>
                </div>
                <div class="card-body">
                    @if($requests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ trans('profile.user') }}</th>
                                        <th>{{ trans('profile.email') }}</th>
                                        <th>{{ trans('profile.fields_changed') }}</th>
                                        <th>{{ trans('profile.requested_on') }}</th>
                                        <th>{{ trans('profile.message') }}</th>
                                        <th class="text-center">{{ trans('profile.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requests as $request)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $request->user->photo_url }}" alt="{{ $request->user->full_name }}"
                                                         class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                                                    <strong>{{ $request->user->full_name }}</strong>
                                                </div>
                                            </td>
                                            <td>{{ $request->user->email }}</td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ count($request->getChangedFields()) }} {{ trans('profile.field_s') }}
                                                </span>
                                            </td>
                                            <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                @if($request->request_message)
                                                    <span class="text-muted" title="{{ $request->request_message }}">
                                                        {{ Str::limit($request->request_message, 30) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('company-manager.profile-change-requests.show', $request->id) }}"
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye me-1"></i>{{ trans('profile.review') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $requests->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">{{ trans('profile.no_pending_requests') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
