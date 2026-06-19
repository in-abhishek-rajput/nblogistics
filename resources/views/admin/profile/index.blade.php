@extends('admin.layouts.master')

@section('title', 'Profile Settings')
@section('page_title', 'Profile Settings')

@section('content')
<div class="container-fluid pt-4 px-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Profile Summary Card -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center p-4">
                    <div class="position-relative d-inline-block mb-4">
                        <img id="profileImagePreview" src="{{ $user->logo ? asset('storage/' . $user->logo) : '#' }}" alt="Profile Logo" class="rounded img-fluid border border-3 border-primary {{ $user->logo ? '' : 'd-none' }}" style="width: 150px; object-fit: cover;">
                        @if(!$user->logo)
                            <div id="profileImagePlaceholder" class="rounded bg-light d-flex align-items-center justify-content-center text-primary fw-bold mx-auto border border-3 border-primary" style="width: 150px; font-size: 3rem;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <h4 class="mb-1 fw-bold">{{ $user->name }}</h4>
                    <p class="text-muted mb-0">{{ $user->email }}</p>
                </div>
            </div>
        </div>

        <!-- Forms Column -->
        <div class="col-xl-8">
            <!-- Update Profile Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-person-fill text-primary me-2"></i>Update Profile Details</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('profile.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Username</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="logo" class="form-label fw-semibold">Profile Logo</label>
                            <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                            <div class="form-text">Recommended size: 300x300px. Max size: 2MB.</div>
                        </div>

                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill"><i class="bi bi-save me-2"></i>Save Changes</button>
                    </form>
                </div>
            </div>

            <!-- Login Credentials Card -->
            <div class="card border-0 shadow-sm rounded-4 border-primary" style="border-top: 5px solid var(--bs-primary) !important;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-shield-lock-fill text-primary me-2"></i>Login Credentials</h5>
                    <p class="text-muted small mt-1 mb-0">Use these details to log in to your account.</p>
                </div>
                <div class="card-body p-4">
                    
                    <!-- Update Mobile Form -->
                    <form action="{{ route('profile.update', $user->id) }}" method="POST" class="mb-4 pb-4 border-bottom">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="update_mobile" value="1">
                        
                        <div class="mb-3">
                            <label for="mobile" class="form-label fw-semibold">Mobile Number</label>
                            <input type="text" class="form-control" id="mobile" name="mobile" value="{{ old('mobile', $user->mobile) }}" required>
                        </div>
                        <button type="submit" class="btn btn-outline-primary px-4 py-2 rounded-pill"><i class="bi bi-phone me-2"></i>Update Mobile</button>
                    </form>

                    <!-- Update Password Form -->
                    <form action="{{ route('profile.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="update_password" value="1">
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-semibold">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">New Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirm New Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill"><i class="bi bi-key me-2"></i>Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('logo').addEventListener('change', function(event) {
        if (event.target.files && event.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('profileImagePreview');
                const placeholder = document.getElementById('profileImagePlaceholder');
                
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                
                if (placeholder) {
                    placeholder.classList.add('d-none');
                }
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    });
</script>
@endpush
