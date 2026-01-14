@extends('dashboard.admin.layouts.main')

@section('content')
<style>
    .page-shell{ max-width: 980px; margin: 0 auto; }
    .panel{
        background:#fff;
        border-radius:16px;
        box-shadow:0 12px 30px rgba(2,6,23,.08);
        border:1px solid rgba(15,23,42,.06);
        overflow:hidden;
    }
    .panel-header{
        background: linear-gradient(90deg, #0d6efd 0%, #6ea8fe 100%);
        color:#fff;
        padding:16px 18px;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
    }
    .panel-title{
        font-weight:900;
        margin:0;
        display:flex;
        align-items:center;
        gap:10px;
        letter-spacing:-.02em;
    }
    .panel-body{ padding:18px; }
    .form-label{ font-weight:800; color:#334155; }
    .form-control{
        border-radius:12px;
        border:1px solid rgba(15,23,42,.12);
        padding:10px 12px;
        box-shadow:none;
    }
    .form-control:focus{
        border-color: rgba(37,99,235,.55);
        box-shadow:0 0 0 .2rem rgba(37,99,235,.12);
    }
    .btn-soft{
        border-radius:12px;
        font-weight:900;
        padding:10px 14px;
    }
    .btn-primary{
        border:none;
        background: linear-gradient(90deg,#2563eb,#3b82f6);
        box-shadow:0 10px 24px rgba(37,99,235,.18);
    }
    .btn-outline-secondary{
        border-radius:12px;
        font-weight:900;
    }
</style>

<div class="content-header mb-3">
    <h1 class="fw-bold text-primary" data-en="Edit Driver" data-ar="تعديل السائق">Edit Driver</h1>
    <p class="text-muted mb-0" data-en="Update driver information." data-ar="تحديث بيانات السائق.">Update driver information.</p>
</div>

<div class="page-shell">
    <div class="panel">
        <div class="panel-header">
            <h5 class="panel-title">
                <i class="fas fa-user-edit"></i>
                <span data-en="Edit Driver" data-ar="تعديل السائق">Edit Driver</span>
            </h5>
            <a href="{{ route('admin.drivers.show', $driver) }}" class="btn btn-light btn-sm btn-soft">
                <i class="fas fa-eye me-1"></i>
                <span data-en="View" data-ar="عرض">View</span>
            </a>
        </div>

        <div class="panel-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.drivers.update', $driver) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" data-en="Name" data-ar="الاسم">Name</label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name', $driver->name) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" data-en="Email" data-ar="البريد الإلكتروني">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', $driver->email) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label"
                               data-en="Password (leave blank to keep current)"
                               data-ar="كلمة المرور (اتركها فارغة للاحتفاظ بالحالية)">
                            Password (leave blank to keep current)
                        </label>
                        <input type="password" name="password" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" data-en="Confirm Password" data-ar="تأكيد كلمة المرور">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-primary btn-soft">
                        <i class="fas fa-save me-1"></i>
                        <span data-en="Update Driver" data-ar="تحديث السائق">Update Driver</span>
                    </button>
                    <a href="{{ route('admin.drivers.index') }}" class="btn btn-outline-secondary btn-soft">
                        <span data-en="Cancel" data-ar="إلغاء">Cancel</span>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
