@extends('layouts.advanced-dashboard')

@section('page-title', __('data_vault.data_vault'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-database me-2"></i>{{ __('data_vault.data_vault') }}</h2>
                    <p class="text-muted">{{ __('data_vault.manage_categories') }}</p>
                </div>
                <div>
                    <a href="{{ route('data-vault.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>{{ __('data_vault.create_category') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Categories Grid -->
    @if($categories->count() > 0)
        <div class="row">
            @foreach($categories as $category)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1">
                                        {{ $category->name }}
                                        @if($category->is_system)
                                            <span class="badge bg-secondary ms-2" title="{{ __('data_vault.system_protected') }}">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                        @endif
                                    </h5>
                                    <code class="text-muted small">{{ $category->code }}</code>
                                </div>
                                <div>
                                    @if($category->is_active)
                                        <span class="badge bg-success">{{ __('data_vault.is_active') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('data_vault.inactive') }}</span>
                                    @endif
                                </div>
                            </div>

                            @if($category->description)
                                <p class="card-text text-muted small">{{ $category->description }}</p>
                            @endif

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="text-muted">
                                    <i class="fas fa-list me-1"></i>
                                    {{ $category->items_count }} {{ __('data_vault.items') }}
                                </span>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex gap-2">
                                <a href="{{ route('data-vault.items.index', $category) }}" class="btn btn-sm btn-primary flex-grow-1">
                                    <i class="fas fa-cog me-1"></i>{{ __('data_vault.manage_items') }}
                                </a>
                                <a href="{{ route('data-vault.edit', $category) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(!$category->is_system)
                                    <form action="{{ route('data-vault.destroy', $category) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('{{ __('data_vault.confirm_delete_category') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-database fa-4x text-muted mb-3"></i>
                <p class="text-muted">{{ __('data_vault.no_categories') }}</p>
                <a href="{{ route('data-vault.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>{{ __('data_vault.create_category') }}
                </a>
            </div>
        </div>
    @endif

    <!-- Info Box -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info">
                <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>{{ __('data_vault.about_data_vault') }}</h6>
                <p class="mb-0">
                    {{ __('data_vault.data_vault_description') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
