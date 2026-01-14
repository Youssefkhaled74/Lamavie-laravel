@extends('dashboard.admin.layouts.main')

@section('content')
@include('dashboard.admin.driver_vehicles._ui')

@php
    $vehicle = $vehicle ?? $driver_vehicle ?? null;
    $drivers = $vehicle->drivers ?? collect();
@endphp

<div class="container py-4">
    <div class="dv-page">

        <div class="dv-head">
            <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                <div>
                    <h3 class="dv-title">Vehicle Details</h3>
                    <p class="dv-sub">Plate, specs and assigned drivers.</p>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <span class="dv-chip"><i class="fas fa-id-card"></i>{{ $vehicle->plate_number }}</span>
                        <span class="dv-chip"><i class="fas fa-car-side"></i>{{ $vehicle->make }} {{ $vehicle->model }}</span>
                        <span class="dv-chip"><i class="fas fa-palette"></i>{{ $vehicle->color }}</span>
                        <span class="dv-chip"><i class="fas fa-users"></i>{{ $drivers->count() }} drivers</span>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.driver-vehicles.edit', $vehicle->id) }}" class="btn btn-primary dv-btn">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <a href="{{ route('admin.driver-vehicles.index') }}" class="btn btn-outline-secondary dv-btn">
                        <i class="fas fa-list me-2"></i>All Vehicles
                    </a>
                </div>
            </div>
        </div>

        <div class="dv-card p-3 p-md-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="text-muted fw-bold mb-2">Specs</h6>

                    <div class="mb-2"><strong>Plate:</strong> {{ $vehicle->plate_number }}</div>
                    <div class="mb-2"><strong>Make / Model:</strong> {{ $vehicle->make }} {{ $vehicle->model }}</div>
                    <div class="mb-2"><strong>Color:</strong> {{ $vehicle->color }}</div>
                    <div class="mb-2"><strong>Capacity:</strong> {{ $vehicle->capacity }}</div>

                    <div class="mt-3">
                        <form action="{{ route('admin.driver-vehicles.destroy', $vehicle->id) }}" method="post" onsubmit="return confirm('Delete this vehicle?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger dv-btn">
                                <i class="fas fa-trash me-2"></i>Delete Vehicle
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted fw-bold mb-2">Assigned Drivers</h6>

                    @if($drivers->count())
                        <div class="list-group">
                            @foreach($drivers as $d)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">{{ $d->name }}</div>
                                        <div class="small text-muted">{{ $d->phone }} · {{ $d->email }}</div>
                                    </div>
                                    <a href="{{ route('admin.car-wash-drivers.show', $d->id) }}" class="btn btn-sm btn-outline-primary dv-btn">
                                        View
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-muted">No drivers assigned.</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Timeline --}}
        <div class="dv-card p-3 p-md-4 mt-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                <div>
                    <h5 class="mb-0 fw-bold">Vehicle Timeline</h5>
                    <div class="text-muted small">Availability for the next <span id="vt-hours-label">72</span> hours</div>
                </div>

                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <input type="date" id="vt-date" class="form-control form-control-sm" style="width:180px;" />
                    <select id="vt-hours" class="form-select form-select-sm" style="width:150px;">
                        <option value="24">24 hours</option>
                        <option value="48">48 hours</option>
                        <option value="72" selected>72 hours</option>
                        <option value="168">1 week</option>
                    </select>
                    <button id="vt-refresh" class="btn btn-sm btn-primary dv-btn">Refresh</button>
                </div>
            </div>

            <div id="vt-container" class="vt-mini">
                <div style="display:flex;align-items:flex-start;gap:12px;flex-wrap:wrap;">
                    <div style="min-width:220px">
                        <div class="fw-bold">{{ $vehicle->plate_number }}</div>
                        <div class="text-muted small">{{ $vehicle->make }} {{ $vehicle->model }}</div>
                        <div class="mt-2">
                            @foreach($drivers as $d)
                                <span class="badge bg-light text-dark me-1">{{ $d->name }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div style="flex:1; position:relative; min-width:260px;">
                        <div id="vt-timeline" class="timeline" data-start=""></div>
                        <div id="vt-marker" class="current-marker" style="left:0; display:none"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Booking modal --}}
    <div class="modal fade" id="vt-booking-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:18px; overflow:hidden;">
                <div class="modal-header" style="background:linear-gradient(90deg,#0d6efd,#6ea8fe); color:#fff;">
                    <h5 class="modal-title">Booking Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="vt-booking-body">
                    <div class="text-center text-muted">Loading...</div>
                </div>
                <div class="modal-footer">
                    <a id="vt-view-booking" class="btn btn-primary dv-btn" style="display:none">View Booking</a>
                    <button class="btn btn-outline-secondary dv-btn" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- KEEP YOUR EXISTING JS (as-is) --}}
    <script>
        (function(){
            const vehicleId = {{ $vehicle->id }};
            const timelineUrl = `{{ route('admin.admin.partials.car_timeline_data') }}`;
            let vtHours = parseInt(document.getElementById('vt-hours').value || 72, 10);
            let vtDateEl = document.getElementById('vt-date');
            let vtTimeline = document.getElementById('vt-timeline');
            let vtMarker = document.getElementById('vt-marker');
            let vtBody = document.getElementById('vt-booking-body');
            let vtViewBtn = document.getElementById('vt-view-booking');

            document.getElementById('vt-hours').addEventListener('change', ()=>{ vtHours = parseInt(document.getElementById('vt-hours').value,10); document.getElementById('vt-hours-label').textContent = vtHours; loadVT(); });
            document.getElementById('vt-refresh').addEventListener('click', ()=> loadVT());

            function escapeHtml(unsafe){ return String(unsafe||'').replace(/[&<>'"]/g, c=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;" }[c])); }

            function getHourAssignments(vehicle, hourIndex, now){
                const hourStart = new Date(now.getTime() + hourIndex*3600*1000);
                const hourEnd = new Date(hourStart.getTime() + 3600*1000);
                return (vehicle.assignments || []).filter(a=>{ const aStart = a.start_at?new Date(a.start_at):new Date(0); const aEnd = a.end_at?new Date(a.end_at):new Date(8640000000000000); return aStart < hourEnd && aEnd > hourStart; });
            }

            function renderVehicle(vehicle, startNow, hours){
                vtTimeline.innerHTML = '';
                vtTimeline.dataset.start = startNow.toISOString();
                for(let i=0;i<hours;i++){
                    const segStart = new Date(startNow.getTime() + i*3600*1000);
                    const segEnd = new Date(segStart.getTime() + 3600*1000);
                    const assignments = getHourAssignments(vehicle,i,startNow);
                    let status='free';
                    if(assignments.length>0){
                        const coversFull = assignments.some(a=>{ const aStart = a.start_at?new Date(a.start_at):new Date(0); const aEnd = a.end_at?new Date(a.end_at):new Date(8640000000000000); return aStart <= segStart && aEnd >= segEnd; });
                        status = coversFull ? 'busy' : 'partial';
                    }
                    const div = document.createElement('div');
                    div.className = `segment ${status}`;
                    div.dataset.start = segStart.toISOString();
                    div.dataset.end = segEnd.toISOString();
                    div.dataset.assignments = JSON.stringify(assignments);
                    div.title = segStart.toLocaleString() + (assignments.length ? ` - ${assignments.length} booking(s)` : '');
                    div.innerHTML = `${segStart.getHours()===0? '<small>'+segStart.toLocaleDateString()+'</small>' : '<small>'+segStart.toLocaleTimeString([], {hour:'2-digit'}) + '</small>'}`;
                    if(assignments.length) {
                        const badge = document.createElement('span'); badge.className='seg-badge'; badge.textContent = assignments.length; div.appendChild(badge);
                    }
                    div.addEventListener('click', ()=> onSegClick(div));
                    vtTimeline.appendChild(div);
                }
                updateMarker(hours);
            }

            function updateMarker(hours){
                const startIso = vtTimeline.dataset.start;
                if(!startIso){ vtMarker.style.display='none'; return; }
                const start = new Date(startIso);
                const now = new Date();
                const elapsedMs = now - start;
                const totalMs = hours * 3600 * 1000;
                const frac = Math.max(0, Math.min(1, elapsedMs/totalMs));
                vtMarker.style.left = (frac*100) + '%';
                vtMarker.style.display = (elapsedMs>=0 && elapsedMs<=totalMs) ? 'block':'none';
            }

            function onSegClick(el){
                const assignments = el.dataset.assignments ? JSON.parse(el.dataset.assignments) : [];
                const modal = new bootstrap.Modal(document.getElementById('vt-booking-modal'));
                vtViewBtn.style.display = 'none';
                if(assignments.length===0){
                    vtBody.innerHTML = `<div class="text-center py-4">
                        <i class="fas fa-check-circle text-success" style="font-size:3rem;"></i>
                        <h5 class="mt-3">Vehicle Available</h5>
                        <p class="text-muted">This time slot is free for booking</p>
                    </div>`;
                    modal.show(); return;
                }
                const a = assignments[0];
                if(a.booking_id){
                    vtBody.innerHTML = '<div class="text-center"><div class="spinner-border text-primary"></div></div>';
                    fetch(`/admin/bookings/${a.booking_id}/json`,{headers:{'X-Requested-With':'XMLHttpRequest'}})
                        .then(r=>r.json())
                        .then(booking=>{
                            vtBody.innerHTML = `<div class="py-2">
                                <h6 class="mb-3"><i class="fas fa-receipt me-2"></i>Order #${escapeHtml(booking.order_number||'N/A')}</h6>
                                <div class="mb-2"><strong>Customer:</strong> ${escapeHtml(booking.user?.name||'N/A')}</div>
                                <div class="mb-2"><strong>Phone:</strong> ${escapeHtml(booking.user?.phone||'N/A')}</div>
                                <div class="mb-2"><strong>Total:</strong> ${escapeHtml(booking.total||'0')}</div>
                            </div>`;
                            vtViewBtn.href = `/admin/bookings/${booking.id}`;
                            vtViewBtn.style.display='inline-block';
                        })
                        .catch(()=>{ vtBody.innerHTML = '<div class="alert alert-danger">Failed to load booking details</div>'; });
                } else {
                    vtBody.innerHTML = `<div class="py-2">
                        <p><strong>Assignment ID:</strong> ${a.id}</p>
                        <p><strong>Start:</strong> ${new Date(a.start_at).toLocaleString()}</p>
                        <p><strong>End:</strong> ${a.end_at? new Date(a.end_at).toLocaleString() : 'Ongoing'}</p>
                    </div>`;
                }
                modal.show();
            }

            function loadVT(){
                vtTimeline.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>';
                let url = `${timelineUrl}?hours=${vtHours}`;
                if(vtDateEl && vtDateEl.value) url += `&date=${encodeURIComponent(vtDateEl.value)}`;
                fetch(url, {headers:{'X-Requested-With':'XMLHttpRequest'}})
                    .then(r=>r.json())
                    .then(data=>{
                        const startNow = vtDateEl && vtDateEl.value ? new Date(vtDateEl.value + 'T00:00:00') : new Date(data.now);
                        const vehicle = (data.vehicles || []).find(v=>String(v.id)===String(vehicleId));
                        if(!vehicle){ vtTimeline.innerHTML = '<div class="text-muted">No timeline data available for this vehicle.</div>'; return; }
                        renderVehicle(vehicle, startNow, vtHours);
                    })
                    .catch(()=>{ vtTimeline.innerHTML = '<div class="alert alert-danger">Failed to load timeline</div>'; });
            }

            loadVT();
            setInterval(()=>{ if(!vtDateEl.value) loadVT(); else updateMarker(vtHours); }, 30000);
        })();
    </script>
</div>
@endsection
