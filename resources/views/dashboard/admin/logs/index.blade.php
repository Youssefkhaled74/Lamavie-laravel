@extends('dashboard.admin.layouts.main')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="content-header fade-in py-4 px-3 mb-4 rounded shadow-sm"
         style="background: linear-gradient(90deg, #e3f2fd 0%, #f8fafc 100%); border-left: 6px solid #0d6efd;">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h1 class="fw-bold text-primary mb-1" style="font-size:2.1rem; letter-spacing:.5px;"
                    data-en="System Logs" data-ar="سجل النظام">System Logs</h1>
                <p class="text-muted mb-0" style="font-size:1.05rem;"
                   data-en="Recent authentication and system events."
                   data-ar="أحدث أحداث تسجيل الدخول وأحداث النظام.">
                    Recent authentication and system events.
                </p>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary" type="button" id="toggleFilters">
                    <i class="fas fa-filter me-2"></i><span data-en="Filters" data-ar="الفلاتر">Filters</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Styles --}}
    <style>
        .actor-badge {
            display:inline-flex; align-items:center; justify-content:center;
            width:36px; height:36px; border-radius:10px; color:#fff;
            margin-right:10px; box-shadow: 0 6px 18px rgba(15,23,42,.08);
        }
        .actor-badge.admin { background: linear-gradient(90deg,#1e3a8a,#2563eb); }
        .actor-badge.lab { background: linear-gradient(90deg,#7c3aed,#a78bfa); }
        .actor-badge.driver { background: linear-gradient(90deg,#059669,#34d399); }
        .actor-badge.system { background: linear-gradient(90deg,#0f172a,#64748b); }

        .actor-label { font-weight:700; }
        .actor-meta { color:#64748b; font-size:.9rem; }

        .table thead th { font-weight:800; }
        .table td, .table th { vertical-align: middle !important; }
        .log-row { cursor: pointer; }
        .log-row:hover { background: #f8fafc; }

        .chip {
            display:inline-flex; align-items:center; gap:8px;
            padding:.35rem .65rem; border-radius:999px;
            background:#fff; border:1px solid #e5e7eb;
            color:#0f172a; font-size:.85rem;
        }
        .chip i { opacity:.7; }

        .event-badge { font-weight:700; }
        .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
        pre.json-pre {
            background:#0b1220; color:#e5e7eb;
            padding:14px; border-radius:12px;
            max-height:360px; overflow:auto;
            border:1px solid rgba(148,163,184,.25);
        }

        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 .25rem rgba(13,110,253,.18);
            border-color:#0d6efd;
        }
    </style>

    {{-- Main card --}}
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header py-3 px-3 d-flex justify-content-between align-items-center text-white"
             style="background: linear-gradient(90deg, #0d6efd 0%, #6ea8fe 100%);">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-clipboard-list"></i>
                <h5 class="mb-0" data-en="Logs" data-ar="السجلات">Logs</h5>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark border">
                    {{ $logs->total() }} <span data-en="events" data-ar="حدث">events</span>
                </span>
            </div>
        </div>

        <div class="card-body p-3">

            {{-- Filters --}}
            <div class="border rounded-4 p-3 mb-3 bg-white" id="filtersBox">
                <form class="row g-2 align-items-end" method="GET">
                    <div class="col-12 col-md-2">
                        <label class="form-label small mb-1" data-en="Actor type" data-ar="نوع المستخدم">Actor type</label>
                        <select name="actor_type" class="form-select">
                            <option value="" data-en="All actors" data-ar="كل الأنواع">All actors</option>
                            <option value="admin" {{ request('actor_type')=='admin'?'selected':'' }}>Admin</option>
                            <option value="lab" {{ request('actor_type')=='lab'?'selected':'' }}>Lab</option>
                            <option value="driver" {{ request('actor_type')=='driver'?'selected':'' }}>Driver</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-2">
                        <label class="form-label small mb-1" data-en="Event type" data-ar="نوع الحدث">Event type</label>
                        <select name="event_type" class="form-select">
                            <option value="" data-en="All event types" data-ar="كل الأحداث">All event types</option>
                            <option value="auth" {{ request('event_type')=='auth'?'selected':'' }}>Auth</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label small mb-1" data-en="Search" data-ar="بحث">Search</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                                   placeholder="Search email or text..."
                                   data-en-placeholder="Search email or text..."
                                   data-ar-placeholder="ابحث بالبريد أو النص...">
                        </div>
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label small mb-1" data-en="From" data-ar="من">From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label small mb-1" data-en="To" data-ar="إلى">To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>

                    <div class="col-12 col-md-1 d-flex gap-2">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="fas fa-filter me-1"></i>
                            <span data-en="Go" data-ar="تطبيق">Go</span>
                        </button>
                    </div>

                    <div class="col-12 col-md-12 d-flex justify-content-between align-items-center mt-1 flex-wrap gap-2">
                        <div class="d-flex gap-2 flex-wrap">
                            @if(request('actor_type'))
                                <span class="chip"><i class="fas fa-user"></i> {{ request('actor_type') }}</span>
                            @endif
                            @if(request('event_type'))
                                <span class="chip"><i class="fas fa-bolt"></i> {{ request('event_type') }}</span>
                            @endif
                            @if(request('q'))
                                <span class="chip"><i class="fas fa-search"></i> {{ request('q') }}</span>
                            @endif
                            @if(request('date_from') || request('date_to'))
                                <span class="chip"><i class="fas fa-calendar"></i> {{ request('date_from') ?? '—' }} → {{ request('date_to') ?? '—' }}</span>
                            @endif
                        </div>

                        <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-rotate-left me-1"></i><span data-en="Reset" data-ar="إعادة ضبط">Reset</span>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:80px" data-en="#" data-ar="#">#</th>
                            <th style="width:190px" data-en="When" data-ar="الوقت">When</th>
                            <th data-en="Actor" data-ar="الفاعل">Actor</th>
                            <th data-en="Event" data-ar="الحدث">Event</th>
                            <th style="width:140px" class="text-end" data-en="Actions" data-ar="إجراءات">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            @php
                                $payload = is_array($log->payload) ? $log->payload : (is_string($log->payload) ? json_decode($log->payload, true) ?? [] : []);
                                $actorEmail = $payload['email'] ?? $log->actor_name ?? 'System';

                                $eventDesc = $payload['description'] ?? null;
                                if (!$eventDesc) {
                                    if ($log->event_type === 'auth') {
                                        switch ($log->event_subtype) {
                                            case 'login_attempt': $eventDesc = 'Login attempt'; break;
                                            case 'login_success': $eventDesc = 'Login successful'; break;
                                            case 'login_failed': $eventDesc = 'Login failed'; break;
                                            default: $eventDesc = 'Authentication event';
                                        }
                                    } else {
                                        $eventDesc = $log->event_type . ($log->event_subtype ? '/'.$log->event_subtype : '');
                                    }
                                }

                                $atype = $log->actor_type ?? 'system';
                                $icon = 'fa-user';
                                if ($atype === 'admin') $icon = 'fa-user-shield';
                                if ($atype === 'lab') $icon = 'fa-flask';
                                if ($atype === 'driver') $icon = 'fa-car';
                                if ($atype === 'system') $icon = 'fa-gear';

                                // quick status badge for auth
                                $eventBadge = null;
                                if ($log->event_type === 'auth') {
                                    if ($log->event_subtype === 'login_success') $eventBadge = ['Success','success'];
                                    elseif ($log->event_subtype === 'login_failed') $eventBadge = ['Failed','danger'];
                                    else $eventBadge = ['Auth','primary'];
                                }

                                $rowData = [
                                    'id' => $log->id,
                                    'when' => $log->created_at->format('Y-m-d H:i:s'),
                                    'actor_type' => $atype,
                                    'actor_id' => $log->actor_id,
                                    'actor_name' => $log->actor_name,
                                    'actor_email' => $actorEmail,
                                    'event' => $eventDesc,
                                    'event_type' => $log->event_type,
                                    'event_subtype' => $log->event_subtype,
                                    'ip' => $log->ip_address,
                                    'user_agent' => $log->user_agent,
                                    'payload' => $payload,
                                ];
                            @endphp

                            <tr class="log-row" data-log='@json($rowData)'>
                                <td class="text-muted fw-semibold">{{ $log->id }}</td>

                                <td class="mono text-muted">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>

                                <td>
                                    <div class="d-flex align-items-start">
                                        <span class="actor-badge {{ e($atype) }}" title="{{ ucfirst($atype) }}">
                                            <i class="fas {{ $icon }}"></i>
                                        </span>
                                        <div>
                                            <div class="actor-label">
                                                {{ ucfirst($atype ?? 'system') }}{{ $log->actor_id ? ' #'.$log->actor_id : '' }}
                                            </div>
                                            <div class="actor-meta">{{ $actorEmail }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="fw-semibold">{{ $eventDesc }}</div>
                                    <div class="text-muted small">
                                        <span class="mono">{{ $log->event_type }}</span>
                                        @if($log->event_subtype)
                                            <span class="mono">/ {{ $log->event_subtype }}</span>
                                        @endif
                                        @if($eventBadge)
                                            <span class="badge bg-{{ $eventBadge[1] }} ms-2 event-badge">{{ $eventBadge[0] }}</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="text-end">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary rounded-pill px-3 open-log"
                                            data-bs-toggle="modal" data-bs-target="#logDetailModal">
                                        <i class="fas fa-eye me-1"></i>
                                        <span data-en="View" data-ar="عرض">View</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <div data-en="No logs found." data-ar="لا توجد سجلات.">No logs found.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                <div class="text-muted">
                    <span data-en="Showing" data-ar="عرض">Showing</span>
                    <span class="fw-semibold">{{ $logs->firstItem() ?: 0 }}</span>
                    <span data-en="to" data-ar="إلى">to</span>
                    <span class="fw-semibold">{{ $logs->lastItem() ?: 0 }}</span>
                    <span data-en="of" data-ar="من">of</span>
                    <span class="fw-semibold">{{ $logs->total() }}</span>
                    <span data-en="results" data-ar="نتيجة">results</span>
                </div>
                <div>{!! $logs->links('pagination::bootstrap-5') !!}</div>
            </div>

        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="logDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 overflow-hidden">
                <div class="modal-header text-white"
                     style="background: linear-gradient(90deg, #0d6efd 0%, #6ea8fe 100%);">
                    <div>
                        <h5 class="modal-title mb-0" id="logDetailModalLabel" data-en="Log details" data-ar="تفاصيل السجل">Log details</h5>
                        <div class="small opacity-75 mono" id="logDetailSub"></div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <ul class="nav nav-pills mb-3" id="logTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-overview" type="button">
                                <i class="fas fa-circle-info me-1"></i><span data-en="Overview" data-ar="ملخص">Overview</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-payload" type="button">
                                <i class="fas fa-code me-1"></i><span data-en="Payload" data-ar="البيانات">Payload</span>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab-overview">
                            <div id="logDetailContent"></div>
                        </div>

                        <div class="tab-pane fade" id="tab-payload">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="text-muted small" data-en="Raw JSON payload" data-ar="بيانات JSON الخام">Raw JSON payload</div>
                                <button class="btn btn-sm btn-outline-light text-dark border" id="copyPayloadBtn" type="button">
                                    <i class="fas fa-copy me-1"></i><span data-en="Copy JSON" data-ar="نسخ JSON">Copy JSON</span>
                                </button>
                            </div>
                            <pre class="json-pre"><code id="payloadJson" class="mono"></code></pre>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <span data-en="Close" data-ar="إغلاق">Close</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Script --}}
<script>
(function(){
    const filtersBox = document.getElementById('filtersBox');
    document.getElementById('toggleFilters')?.addEventListener('click', function(){
        if (!filtersBox) return;
        filtersBox.style.display = (filtersBox.style.display === 'none') ? '' : 'none';
    });

    const modal = document.getElementById('logDetailModal');
    const content = document.getElementById('logDetailContent');
    const sub = document.getElementById('logDetailSub');
    const payloadEl = document.getElementById('payloadJson');
    const copyBtn = document.getElementById('copyPayloadBtn');

    let lastPayloadText = '';

    function escHtml(str){
        return String(str ?? '')
            .replaceAll('&','&amp;')
            .replaceAll('<','&lt;')
            .replaceAll('>','&gt;')
            .replaceAll('"','&quot;')
            .replaceAll("'","&#039;");
    }

    function buildOverviewTable(data){
        const actor = (data.actor_type ? (data.actor_type.charAt(0).toUpperCase()+data.actor_type.slice(1)) : 'System')
            + (data.actor_id ? (' #' + data.actor_id) : '');

        return `
            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="p-3 border rounded-4 bg-white">
                        <div class="fw-bold mb-2">Event</div>
                        <div class="fw-semibold">${escHtml(data.event)}</div>
                        <div class="text-muted small mono">${escHtml(data.event_type)}${data.event_subtype ? ' / ' + escHtml(data.event_subtype) : ''}</div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="p-3 border rounded-4 bg-white">
                        <div class="fw-bold mb-2">Actor</div>
                        <div class="fw-semibold">${escHtml(actor)}</div>
                        <div class="text-muted small">${escHtml(data.actor_name || '')} &lt;${escHtml(data.actor_email || '')}&gt;</div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="p-3 border rounded-4 bg-white">
                        <div class="fw-bold mb-2">When</div>
                        <div class="mono">${escHtml(data.when)}</div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="p-3 border rounded-4 bg-white">
                        <div class="fw-bold mb-2">IP</div>
                        <div class="mono">${escHtml(data.ip || '—')}</div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="p-3 border rounded-4 bg-white">
                        <div class="fw-bold mb-2">ID</div>
                        <div class="mono">${escHtml(data.id || '—')}</div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="p-3 border rounded-4 bg-white">
                        <div class="fw-bold mb-2">User Agent</div>
                        <div class="text-muted small" style="word-break:break-word;">${escHtml(data.user_agent || '—')}</div>
                    </div>
                </div>
            </div>
        `;
    }

    document.querySelectorAll('.open-log').forEach(function(btn){
        btn.addEventListener('click', function(){
            const tr = btn.closest('tr.log-row');
            if (!tr) return;

            const raw = tr.getAttribute('data-log') || '{}';
            let data = {};
            try { data = JSON.parse(raw); } catch(e) { data = { raw: raw }; }

            sub.textContent = `#${data.id || ''} • ${data.when || ''}`;
            content.innerHTML = buildOverviewTable(data);

            lastPayloadText = JSON.stringify(data.payload || {}, null, 2);
            payloadEl.textContent = lastPayloadText;
        });
    });

    copyBtn?.addEventListener('click', async function(){
        try {
            await navigator.clipboard.writeText(lastPayloadText || '{}');
            if (typeof showCustomAlert !== 'undefined') showCustomAlert('Copied!');
            else alert('Copied!');
        } catch(e) {
            alert('Copy failed');
        }
    });

    // Optional: click row to open modal
    document.querySelectorAll('tr.log-row').forEach(function(tr){
        tr.addEventListener('click', function(e){
            if (e.target.closest('button, a, input, label, select')) return;
            const openBtn = tr.querySelector('.open-log');
            if (openBtn) openBtn.click();
        });
    });

})();
</script>
@endsection
