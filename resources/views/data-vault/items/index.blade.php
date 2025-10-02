@extends('layouts.advanced-dashboard')

@section('page-title', $category->name . ' - ' . __('data_vault.items'))

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
                            <li class="breadcrumb-item active">{{ $category->name }}</li>
                        </ol>
                    </nav>
                    <h2><i class="fas fa-list me-2"></i>{{ $category->name }}</h2>
                    <p class="text-muted mb-0">
                        <code>{{ $category->code }}</code>
                        @if($category->is_system)
                            <span class="badge bg-secondary ms-2">{{ __('data_vault.system_protected') }}</span>
                        @endif
                    </p>
                </div>
                <div>
                    <a href="{{ route('data-vault.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>{{ __('data_vault.back_to_categories') }}
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

    <div class="row">
        <!-- Items List -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('data_vault.items') }} ({{ $items->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($items->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('data_vault.code') }}</th>
                                        <th>{{ __('data_vault.label_english') }}</th>
                                        <th>{{ __('data_vault.label_italian') }}</th>
                                        <th>{{ __('data_vault.color') }}</th>
                                        <th>{{ __('data_vault.is_default') }}</th>
                                        <th>{{ __('data_vault.is_active') }}</th>
                                        <th width="150">{{ __('data_vault.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        <tr>
                                            <td><code>{{ $item->code }}</code></td>
                                            <td>{{ $item->label_en }}</td>
                                            <td>{{ $item->label_it }}</td>
                                            <td>
                                                @if($item->color)
                                                    <span class="badge bg-{{ $item->color }}">{{ $item->color }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->is_default)
                                                    <i class="fas fa-check-circle text-success"></i>
                                                @else
                                                    <i class="far fa-circle text-muted"></i>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->is_active)
                                                    <span class="badge bg-success">{{ __('data_vault.active') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ __('data_vault.inactive') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                        onclick="editItem({{ $item->id }}, '{{ $item->code }}', '{{ $item->label_en }}', '{{ $item->label_it }}', '{{ $item->color }}', '{{ $item->icon }}', {{ $item->sort_order }}, {{ $item->is_default ? 'true' : 'false' }}, {{ $item->is_active ? 'true' : 'false' }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                @if(!$item->is_system)
                                                    <form action="{{ route('data-vault.items.destroy', [$category, $item]) }}"
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('{{ __('data_vault.confirm_delete_item') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <i class="fas fa-lock text-muted" title="{{ __('data_vault.system_item_warning') }}"></i>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-list fa-3x text-muted mb-3"></i>
                            <p class="text-muted">{{ __('data_vault.no_items') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Add/Edit Item Form -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0" id="formTitle">{{ __('data_vault.add_new_item') }}</h5>
                </div>
                <div class="card-body">
                    <form id="itemForm" action="{{ route('data-vault.items.store', $category) }}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" value="POST" id="formMethod">
                        <input type="hidden" name="item_id" id="itemId">

                        <div class="mb-3">
                            <label for="code" class="form-label">{{ __('data_vault.code') }} *</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                   id="code" name="code" required>
                            <small class="text-muted">{{ __('data_vault.code_help') }}</small>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="label_en" class="form-label">{{ __('data_vault.label_english') }} *</label>
                            <input type="text" class="form-control @error('label_en') is-invalid @enderror"
                                   id="label_en" name="label_en" required>
                            @error('label_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="label_it" class="form-label">{{ __('data_vault.label_italian') }} *</label>
                            <input type="text" class="form-control @error('label_it') is-invalid @enderror"
                                   id="label_it" name="label_it" required>
                            @error('label_it')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="color" class="form-label">{{ __('data_vault.color') }}</label>
                            <select class="form-select @error('color') is-invalid @enderror" id="color" name="color">
                                <option value="">{{ __('data_vault.none') }}</option>
                                <option value="primary">{{ __('data_vault.primary_blue') }}</option>
                                <option value="secondary">{{ __('data_vault.secondary_gray') }}</option>
                                <option value="success">{{ __('data_vault.success_green') }}</option>
                                <option value="danger">{{ __('data_vault.danger_red') }}</option>
                                <option value="warning">{{ __('data_vault.warning_yellow') }}</option>
                                <option value="info">{{ __('data_vault.info_cyan') }}</option>
                                <option value="dark">{{ __('data_vault.dark') }}</option>
                            </select>
                            <small class="text-muted">{{ __('data_vault.color_help') }}</small>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="icon" class="form-label">{{ __('data_vault.icon') }}</label>
                            <input type="text" class="form-control @error('icon') is-invalid @enderror"
                                   id="icon" name="icon" placeholder="fas fa-star">
                            <small class="text-muted">{{ __('data_vault.icon_help') }}</small>
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sort_order" class="form-label">{{ __('data_vault.sort_order') }}</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                   id="sort_order" name="sort_order" value="0">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_default" name="is_default" value="1">
                            <label class="form-check-label" for="is_default">{{ __('data_vault.is_default') }}</label>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">{{ __('data_vault.is_active') }}</label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ __('data_vault.save') }}
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                <i class="fas fa-times me-2"></i>{{ __('data_vault.cancel') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function editItem(id, code, labelEn, labelIt, color, icon, sortOrder, isDefault, isActive) {
    document.getElementById('formTitle').textContent = '{{ __('data_vault.edit_item') }}';
    document.getElementById('itemForm').action = '{{ route('data-vault.items.update', [$category, ':id']) }}'.replace(':id', id);
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('itemId').value = id;

    document.getElementById('code').value = code;
    document.getElementById('label_en').value = labelEn;
    document.getElementById('label_it').value = labelIt;
    document.getElementById('color').value = color || '';
    document.getElementById('icon').value = icon || '';
    document.getElementById('sort_order').value = sortOrder;
    document.getElementById('is_default').checked = isDefault;
    document.getElementById('is_active').checked = isActive;
}

function resetForm() {
    document.getElementById('formTitle').textContent = '{{ __('data_vault.add_new_item') }}';
    document.getElementById('itemForm').action = '{{ route('data-vault.items.store', $category) }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('itemId').value = '';
    document.getElementById('itemForm').reset();
    document.getElementById('is_active').checked = true;
}
</script>
@endpush
@endsection
