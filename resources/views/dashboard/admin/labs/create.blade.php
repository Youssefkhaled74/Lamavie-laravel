@extends('dashboard.admin.layouts.main')

@section('content')
<style>
    .lab-create {
        --radius: 16px;
        --border: rgba(15,23,42,.08);
        --shadow: 0 14px 40px rgba(2,6,23,.08);
        --muted: rgba(15,23,42,.65);
        --primary: #2563eb;
    }

    .lab-create .hero {
        background:
            radial-gradient(900px 400px at 0% 0%, rgba(37,99,235,.18), transparent 60%),
            linear-gradient(180deg,#ffffff,#f6f8ff);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: var(--shadow);
    }

    .lab-create .hero h1 {
        font-weight: 800;
        letter-spacing: -0.02em;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .lab-create .hero .icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: rgba(37,99,235,.12);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
    }

    .lab-create .hero p {
        margin: 6px 0 0;
        color: var(--muted);
    }

    .lab-create .card-form {
        border-radius: var(--radius);
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        padding: 20px;
        background: #fff;
    }

    .lab-create .form-group {
        margin-bottom: 16px;
    }

    .lab-create .form-label {
        font-weight: 700;
        font-size: 0.9rem;
        color: #0f172a;
    }

    .lab-create .input-group-text {
        background: rgba(37,99,235,.08);
        border: 1px solid var(--border);
        color: var(--primary);
        border-radius: 12px 0 0 12px;
    }

    .lab-create .form-control {
        border-radius: 0 12px 12px 0;
        border: 1px solid var(--border);
        padding: 10px 12px;
    }

    .lab-create textarea.form-control {
        border-radius: 12px;
    }

    .lab-create .alert {
        border-radius: 14px;
    }

    .lab-create .actions {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }

    .lab-create .btn-primary {
        background: linear-gradient(90deg,#2563eb,#3b82f6);
        border: none;
        font-weight: 800;
        border-radius: 12px;
        padding: 10px 16px;
        box-shadow: 0 10px 24px rgba(37,99,235,.25);
    }

    .lab-create .btn-outline-secondary {
        border-radius: 12px;
        font-weight: 700;
    }

    .lab-create .hint {
        font-size: 0.85rem;
        color: var(--muted);
        margin-top: 6px;
    }
</style>

<div class="lab-create container py-4">

    {{-- HERO --}}
    <div class="hero">
        <h1>
            <span class="icon"><i class="fas fa-flask"></i></span>
            Create New Lab
        </h1>
        <p>Add a new lab account and basic contact information.</p>
    </div>

    {{-- FORM --}}
    <div class="card-form">

        @if($errors->any())
            <div class="alert alert-danger">
                <strong><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="post" action="{{ route('admin.labs.store') }}">
            @csrf

            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="form-label">Lab Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                </div>

                <div class="col-md-6 form-group">
                    <label class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                    </div>
                </div>

                <div class="col-md-6 form-group">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="hint">Leave empty if password will be set later.</div>
                </div>

                <div class="col-md-6 form-group">
                    <label class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>

                <div class="col-md-6 form-group">
                    <label class="form-label">Phone</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                </div>

                <div class="col-md-12 form-group">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
                </div>
            </div>

            <div class="actions">
                <button class="btn btn-primary">
                    <i class="fas fa-check me-2"></i>Create Lab
                </button>
                <a href="{{ route('admin.labs.index') }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
