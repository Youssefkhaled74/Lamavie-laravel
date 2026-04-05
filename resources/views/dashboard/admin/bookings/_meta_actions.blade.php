{{-- resources/views/dashboard/admin/bookings/_meta_actions.blade.php --}}

@php
    $user = data_get($booking, 'user');

    $userName = data_get($user, 'name', '');
    $initials = collect(preg_split('/\s+/', trim($userName)))
        ->filter()
        ->map(fn($p) => mb_substr($p, 0, 1))
        ->take(2)
        ->join('');

    // Prefer canonical `profile_photo` stored on public disk
    $photoPath = data_get($user, 'profile_photo');
    $photoUrl = null;
    if ($photoPath) {
        $clean = ltrim($photoPath, '/');
        try {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($clean)) {
                $photoUrl = asset('storage/' . $clean);
            }
        } catch (\Throwable $e) {
            $photoUrl = null;
        }
    }

    $lab = data_get($booking, 'lab');
    $driver = data_get($booking, 'driver');

    $payName = data_get($booking, 'paymentMethod.name.' . app()->getLocale())
        ?? data_get($booking, 'paymentMethod.name.en')
        ?? data_get($booking, 'paymentMethod.name')
        ?? '—';

    $status = strtolower(data_get($booking, 'status', 'pending'));
    $statusUi = match($status){
        'completed' => ['text'=>'Completed', 'cls'=>'bg-success-subtle text-success border-success-subtle'],
        'cancelled','canceled' => ['text'=>'Cancelled', 'cls'=>'bg-danger-subtle text-danger border-danger-subtle'],
        'in_progress' => ['text'=>'In progress', 'cls'=>'bg-info-subtle text-info border-info-subtle'],
        'pickup','picked_up' => ['text'=>'Pickup', 'cls'=>'bg-warning-subtle text-warning border-warning-subtle'],
        default => ['text'=>ucfirst($status), 'cls'=>'bg-warning-subtle text-warning border-warning-subtle'],
    };

    $createdAt = data_get($booking, 'created_at');
    $updatedAt = data_get($booking, 'updated_at');

    $rawPayload = data_get($booking, 'payload_data');
    if (is_array($rawPayload)) {
        $payload = $rawPayload;
    } elseif (is_string($rawPayload)) {
        $payload = json_decode($rawPayload, true) ?? [];
    } elseif (is_object($rawPayload)) {
        $payload = (array) $rawPayload;
    } else {
        $payload = [];
    }

    $paymentProofPath =
        data_get($payload, 'photo')
        ?? data_get($payload, 'payment_photo')
        ?? data_get($payload, 'instapay_photo')
        ?? data_get($payload, 'instapay_image')
        ?? data_get($payload, 'receipt_photo')
        ?? data_get($payload, 'receipt_image');

    $paymentProofUrl = null;
    if (is_string($paymentProofPath) && trim($paymentProofPath) !== '') {
        $candidate = trim($paymentProofPath);
        if (\Illuminate\Support\Str::startsWith($candidate, ['http://', 'https://'])) {
            $paymentProofUrl = $candidate;
        } elseif (\Illuminate\Support\Str::startsWith($candidate, '/storage/')) {
            $paymentProofUrl = asset(ltrim($candidate, '/'));
        } elseif (\Illuminate\Support\Str::startsWith($candidate, 'storage/')) {
            $paymentProofUrl = asset($candidate);
        } else {
            $paymentProofUrl = asset('storage/' . ltrim($candidate, '/'));
        }
    }
@endphp

