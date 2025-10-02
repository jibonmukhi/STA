@extends('layouts.advanced-dashboard')

@section('page-title', __('data_vault.create_category'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('data-vault.index') }}">{{ __('data_vault.data_vault') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('data_vault.create_category') }}</li>
                        </ol>
                    </nav>
                    <h2><i class="fas fa-plus me-2"></i>{{ __('data_vault.create_category') }}</h2>
                </div>
                <div>
                    <a href="{{ route('data-vault.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>{{ __('data_vault.back_to_categories') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('data_vault.category_details') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('data-vault.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="code" class="form-label">{{ __('data_vault.category_code') }} *</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                   id="code" name="code" value="{{ old('code') }}" required>
                            <small class="text-muted">{{ __('data_vault.code_help') }}</small>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name_en" class="form-label">{{ __('data_vault.category_name_en') }} *</label>
                            <input type="text" class="form-control @error('name_en') is-invalid @enderror"
                                   id="name_en" name="name_en" value="{{ old('name_en') }}" required>
                            @error('name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name_it" class="form-label">{{ __('data_vault.category_name_it') }} *</label>
                            <input type="text" class="form-control @error('name_it') is-invalid @enderror"
                                   id="name_it" name="name_it" value="{{ old('name_it') }}" required>
                            @error('name_it')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('data_vault.description') }}</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sort_order" class="form-label">{{ __('data_vault.sort_order') }}</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">{{ __('data_vault.is_active') }}</label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ __('data_vault.create') }}
                            </button>
                            <a href="{{ route('data-vault.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>{{ __('data_vault.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-question-circle me-2"></i>{{ __('data_vault.help') }}</h6>
                </div>
                <div class="card-body">
                    <h6>{{ __('data_vault.category_code_title') }}</h6>
                    <p class="small text-muted">{{ __('data_vault.category_code_description') }}</p>

                    <h6>{{ __('data_vault.category_names_title') }}</h6>
                    <p class="small text-muted">{{ __('data_vault.category_names_description') }}</p>

                    <h6>{{ __('data_vault.sort_order_title') }}</h6>
                    <p class="small text-muted">{{ __('data_vault.sort_order_description') }}</p>

                    <hr>

                    <div class="alert alert-info mb-0">
                        <small>
                            <i class="fas fa-lightbulb me-2"></i>
                            {{ __('data_vault.after_creating_tip') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
