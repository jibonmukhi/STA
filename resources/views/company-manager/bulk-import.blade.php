@extends('layouts.advanced-dashboard')

@section('page-title', __('bulk_import.page_title'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-1">{{ __('bulk_import.page_title') }}</h3>
                            <p class="card-text opacity-75 mb-0">{{ __('bulk_import.page_subtitle') }}</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('company-users.index') }}" class="btn btn-outline-light">
                                <i class="fas fa-users me-1"></i> {{ __('bulk_import.company_users') }}
                            </a>
                            <a href="{{ route('company-manager.profile') }}" class="btn btn-light">
                                <i class="fas fa-user me-1"></i> {{ __('bulk_import.my_profile') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Instructions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>{{ __('bulk_import.how_to_use') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-info">{{ __('bulk_import.step_1_title') }}</h6>
                            <ol class="mb-4">
                                <li>{!! __('bulk_import.step_1_1') !!}</li>
                                <li>{{ __('bulk_import.step_1_2') }}</li>
                                <li>{{ __('bulk_import.step_1_3') }}</li>
                            </ol>

                            <h6 class="text-info">{{ __('bulk_import.step_2_title') }}</h6>
                            <ol class="mb-4">
                                <li>{!! __('bulk_import.step_2_1') !!}</li>
                                <li>{{ __('bulk_import.step_2_2') }}</li>
                                <li>{{ __('bulk_import.step_2_3') }}</li>
                                <li>{{ __('bulk_import.step_2_4') }}</li>
                            </ol>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-info">{{ __('bulk_import.step_3_title') }}</h6>
                            <ol class="mb-4">
                                <li>{{ __('bulk_import.step_3_1') }}</li>
                                <li>{{ __('bulk_import.step_3_2') }}</li>
                                <li>{{ __('bulk_import.step_3_3') }}</li>
                                <li>{{ __('bulk_import.step_3_4') }}</li>
                            </ol>

                            <div class="alert alert-warning mb-0">
                                <strong><i class="fas fa-exclamation-triangle me-2"></i>{{ __('bulk_import.important_notes') }}</strong>
                                <ul class="mb-0 mt-2">
                                    <li>{!! __('bulk_import.note_password') !!}</li>
                                    <li>{!! __('bulk_import.note_status') !!}</li>
                                    <li>{{ __('bulk_import.note_unique_email') }}</li>
                                    <li>{{ __('bulk_import.note_max_size') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Download Template -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-download me-2"></i>{{ __('bulk_import.download_template_title') }}
                    </h5>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-file-excel text-success" style="font-size: 5rem;"></i>
                    <h5 class="mt-3">{{ __('bulk_import.excel_template') }}</h5>
                    <p class="text-muted">
                        {{ __('bulk_import.template_description') }}
                    </p>

                    <div class="list-group list-group-flush text-start mb-4">
                        <div class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            {!! __('bulk_import.feature_1') !!}
                        </div>
                        <div class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            {{ __('bulk_import.feature_2') }}
                        </div>
                        <div class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            {{ __('bulk_import.feature_3') }}
                        </div>
                        <div class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i>
                            {{ __('bulk_import.feature_4') }}
                        </div>
                    </div>

                    <a href="{{ route('company-manager.template.download') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-download me-2"></i>{{ __('bulk_import.download_button') }}
                    </a>
                    <div class="mt-2">
                        <small class="text-muted">{{ __('bulk_import.file_format') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Form -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-upload me-2"></i>{{ __('bulk_import.upload_title') }}
                    </h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6 class="alert-heading"><i class="fas fa-exclamation-circle me-2"></i>{{ __('bulk_import.upload_errors') }}</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('import_errors'))
                        <div class="alert alert-warning">
                            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>{{ __('bulk_import.import_errors', ['count' => count(session('import_errors'))]) }}</h6>
                            <div class="mt-2" style="max-height: 200px; overflow-y: auto;">
                                <ul class="mb-0">
                                    @foreach(session('import_errors') as $error)
                                        <li><small>{{ $error }}</small></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('company-manager.bulk-import.process') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="company_id" class="form-label">
                                {{ __('bulk_import.select_company') }} <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('company_id') is-invalid @enderror" id="company_id" name="company_id" required>
                                <option value="">{{ __('bulk_import.choose_company') }}</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>{{ __('bulk_import.company_info') }}
                            </div>
                            @error('company_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="excel_file" class="form-label">
                                {{ __('bulk_import.upload_excel') }} <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control @error('excel_file') is-invalid @enderror" id="excel_file" name="excel_file" accept=".xlsx,.xls" required>
                            <div class="form-text">
                                <i class="fas fa-file-excel me-1"></i>{{ __('bulk_import.accepted_formats') }}
                            </div>
                            @error('excel_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <strong><i class="fas fa-lightbulb me-2"></i>{{ __('bulk_import.before_uploading') }}</strong>
                            <ul class="mb-0 mt-2">
                                <li>{!! __('bulk_import.check_1') !!}</li>
                                <li>{!! __('bulk_import.check_2') !!}</li>
                                <li>{!! __('bulk_import.check_3') !!}</li>
                                <li>{!! __('bulk_import.check_4') !!}</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-upload me-2"></i>{{ __('bulk_import.upload_import_button') }}
                            </button>
                            <a href="{{ route('company-users.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>{{ __('bulk_import.cancel_button') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Template Columns Reference -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-table me-2"></i>{{ __('bulk_import.columns_reference_title') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="30">#</th>
                                    <th width="200">{{ __('bulk_import.column_header') }}</th>
                                    <th>{{ __('bulk_import.description_header') }}</th>
                                    <th width="100">{{ __('bulk_import.required_header') }}</th>
                                    <th width="200">{{ __('bulk_import.example_header') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td><strong>{{ __('bulk_import.col_name') }}</strong></td>
                                    <td>{{ __('bulk_import.col_name_desc') }}</td>
                                    <td><span class="badge bg-danger">{{ __('bulk_import.required_badge') }}</span></td>
                                    <td>John</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td><strong>{{ __('bulk_import.col_surname') }}</strong></td>
                                    <td>{{ __('bulk_import.col_surname_desc') }}</td>
                                    <td><span class="badge bg-danger">{{ __('bulk_import.required_badge') }}</span></td>
                                    <td>Doe</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td><strong>{{ __('bulk_import.col_email') }}</strong></td>
                                    <td>{{ __('bulk_import.col_email_desc') }}</td>
                                    <td><span class="badge bg-danger">{{ __('bulk_import.required_badge') }}</span></td>
                                    <td>john.doe@example.com</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td><strong>{{ __('bulk_import.col_phone') }}</strong></td>
                                    <td>{{ __('bulk_import.col_phone_desc') }}</td>
                                    <td><span class="badge bg-secondary">{{ __('bulk_import.optional_badge') }}</span></td>
                                    <td>+39 123 456 7890</td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td><strong>{{ __('bulk_import.col_mobile') }}</strong></td>
                                    <td>{{ __('bulk_import.col_mobile_desc') }}</td>
                                    <td><span class="badge bg-secondary">{{ __('bulk_import.optional_badge') }}</span></td>
                                    <td>+39 987 654 3210</td>
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td><strong>{{ __('bulk_import.col_dob') }}</strong></td>
                                    <td>{{ __('bulk_import.col_dob_desc') }}</td>
                                    <td><span class="badge bg-secondary">{{ __('bulk_import.optional_badge') }}</span></td>
                                    <td>1990-01-15</td>
                                </tr>
                                <tr>
                                    <td>7</td>
                                    <td><strong>{{ __('bulk_import.col_pob') }}</strong></td>
                                    <td>{{ __('bulk_import.col_pob_desc') }}</td>
                                    <td><span class="badge bg-secondary">{{ __('bulk_import.optional_badge') }}</span></td>
                                    <td>Rome</td>
                                </tr>
                                <tr>
                                    <td>8</td>
                                    <td><strong>{{ __('bulk_import.col_country') }}</strong></td>
                                    <td>{{ __('bulk_import.col_country_desc') }}</td>
                                    <td><span class="badge bg-secondary">{{ __('bulk_import.optional_badge') }}</span></td>
                                    <td>Italy</td>
                                </tr>
                                <tr>
                                    <td>9</td>
                                    <td><strong>{{ __('bulk_import.col_gender') }}</strong></td>
                                    <td>{{ __('bulk_import.col_gender_desc') }}</td>
                                    <td><span class="badge bg-secondary">{{ __('bulk_import.optional_badge') }}</span></td>
                                    <td>male</td>
                                </tr>
                                <tr>
                                    <td>10</td>
                                    <td><strong>{{ __('bulk_import.col_cf') }}</strong></td>
                                    <td>{{ __('bulk_import.col_cf_desc') }}</td>
                                    <td><span class="badge bg-secondary">{{ __('bulk_import.optional_badge') }}</span></td>
                                    <td>RSSMRA80A01H501U</td>
                                </tr>
                                <tr>
                                    <td>11</td>
                                    <td><strong>{{ __('bulk_import.col_address') }}</strong></td>
                                    <td>{{ __('bulk_import.col_address_desc') }}</td>
                                    <td><span class="badge bg-secondary">{{ __('bulk_import.optional_badge') }}</span></td>
                                    <td>Via Roma 123, Rome, Italy</td>
                                </tr>
                                <tr>
                                    <td>12</td>
                                    <td><strong>{{ __('bulk_import.col_percentage') }}</strong></td>
                                    <td>{{ __('bulk_import.col_percentage_desc') }}</td>
                                    <td><span class="badge bg-danger">{{ __('bulk_import.required_badge') }}</span></td>
                                    <td>100</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-success mt-3 mb-0">
                        <strong><i class="fas fa-info-circle me-2"></i>{{ __('bulk_import.after_import') }}</strong>
                        <ul class="mb-0 mt-2">
                            <li>{!! __('bulk_import.after_import_1') !!}</li>
                            <li>{{ __('bulk_import.after_import_2') }}</li>
                            <li>{!! __('bulk_import.after_import_3') !!}</li>
                            <li>{{ __('bulk_import.after_import_4') }}</li>
                            <li>{!! __('bulk_import.after_import_5') !!}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