<style>
    /* Right side modern cards */
    .meta-card {
        border: 1px solid rgba(15,23,42,0.06);
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 10px 30px rgba(2, 6, 23, 0.06);
        overflow: hidden;
    }
    .meta-card-header {
        padding: 16px 16px 12px 16px;
        background: linear-gradient(180deg, rgba(13,110,253,0.08), rgba(255,255,255,0));
        border-bottom: 1px solid rgba(15,23,42,0.06);
        display:flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
    }
    .meta-title {
        display:flex;
        gap: 12px;
        align-items:center;
        min-width: 0;
    }
    .meta-icon {
        width: 44px; height: 44px;
        border-radius: 14px;
        display:flex; align-items:center; justify-content:center;
        background: linear-gradient(135deg,#0d6efd,#6ea8fe);
        color:#fff;
        flex: 0 0 auto;
        box-shadow: 0 8px 18px rgba(13,110,253,0.22);
    }
    .meta-h {
        font-weight: 800;
        font-size: 1.2rem;
        margin: 0;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .meta-sub {
        margin: 2px 0 0 0;
        color: #64748b;
        font-size: .92rem;
    }
    .meta-body { padding: 14px 16px 16px 16px; }

    .meta-row {
        display:flex;
        gap: 12px;
        align-items: center;
        padding: 10px 10px;
        border-radius: 14px;
        border: 1px solid rgba(15,23,42,0.06);
        background: #ffffff;
        margin-bottom: 10px;
    }
    .meta-row i { width: 22px; text-align:center; color:#0d6efd; }
    .meta-k { font-size:.82rem; color:#64748b; margin-bottom:2px; }
    .meta-v { font-weight:700; color:#0f172a; }

    .avatar {
        width: 48px; height: 48px;
        border-radius: 16px;
        overflow:hidden;
        background: #f1f5f9;
        border: 1px solid rgba(15,23,42,0.06);
        display:flex; align-items:center; justify-content:center;
        flex: 0 0 auto;
    }
    .avatar img { width:100%; height:100%; object-fit:cover; }
    .avatar span {
        font-weight: 900;
        color:#334155;
        font-size: 1rem;
    }
    .chip {
        display:inline-flex;
        align-items:center;
        gap: 6px;
        border-radius: 999px;
        padding: 6px 10px;
        font-weight: 700;
        font-size: .85rem;
        border: 1px solid rgba(15,23,42,0.08);
        background:#fff;
        white-space: nowrap;
    }
    .actions-grid {
        display:grid;
        grid-template-columns: 1fr;
        gap: 10px;
        margin-top: 12px;
    }
    .btn-soft {
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap: 8px;
        border-radius: 14px;
        padding: 12px 14px;
        font-weight: 800;
        border: 1px solid rgba(15,23,42,0.08);
        transition: transform .08s ease, box-shadow .15s ease, background .15s ease;
        text-decoration:none !important;
    }
    .btn-soft:hover { transform: translateY(-1px); box-shadow: 0 10px 24px rgba(2,6,23,0.08); }
    .btn-soft-primary { background: linear-gradient(135deg,#0d6efd,#6ea8fe); color:#fff; border-color: transparent; }
    .btn-soft-outline { background:#fff; color:#0f172a; }
    .btn-soft-danger { background: linear-gradient(135deg,#ef4444,#fb7185); color:#fff; border-color: transparent; }

    .mini-card {
        border: 1px solid rgba(15,23,42,0.06);
        border-radius: 18px;
        background:#fff;
        box-shadow: 0 10px 30px rgba(2,6,23,0.05);
        overflow:hidden;
        margin-top: 14px;
    }
    .mini-head {
        padding: 14px 16px;
        display:flex;
        gap: 12px;
        align-items:center;
        border-bottom: 1px solid rgba(15,23,42,0.06);
        background: linear-gradient(180deg, rgba(13,110,253,0.06), rgba(255,255,255,0));
    }
    .mini-body { padding: 14px 16px; }
    .mini-icon {
        width: 40px; height: 40px; border-radius: 14px;
        display:flex; align-items:center; justify-content:center;
        background: #eef2ff;
        color:#4f46e5;
        flex: 0 0 auto;
    }
    .mini-icon.lab { background:#ecfeff; color:#0891b2; }
    .mini-icon.driver { background:#ecfdf5; color:#059669; }
    .mini-title { font-weight: 900; color:#0f172a; margin:0; font-size:1.05rem; }
    .mini-sub { margin:2px 0 0 0; color:#64748b; font-size:.9rem; }
    .mini-actions { margin-left:auto; }
    .mini-actions a { border-radius: 12px; padding: 8px 10px; }
</style>

<div class="meta-card">
    <div class="meta-card-header">
        <div class="meta-title">
            <div class="meta-icon"><i class="fas fa-sliders-h"></i></div>
            <div style="min-width:0">
                <p class="meta-h">Manage Booking</p>
                <p class="meta-sub">Quick actions and metadata</p>
            </div>
        </div>

        <div class="d-flex flex-column align-items-end gap-2">
            <span class="chip {{ $statusUi['cls'] }}">
                <i class="fas fa-circle" style="font-size:8px;"></i>
                {{ $statusUi['text'] }}
            </span>
            @if(data_get($booking,'order_number'))
                <span class="chip">
                    <i class="fas fa-hashtag"></i> {{ data_get($booking,'order_number') }}
                </span>
            @endif
        </div>
    </div>

    <div class="meta-body">
        {{-- Customer --}}
        <div class="meta-row">
            <div class="avatar" title="{{ data_get($user,'name','') }}">
                @if($photoUrl)
                    <img src="{{ $photoUrl }}" alt="avatar">
                @else
                    <span>{{ $initials ?: '?' }}</span>
                @endif
            </div>
            <div style="min-width:0">
                <div class="meta-k">Customer</div>
                <div class="meta-v" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    {{ data_get($user,'name','N/A') }}
                </div>
                <div class="meta-k" style="margin-top:2px;">{{ data_get($user,'phone','') }}</div>
            </div>
        </div>

        {{-- Payment --}}
        <div class="meta-row">
            <i class="fas fa-credit-card"></i>
            <div>
                <div class="meta-k">Payment</div>
                <div class="meta-v">{{ $payName }}</div>
            </div>
        </div>

        @if($paymentProofUrl)
            <div class="meta-row" style="align-items:flex-start;">
                <i class="fas fa-image"></i>
                <div style="width:100%;">
                    <div class="meta-k">Payment Proof</div>
                    <a href="{{ $paymentProofUrl }}" target="_blank" rel="noopener noreferrer">
                        <img
                            src="{{ $paymentProofUrl }}"
                            alt="Payment proof"
                            style="width:100%; max-height:220px; object-fit:cover; border-radius:12px; border:1px solid rgba(15,23,42,0.08); margin-top:6px;"
                        >
                    </a>
                </div>
            </div>
        @endif

        {{-- Created --}}
        <div class="meta-row">
            <i class="fas fa-calendar-alt"></i>
            <div>
                <div class="meta-k">Created</div>
                <div class="meta-v">
                    @if($createdAt)
                        {{ \Carbon\Carbon::parse($createdAt)->format('d M Y, H:i') }}
                    @else
                        —
                    @endif
                </div>
            </div>
        </div>

        {{-- Updated --}}
        <div class="meta-row">
            <i class="fas fa-clock"></i>
            <div>
                <div class="meta-k">Updated</div>
                <div class="meta-v">
                    @if($updatedAt)
                        {{ \Carbon\Carbon::parse($updatedAt)->format('d M Y, H:i') }}
                    @else
                        —
                    @endif
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="actions-grid">
            <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn-soft btn-soft-primary">
                <i class="fas fa-edit"></i> Update
            </a>

            <button type="button" class="btn-soft btn-soft-outline" data-bs-toggle="modal" data-bs-target="#invoiceModal">
                <i class="fas fa-file-invoice"></i> Invoice
            </button>

            <a href="{{ route('admin.bookings.index') }}" class="btn-soft btn-soft-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <button type="button" class="btn-soft btn-soft-outline" data-bs-toggle="modal" data-bs-target="#notifyModal">
                <i class="fas fa-bell"></i> Send Notification
            </button>
        </div>
    </div>
</div>

{{-- Assigned Lab --}}
@if($lab)
    <div class="mini-card">
        <div class="mini-head">
            <div class="mini-icon lab"><i class="fas fa-flask"></i></div>
            <div style="min-width:0">
                <p class="mini-title">Assigned Lab</p>
                <p class="mini-sub">Lab handling this booking</p>
            </div>
            <div class="mini-actions">
                @if(data_get($lab,'id'))
                    <a href="{{ route('admin.labs.show', data_get($lab,'id')) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i>
                    </a>
                @endif
            </div>
        </div>
        <div class="mini-body">
            <div class="meta-v">{{ data_get($lab,'name','—') }}</div>
            <div class="meta-k mt-1">{{ data_get($lab,'phone','') }}</div>
            @if(data_get($lab,'address'))
                <div class="meta-k mt-2" style="line-height:1.3">{{ data_get($lab,'address') }}</div>
            @endif
        </div>
    </div>
@endif

{{-- Assigned Driver removed per UX request --}}
