<div>
    <div class="mb-3">
        <p class="text-muted">
            {{ trans('profile.password_help') }}
        </p>
    </div>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">{{ trans('profile.current_password') }}</label>
            <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                   id="update_password_current_password" name="current_password" autocomplete="current-password">
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label">{{ trans('profile.new_password') }}</label>
            <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                   id="update_password_password" name="password" autocomplete="new-password">
            @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label">{{ trans('profile.confirm_password') }}</label>
            <input type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                   id="update_password_password_confirmation" name="password_confirmation" autocomplete="new-password">
            @error('password_confirmation', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-end align-items-center">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>{{ trans('profile.save') }}
            </button>

            @if (session('status') === 'password-updated')
                <div class="alert alert-success ms-3 mb-0">
                    <i class="fas fa-check-circle me-2"></i>{{ trans('profile.password_updated') }}
                </div>
            @endif
        </div>
    </form>
</div>
