@extends('layouts.advanced-dashboard')

@section('page-title', __('data_vault.edit_category'))

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
                            <li class="breadcrumb-item active">{{ __('data_vault.edit_category') }}</li>
                        </ol>
                    </nav>
                    <h2><i class="fas fa-edit me-2"></i>{{ __('data_vault.edit_category') }}</h2>
                </div>
                <div>
                    <a href="{{ route('data-vault.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>{{ __('data_vault.back_to_categories') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('data_vault.category_details') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('data-vault.update', $category) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="code" class="form-label">{{ __('data_vault.category_code') }} *</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                   id="code" name="code" value="{{ old('code', $category->code) }}" required
                                   @if($category->is_system) readonly @endif>
                            <small class="text-muted">{{ __('data_vault.code_help') }}</small>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name_en" class="form-label">{{ __('data_vault.category_name_en') }} *</label>
                            <input type="text" class="form-control @error('name_en') is-invalid @enderror"
                                   id="name_en" name="name_en" value="{{ old('name_en', $category->name_en) }}" required>
                            @error('name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name_it" class="form-label">{{ __('data_vault.category_name_it') }} *</label>
                            <input type="text" class="form-control @error('name_it') is-invalid @enderror"
                                   id="name_it" name="name_it" value="{{ old('name_it', $category->name_it) }}" required>
                            @error('name_it')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('data_vault.description') }}</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sort_order" class="form-label">{{ __('data_vault.sort_order') }}</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                   {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">{{ __('data_vault.is_active') }}</label>
                        </div>

                        @if($category->is_system)
                            <div class="alert alert-warning">
                                <i class="fas fa-lock me-2"></i>{{ __('data_vault.system_item_warning') }}
                            </div>
                        @endif

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ __('data_vault.save') }}
                            </button>
                            <a href="{{ route('data-vault.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>{{ __('data_vault.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>{{ __('data_vault.information') }}</h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">{{ __('data_vault.created') }}:</dt>
                        <dd class="col-sm-7">{{ $category->created_at->format('M d, Y') }}</dd>

                        <dt class="col-sm-5">{{ __('data_vault.updated') }}:</dt>
                        <dd class="col-sm-7">{{ $category->updated_at->format('M d, Y') }}</dd>

                        <dt class="col-sm-5">{{ __('data_vault.total_items') }}:</dt>
                        <dd class="col-sm-7">{{ $category->items()->count() }}</dd>

                        <dt class="col-sm-5">{{ __('data_vault.system') }}:</dt>
                        <dd class="col-sm-7">
                            @if($category->is_system)
                                <span class="badge bg-secondary">{{ __('data_vault.yes') }}</span>
                            @else
                                <span class="badge bg-light text-dark">{{ __('data_vault.no') }}</span>
                            @endif
                        </dd>
                    </dl>

                    <hr>

                    <a href="{{ route('data-vault.items.index', $category) }}" class="btn btn-sm btn-outline-primary w-100">
                        <i class="fas fa-cog me-2"></i>{{ __('data_vault.manage_items') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
