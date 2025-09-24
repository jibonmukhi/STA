@extends('layouts.advanced-dashboard')

@section('page-title', __('companies.add_new_company'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="fas fa-plus me-2"></i>{{ __('companies.add_new_company') }}
            </h4>
            <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>{{ __('companies.back_to_companies') }}
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building me-2"></i>{{ __('companies.company_information') }}
                </h5>
            </div>
            <div class="card-body">
                <!-- Display Flash Messages -->
                @include('components.flash-messages')

                <!-- Display Validation Errors -->
                @if($errors->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ $errors->first('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any() && !$errors->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>{{ __('companies.please_fix_errors') }}</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-8">
                            <!-- ATECO Code - Priority Field -->
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="card bg-light border-primary mb-4">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary">
                                                <i class="fas fa-tag me-2"></i>{{ __('companies.ATECO Classification Code') }}
                                            </h6>
                                            <div class="mb-3">
                                                <label for="ateco_code" class="form-label fw-semibold">{{ __('companies.ATECO Code') }}</label>
                                                <input type="text" class="form-control form-control-lg @error('ateco_code') is-invalid @enderror"
                                                       id="ateco_code" name="ateco_code" value="{{ old('ateco_code') }}"
                                                       placeholder="e.g. 620100" maxlength="10">
                                                <div class="form-text">
                                                    <i class="fas fa-info-circle me-1"></i>{{ __('companies.Economic activity classification code (numeric only)') }}
                                                </div>
                                                @error('ateco_code')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">
                                            {{ __('companies.company_name') }} <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">{{ __('companies.email') }}</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email') }}">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">{{ __('companies.phone') }}</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" value="{{ old('phone') }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="piva" class="form-label">{{ __('companies.tax_id') }} / VAT Number</label>
                                        <input type="text" class="form-control @error('piva') is-invalid @enderror" 
                                               id="piva" name="piva" value="{{ old('piva') }}">
                                        @error('piva')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            

                            <div class="mb-3">
                                <label for="website" class="form-label">{{ __('companies.website') }}</label>
                                <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                       id="website" name="website" value="{{ old('website') }}" 
                                       placeholder="https://example.com">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">{{ __('companies.address') }}</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="logo" class="form-label">{{ __('companies.logo') }}</label>
                                <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                                       id="logo" name="logo" accept="image/*" onchange="previewLogo(this)">
                                <div class="form-text">{{ __('companies.max_file_size') }}</div>
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div id="logo-preview" class="text-center d-none">
                                    <img id="preview-image" src="" alt="{{ __('companies.logo_preview') }}"
                                         class="img-fluid rounded border" style="max-height: 150px;">
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="active" value="0">
                                    <input class="form-check-input" type="checkbox" id="active" name="active" value="1" checked>
                                    <label class="form-check-label" for="active">
                                        {{ __('companies.active') }} {{ __('companies.company') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('companies.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-times me-2"></i>{{ __('companies.cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ __('companies.create_company_btn') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-image').src = e.target.result;
            document.getElementById('logo-preview').classList.remove('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
