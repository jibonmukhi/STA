<div>
    <h5 class="mb-3">Personal Information</h5>
    
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="row">
            <!-- Photo Upload -->
            <div class="col-12 mb-3">
                <div class="text-center">
                    <div class="mb-3">
                        @if($user->photo)
                            <img id="current-photo" src="{{ $user->photo_url }}" alt="{{ $user->name }}" 
                                 class="rounded-circle" width="120" height="120" style="object-fit: cover;">
                        @else
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 120px; height: 120px; margin: 0 auto;">
                                <i class="fas fa-user fa-3x text-muted"></i>
                            </div>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <label for="photo" class="form-label">Profile Photo</label>
                        <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                               id="photo" name="photo" accept="image/*" onchange="previewPhoto(this)">
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div id="photo-preview" class="text-center d-none">
                        <img id="preview-image" src="" alt="New Photo Preview" 
                             class="rounded-circle" width="120" height="120" style="object-fit: cover;">
                        <p class="form-text mt-2">New Photo Preview</p>
                    </div>
                </div>
            </div>

            <!-- Name -->
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Surname -->
            <div class="col-md-6 mb-3">
                <label for="surname" class="form-label">Surname</label>
                <input type="text" class="form-control @error('surname') is-invalid @enderror" 
                       id="surname" name="surname" value="{{ old('surname', $user->surname) }}">
                @error('surname')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email -->
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                
                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2">
                        <div class="alert alert-warning alert-sm">
                            Your email address is unverified.
                            <button form="send-verification" class="btn btn-link p-0 text-decoration-underline">
                                Click here to re-send the verification email.
                            </button>
                        </div>

                        @if (session('status') === 'verification-link-sent')
                            <div class="alert alert-success alert-sm">
                                A new verification link has been sent to your email address.
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Phone -->
            <div class="col-md-6 mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Mobile -->
            <div class="col-md-6 mb-3">
                <label for="mobile" class="form-label">Mobile</label>
                <input type="tel" class="form-control @error('mobile') is-invalid @enderror" 
                       id="mobile" name="mobile" value="{{ old('mobile', $user->mobile) }}">
                @error('mobile')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Gender -->
            <div class="col-md-6 mb-3">
                <label for="gender" class="form-label">Gender</label>
                <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                    <option value="">Select Gender</option>
                    <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('gender')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Date of Birth -->
            <div class="col-md-6 mb-3">
                <label for="date_of_birth" class="form-label">Date of Birth</label>
                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                       id="date_of_birth" name="date_of_birth" 
                       value="{{ old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '') }}">
                @error('date_of_birth')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Place of Birth -->
            <div class="col-md-6 mb-3">
                <label for="place_of_birth" class="form-label">Place of Birth</label>
                <input type="text" class="form-control @error('place_of_birth') is-invalid @enderror" 
                       id="place_of_birth" name="place_of_birth" value="{{ old('place_of_birth', $user->place_of_birth) }}">
                @error('place_of_birth')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Country -->
            <div class="col-md-6 mb-3">
                <label for="country" class="form-label">Country</label>
                <input type="text" class="form-control @error('country') is-invalid @enderror" 
                       id="country" name="country" value="{{ old('country', $user->country ?? 'IT') }}">
                @error('country')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Codice Fiscale -->
            <div class="col-md-6 mb-3">
                <label for="cf" class="form-label">Codice Fiscale</label>
                <input type="text" class="form-control @error('cf') is-invalid @enderror" 
                       id="cf" name="cf" value="{{ old('cf', $user->cf) }}" maxlength="16">
                @error('cf')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Address -->
            <div class="col-12 mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control @error('address') is-invalid @enderror" 
                          id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Update Profile
            </button>
        </div>

        @if (session('status') === 'profile-updated')
            <div class="alert alert-success mt-3">
                <i class="fas fa-check-circle me-2"></i>Profile updated successfully!
            </div>
        @endif
    </form>
</div>

<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-image').src = e.target.result;
            document.getElementById('photo-preview').classList.remove('d-none');
            if (document.getElementById('current-photo')) {
                document.getElementById('current-photo').style.opacity = '0.5';
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
