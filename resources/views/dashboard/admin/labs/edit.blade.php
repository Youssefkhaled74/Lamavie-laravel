@extends('dashboard.admin.layouts.main')

@section('content')
<style>
    .lab-edit{
        --radius: 16px;
        --border: rgba(15,23,42,.08);
        --shadow: 0 14px 40px rgba(2,6,23,.08);
        --muted: rgba(15,23,42,.65);
        --primary: #2563eb;
    }

    .lab-edit .hero{
        background:
            radial-gradient(900px 400px at 0% 0%, rgba(37,99,235,.18), transparent 60%),
            linear-gradient(180deg,#ffffff,#f6f8ff);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: var(--shadow);
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        gap: 16px;
        flex-wrap: wrap;
    }

    .lab-edit .hero h1{
        font-weight: 800;
        letter-spacing: -0.02em;
        margin: 0;
        display:flex;
        align-items:center;
        gap: 12px;
    }

    .lab-edit .hero .icon{
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: rgba(37,99,235,.12);
        display:inline-flex;
        align-items:center;
        justify-content:center;
        color: var(--primary);
    }

    .lab-edit .hero p{
        margin: 6px 0 0;
        color: var(--muted);
    }

    .lab-edit .card-form{
        border-radius: var(--radius);
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        background: #fff;
        padding: 20px;
    }

    .lab-edit .form-label{
        font-weight: 700;
        font-size: .9rem;
        color: #0f172a;
    }

    .lab-edit .input-group-text{
        background: rgba(37,99,235,.08);
        border: 1px solid var(--border);
        color: var(--primary);
        border-radius: 12px 0 0 12px;
    }

    .lab-edit .form-control{
        border: 1px solid var(--border);
        border-left: 0;
        border-radius: 0 12px 12px 0;
        padding: 10px 12px;
    }

    .lab-edit textarea.form-control{
        border-radius: 12px;
        border-left: 1px solid var(--border);
    }

    .lab-edit .hint{
        font-size: .85rem;
        color: var(--muted);
        margin-top: 6px;
    }

    .lab-edit .alert{
        border-radius: 14px;
    }

    .lab-edit .btn-primary{
        background: linear-gradient(90deg,#2563eb,#3b82f6);
        border: none;
        font-weight: 800;
        border-radius: 12px;
        padding: 10px 16px;
        box-shadow: 0 10px 24px rgba(37,99,235,.25);
    }

    .lab-edit .btn-outline-secondary{
        border-radius: 12px;
        font-weight: 800;
    }

    .lab-edit .actions{
        display:flex;
        gap: 10px;
        margin-top: 12px;
        flex-wrap: wrap;
    }
</style>

<div class="lab-edit container py-4">

    {{-- HERO --}}
    <div class="hero">
        <div>
            <h1>
                <span class="icon"><i class="fas fa-edit"></i></span>
                Edit Lab
            </h1>
            <p class="mb-0">Update lab account and contact information.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.labs.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Labs
            </a>
        </div>
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

        <form method="post" action="{{ route('admin.labs.update', $lab->id) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Lab Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $lab->name) }}" required>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $lab->email) }}">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="hint">Leave blank to keep current password.</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $lab->phone) }}">
                    </div>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="3">{{ old('address', $lab->address) }}</textarea>
                </div>
            </div>

            <div class="actions">
                <button class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Lab
                </button>
                <a href="{{ route('admin.labs.index') }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
