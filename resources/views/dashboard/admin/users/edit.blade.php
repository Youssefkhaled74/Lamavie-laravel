@extends('dashboard.admin.layouts.main')

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h3 class="mb-0 fw-bold text-primary"><i class="fas fa-user-edit me-2"></i>Edit User</h3>
            <p class="text-muted small mb-0">Update user profile information and password.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-primary">
                <i class="fas fa-user me-1"></i>Back to Profile
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Users
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom py-3">
            <div class="fw-semibold"><i class="fas fa-id-card me-2 text-primary"></i>User Information</div>
        </div>

        <div class="card-body p-4">
            <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-md-4">
                        @php $avatarPath = $user->profile_photo ?? $user->photo ?? null; @endphp

                        <div class="p-3 border rounded-4 bg-light text-center">
                            <div class="mx-auto mb-3" style="width:160px;height:160px;border-radius:16px;overflow:hidden;background:#fff;display:flex;align-items:center;justify-content:center;border:1px solid #e5e7eb;">
                                @if($avatarPath && Storage::disk('public')->exists($avatarPath))
                                    <img id="avatarPreview" src="{{ asset('storage/'.$avatarPath) }}" style="width:100%;height:100%;object-fit:cover;" alt="avatar">
                                @else
                                    <div id="avatarFallback" style="font-size:54px;font-weight:900;color:#334155">
                                        {{ strtoupper(substr($user->name,0,1) ?? '?') }}
                                    </div>
                                    <img id="avatarPreview" src="" style="display:none;width:100%;height:100%;object-fit:cover;" alt="avatar">
                                @endif
                            </div>

                            <label class="form-label fw-semibold">Photo</label>
                            <input type="file" name="photo" class="form-control" id="photoInput" accept="image/*">
                            <div class="text-muted small mt-2">Recommended: square image (JPG/PNG).</div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                            </div>

                            <div class="col-12">
                                <div class="p-3 rounded-4 border bg-white mt-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h6 class="mb-0 fw-bold"><i class="fas fa-lock me-2 text-primary"></i>Change Password</h6>
                                        <span class="text-muted small">Leave blank to keep current password</span>
                                    </div>
                                    <div class="row g-3 mt-1">
                                        <div class="col-md-6">
                                            <label class="form-label">New Password</label>
                                            <input type="password" name="password" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Confirm Password</label>
                                            <input type="password" name="password_confirmation" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-flex gap-2 mt-2">
                                    <button class="btn btn-primary px-4">
                                        <i class="fas fa-save me-1"></i>Save changes
                                    </button>
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary px-4">
                                        Cancel
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    // live photo preview
    const input = document.getElementById('photoInput');
    const preview = document.getElementById('avatarPreview');
    const fallback = document.getElementById('avatarFallback');

    if (input && preview) {
        input.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (fallback) fallback.style.display = 'none';
            };
            reader.readAsDataURL(file);
        });
    }
</script>
@endsection
