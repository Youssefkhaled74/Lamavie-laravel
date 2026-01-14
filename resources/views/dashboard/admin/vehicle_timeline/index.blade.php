@extends('dashboard.admin.layouts.main')

@section('content')
@php
    $hours = 72;
@endphp

<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');

  :root{
    --bg1:#f8fafc; --bg2:#eef2ff;
    --card:#fff;
    --text:#0f172a;
    --muted:rgba(15,23,42,.6);
    --border:rgba(15,23,42,.08);
    --shadow:0 14px 40px rgba(2,6,23,.08);
    --shadow-sm:0 8px 22px rgba(2,6,23,.06);
    --primary:#0b5ed7;
    --radius:14px;
  }

  /* Page shell */
  .vt-page{
    font-family:'Inter',system-ui,-apple-system,'Segoe UI',Roboto,'Helvetica Neue',Arial;
    background: radial-gradient(1200px 500px at 20% 0%, #e8f0ff 0%, transparent 60%),
                radial-gradient(900px 500px at 100% 30%, #e6fff7 0%, transparent 55%),
                linear-gradient(180deg,var(--bg1) 0%, var(--bg2) 100%);
    padding: 18px 0;
    min-height: calc(100vh - 120px);
  }

  .vt-card{
    background:var(--card);
    border-radius:var(--radius);
    border:1px solid var(--border);
    box-shadow:var(--shadow-sm);
  }

  .vt-small{ font-size:.88rem; color:#6b7280; }

  /* Header */
  .vt-header{
    display:flex;
    gap:1rem;
    align-items:flex-start;
    justify-content:space-between;
    padding: 14px 16px;
  }

  .vt-title{
    font-weight:800;
    font-size:1.15rem;
    color:var(--text);
    display:flex;
    gap:.75rem;
    align-items:flex-start;
  }

  .controls-toolbar{
    display:flex;
    gap:.5rem;
    align-items:center;
    flex-wrap:wrap;
    justify-content:flex-end;
  }

  .controls-toolbar .form-control,
  .controls-toolbar .form-select{
    height:36px;
    padding:.25rem .55rem;
    border-radius:10px;
  }

  @media(max-width:900px){
    .vt-header{ flex-direction:column; align-items:flex-start; gap:.75rem; }
    .controls-toolbar{ justify-content:flex-start; }
  }

  /* Stats */
  .vt-stats{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:12px;
    margin-top: 10px;
  }

  .vt-stat{
    display:flex;
    gap:12px;
    align-items:center;
    padding:14px;
    border-radius:12px;
    background: linear-gradient(180deg, rgba(99,102,241,.05), #fff);
    border:1px solid rgba(15,23,42,.06);
  }

  .vt-stat .icon{
    width:44px; height:44px; border-radius:12px;
    display:flex; align-items:center; justify-content:center;
    color:#fff;
  }

  .bg-grad-1{background:linear-gradient(90deg,#6366f1,#7c3aed);}
  .bg-grad-2{background:linear-gradient(90deg,#10b981,#34d399);}
  .bg-grad-3{background:linear-gradient(90deg,#ef4444,#f97316);}
  .bg-grad-4{background:linear-gradient(90deg,#06b6d4,#3b82f6);}

  .vt-stat .value{ font-weight:900; font-size:1.35rem; color:var(--text); line-height:1; }

  @media(max-width:1000px){
    .vt-stats{ grid-template-columns:repeat(2,1fr); }
  }

  /* Legend */
  .vt-legend{ display:flex; gap:14px; align-items:center; flex-wrap:wrap; }
  .legend-dot{ width:12px; height:12px; border-radius:4px; }

  /* Vehicle row */
  .vehicle-timeline-row{
    display:flex;
    gap:12px;
    padding: 10px;
    align-items:stretch;
    border-radius: 14px;
    margin-bottom: 12px;
    border:1px solid rgba(15,23,42,.06);
  }

  /* left info sticky */
  .vehicle-left{
    width:280px;
    min-width:220px;
    position: sticky;
    left: 0;
    align-self:stretch;
    z-index: 25;

    background: #fff;
    border-radius: 12px;
    padding: 10px;
    border:1px solid rgba(15,23,42,.06);
    box-shadow: 0 8px 18px rgba(2,6,23,.05);
  }

  .vehicle-plate{ font-weight:900; color:var(--text); font-size:1rem; display:flex; gap:.5rem; align-items:center; }
  .vehicle-meta{ color:#6b7280; font-size:.9rem; }
  .driver-badge{
    padding: .28rem .6rem;
    background: linear-gradient(90deg,#eef2ff,#e0f2fe);
    border-radius: 999px;
    font-weight: 700;
    font-size: .78rem;
    border:1px solid rgba(15,23,42,.06);
  }

  .vehicle-left img.thumb{
    width:46px; height:46px; border-radius:10px;
    object-fit:cover; border:1px solid rgba(15,23,42,.06);
  }

  @media(max-width:1000px){
    .vehicle-left{ width:200px; min-width:160px; }
  }

  /* Timeline container */
  .timeline-container{
    flex:1;
    overflow-x:auto;
    overflow-y:hidden;
    padding: 10px 12px;
    background: linear-gradient(180deg,#fbfdff,#fff);
    border-radius: 12px;
    border:1px solid rgba(15,23,42,.06);
    height: 92px;
    position:relative;
  }

  /* segments = heatmap */
  .segments{
    display:flex;
    gap:4px;
    align-items:center;
    height: 68px;
    position:relative;
    padding-bottom: 14px; /* space for ticks */
  }

  /* ✅ IMPORTANT: fixed width so it's visible */
  .segment{
    width: 18px;
    min-width: 18px;
    height: 52px;
    border-radius: 8px;
    cursor: pointer;
    position: relative;
    border: 1px solid rgba(2,6,23,.06);
    transition: transform .12s ease, box-shadow .12s ease;
  }
  .segment:hover{
    transform: translateY(-3px);
    box-shadow: 0 12px 26px rgba(2,6,23,.12);
    z-index: 5;
  }

  .segment.free{ background: linear-gradient(180deg,#ecfdf5,#bbf7d0); }
  .segment.partial{ background: linear-gradient(180deg,#fffbeb,#fef08a); }
  .segment.busy{ background: linear-gradient(180deg,#fff1f2,#fecaca); }

  .segment .seg-badge{
    position:absolute;
    top:6px;
    right:6px;
    background: rgba(255,255,255,.92);
    color:#0f172a;
    font-weight: 900;
    font-size: .72rem;
    padding: 2px 6px;
    border-radius: 999px;
    box-shadow: 0 2px 8px rgba(2,6,23,.08);
  }
  .segment.busy .seg-badge{ color:#7f1d1d; }
  .segment.partial .seg-badge{ color:#854d0e; }

  /* ticks */
  .segment .seg-time{
    position:absolute;
    bottom:-18px;
    left:50%;
    transform:translateX(-50%);
    font-size: 11px;
    font-weight: 800;
    color: rgba(15,23,42,.55);
    white-space:nowrap;
  }

  /* day separator small */
  .day-sep{
    flex: 0 0 auto;
    min-width: 52px;
    height: 52px;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-direction:column;
  }
  .day-badge{
    background:#f1f5f9;
    padding: 6px 8px;
    border-radius: 10px;
    font-weight: 900;
    font-size: 11px;
    color: #0f172a;
    border: 1px solid rgba(15,23,42,.06);
  }

  /* current marker */
  .current-time-marker{
    position:absolute;
    top:6px;
    bottom:6px;
    width: 2px;
    background: linear-gradient(180deg,#ef4444,#f97316);
    border-radius: 3px;
    z-index: 10;
    pointer-events:none;
    box-shadow: 0 0 0 3px rgba(239,68,68,.12);
  }

  /* Modal */
  .modal.vt-modal .modal-content{ border-radius:14px; overflow:hidden; }
  .modal.vt-modal .modal-header{ background:linear-gradient(90deg,#6366f1,#7c3aed); color:#fff; }
</style>

<script>
  // compact mode state
  let compactMode = false;
  function toggleCompactMode(){
    compactMode = !compactMode;
    const btn = document.getElementById('compactBtn');
    if(compactMode){
      document.querySelectorAll('.segment').forEach(el=>{ el.style.width='14px'; el.style.minWidth='14px'; el.style.height='44px'; });
      document.querySelectorAll('.vehicle-left').forEach(el=>{ el.style.width='190px'; el.style.minWidth='150px'; });
      btn.classList.add('active'); btn.setAttribute('aria-pressed','true');
    } else {
      document.querySelectorAll('.segment').forEach(el=>{ el.style.width='18px'; el.style.minWidth='18px'; el.style.height='52px'; });
      document.querySelectorAll('.vehicle-left').forEach(el=>{ el.style.width='280px'; el.style.minWidth='220px'; });
      btn.classList.remove('active'); btn.setAttribute('aria-pressed','false');
    }
  }
</script>

<div class="vt-page container-fluid">
  <div class="vt-card p-3">
    <div class="vt-header">
      <div class="vt-title">
        <span class="badge bg-primary" style="border-radius:10px;padding:.55rem .65rem;">
          <i class="fas fa-car-side"></i>
        </span>
        <div>
          <div>Vehicle Timeline</div>
          <div class="vt-small">Real-time vehicle availability & schedule</div>
        </div>
      </div>

      <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-end;">
        <div class="vt-small text-muted">Last updated: <span id="lastUpdated">—</span></div>

        <div class="controls-toolbar">
          <input type="date" id="dateSelect" class="form-control form-control-sm" style="width:170px;"
                 onchange="updateDate()" title="Select date" />
          <select id="hoursSelect" class="form-select form-select-sm" style="width:140px;" onchange="updateHours()">
            <option value="24">24 hours</option>
            <option value="48">48 hours</option>
            <option value="72" selected>72 hours</option>
            <option value="168">1 week</option>
          </select>

          <select id="vehicleSelect" class="form-select form-select-sm" style="width:220px;" onchange="onVehicleSelectChange()">
            <!-- populated dynamically -->
          </select>

          <button id="showAllBtn" class="btn btn-sm btn-outline-secondary" onclick="toggleShowAll()" aria-pressed="true" title="Show all vehicles">
            <i class="fas fa-list"></i>
          </button>

          <button id="compactBtn" class="btn btn-sm btn-outline-secondary" onclick="toggleCompactMode()" aria-pressed="false" title="Toggle compact view">
            <i class="fas fa-compress"></i>
          </button>

          <button id="refreshBtn" class="btn btn-sm btn-primary d-flex align-items-center" onclick="loadTimeline()">
            <span id="refreshIcon" class="me-2"><i class="fas fa-sync-alt"></i></span>
            <span id="refreshLabel">Refresh</span>
          </button>

          <button id="exportBtn" class="btn btn-sm btn-outline-primary d-flex align-items-center" onclick="exportTimeline()" title="Export timeline as Excel">
            <i class="fas fa-file-excel me-2"></i>
            <span>Export</span>
          </button>
        </div>
      </div>
    </div>

    <div class="p-3">
      <div id="stats-section" class="vt-stats">
        <div class="vt-stat">
          <div class="icon bg-grad-1"><i class="fas fa-car"></i></div>
          <div>
            <div class="value" id="totalVehicles">0</div>
            <div class="vt-small">Total Vehicles</div>
          </div>
        </div>
        <div class="vt-stat">
          <div class="icon bg-grad-2"><i class="fas fa-check"></i></div>
          <div>
            <div class="value" id="availableVehicles">0</div>
            <div class="vt-small">Available Now</div>
          </div>
        </div>
        <div class="vt-stat">
          <div class="icon bg-grad-3"><i class="fas fa-exclamation-triangle"></i></div>
          <div>
            <div class="value" id="busyVehicles">0</div>
            <div class="vt-small">Currently Busy</div>
          </div>
        </div>
        <div class="vt-stat">
          <div class="icon bg-grad-4"><i class="fas fa-receipt"></i></div>
          <div>
            <div class="value" id="totalBookings">0</div>
            <div class="vt-small">Active Bookings</div>
          </div>
        </div>
      </div>

      <div class="mt-3 vt-card p-3">
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
          <div class="vt-legend">
            <div class="d-flex align-items-center gap-2">
              <span class="legend-dot" style="background:linear-gradient(90deg,#10b981,#34d399)"></span>
              <small class="vt-small">Available</small>
            </div>
            <div class="d-flex align-items-center gap-2">
              <span class="legend-dot" style="background:linear-gradient(90deg,#ef4444,#f97316)"></span>
              <small class="vt-small">Busy</small>
            </div>
            <div class="d-flex align-items-center gap-2">
              <span class="legend-dot" style="background:linear-gradient(90deg,#f59e0b,#fbbf24)"></span>
              <small class="vt-small">Partial</small>
            </div>
          </div>
          <div class="vt-small text-muted">Tip: hover blocks for details, click to open booking</div>
        </div>

        <div id="timeline-content">
          <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
            <p class="mt-3 text-muted">Loading timeline data...</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Booking Details Modal -->
<div class="modal fade vt-modal" id="bookingModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Booking Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="bookingModalBody">
        <div class="text-center"><div class="spinner-border text-primary"></div><p class="mt-2">Loading...</p></div>
      </div>
      <div class="modal-footer">
        <a href="#" id="viewBookingBtn" class="btn btn-primary" style="display:none;">View Full Booking</a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
let currentHours = {{ $hours }};
let pollInterval = null;
let selectedDate = null;
let latestData = null;
let latestStartNow = null;
let showAll = true;     // ✅ better default UX
let focusVehicleId = null;

function escapeHtml(unsafe) {
  return String(unsafe || '').replace(/[&<>"']/g, function(m) {
    return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"}[m];
  });
}

function setLastUpdated(date){
  const el = document.getElementById('lastUpdated');
  if(!el) return;
  el.textContent = new Date(date).toLocaleString();
}

function setRefreshLoading(loading){
  const btn = document.getElementById('refreshBtn');
  const icon = document.getElementById('refreshIcon');
  const label = document.getElementById('refreshLabel');
  if(!btn) return;

  if(loading){
    btn.classList.add('disabled');
    icon.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    label.textContent = 'Refreshing';
  } else {
    btn.classList.remove('disabled');
    icon.innerHTML = '<i class="fas fa-sync-alt"></i>';
    label.textContent = 'Refresh';
  }
}

function updateHours(){
  currentHours = parseInt(document.getElementById('hoursSelect').value);
  loadTimeline();
}

function updateDate(){
  const el = document.getElementById('dateSelect');
  if(!el) return;
  selectedDate = el.value || null;
  loadTimeline();
}

function loadTimeline(){
  setRefreshLoading(true);
  const content = document.getElementById('timeline-content');
  content.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-3 text-muted">Loading timeline data...</p></div>';

  let url = `{{ route('admin.admin.partials.car_timeline_data') }}?hours=${currentHours}`;
  if(selectedDate){ url += `&date=${encodeURIComponent(selectedDate)}`; }

  fetch(url, { headers: {'X-Requested-With':'XMLHttpRequest'} })
    .then(r=>r.json())
    .then(data=>{
      const startNow = selectedDate ? new Date(selectedDate + 'T00:00:00') : new Date(data.now);

      setLastUpdated(selectedDate ? selectedDate : data.now);

      latestData = data;
      latestStartNow = startNow;

      populateVehicleSelect(data);
      renderTimeline(data, startNow);

      setRefreshLoading(false);
    })
    .catch(err=>{
      content.innerHTML = '<div class="alert alert-danger">Failed to load timeline data</div>';
      console.error(err);
      setRefreshLoading(false);
    });
}

function renderTimeline(data, startNow){
  const now = startNow ? new Date(startNow) : new Date(data.now);
  const content = document.getElementById('timeline-content');

  document.getElementById('stats-section').style.display = 'grid';
  document.getElementById('totalVehicles').textContent = data.vehicles.length;

  let availableNow=0, busyNow=0, totalBookings=0;
  let html = '';

  const vehiclesToRender = showAll
    ? data.vehicles
    : data.vehicles.filter(v => String(v.id) === String(focusVehicleId));

  vehiclesToRender.forEach(vehicle=>{
    const firstHourBusy = isHourBusy(vehicle,0,now,data.hours);
    if(firstHourBusy) busyNow++; else availableNow++;

    totalBookings += (vehicle.assignments || []).length;

    html += `
      <div class="vehicle-timeline-row vt-card" data-vehicle-id="${vehicle.id}">
        <div class="vehicle-left">
          <div class="d-flex align-items-center gap-2">
            <img src="${escapeHtml(vehicle.thumbnail || '/assets/images/vehicle-placeholder.png')}"
                 class="thumb" alt="vehicle"
                 onerror="this.style.display='none'">
            <div>
              <div class="vehicle-plate"><i class="fas fa-car-side text-primary"></i> ${escapeHtml(vehicle.plate_number)}</div>
              <div class="vehicle-meta">${escapeHtml(vehicle.make)} ${escapeHtml(vehicle.model)}</div>
            </div>
          </div>

          <div class="d-flex flex-wrap mt-2">
            ${(vehicle.drivers || []).map(d=>`
              <span class="driver-badge me-2">
                <i class="fas fa-user me-1"></i>${escapeHtml(d)}
              </span>
            `).join('')}
          </div>
        </div>

        <div class="timeline-container" data-start="${now.toISOString()}">
          <div class="segments">
            ${generateTimelineHours(vehicle, now, data.hours)}
            <div class="current-time-marker" aria-hidden="true" style="left:0"></div>
          </div>
        </div>
      </div>
    `;
  });

  document.getElementById('availableVehicles').textContent = availableNow;
  document.getElementById('busyVehicles').textContent = busyNow;
  document.getElementById('totalBookings').textContent = totalBookings;

  content.innerHTML = html || '<div class="alert alert-info">No vehicles found</div>';

  // Bootstrap tooltips
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl); });

  try{ updateCurrentTimeMarkers(data.hours); } catch(e){ console.warn(e); }
}

function populateVehicleSelect(data){
  const sel = document.getElementById('vehicleSelect');
  if(!sel) return;

  sel.innerHTML = '';
  if(!data.vehicles || data.vehicles.length === 0) return;

  // add "All" option
  const allOpt = document.createElement('option');
  allOpt.value = '';
  allOpt.textContent = 'All vehicles';
  sel.appendChild(allOpt);

  data.vehicles.forEach(v => {
    const opt = document.createElement('option');
    opt.value = v.id;
    opt.textContent = `${v.plate_number} — ${v.make} ${v.model}`;
    sel.appendChild(opt);
  });

  // sync selection
  if(showAll) sel.value = '';
  else if(focusVehicleId) sel.value = String(focusVehicleId);
}

function onVehicleSelectChange(){
  const sel = document.getElementById('vehicleSelect');
  if(!sel) return;

  if(!sel.value){
    showAll = true;
    focusVehicleId = null;
    document.getElementById('showAllBtn').classList.add('active');
    document.getElementById('showAllBtn').setAttribute('aria-pressed','true');
  } else {
    showAll = false;
    focusVehicleId = sel.value;
    document.getElementById('showAllBtn').classList.remove('active');
    document.getElementById('showAllBtn').setAttribute('aria-pressed','false');
  }

  renderTimeline(latestData, latestStartNow);
}

function toggleShowAll(){
  showAll = !showAll;

  const btn = document.getElementById('showAllBtn');
  btn.setAttribute('aria-pressed', String(showAll));
  if(showAll) btn.classList.add('active'); else btn.classList.remove('active');

  populateVehicleSelect(latestData);
  renderTimeline(latestData, latestStartNow);
}

function updateCurrentTimeMarkers(hours){
  const containers = document.querySelectorAll('.timeline-container');
  const now = new Date();

  containers.forEach(c => {
    const startIso = c.dataset.start;
    if(!startIso) return;

    const start = new Date(startIso);
    const elapsedMs = now - start;
    const totalMs = hours * 3600 * 1000;
    const frac = Math.max(0, Math.min(1, elapsedMs / totalMs));

    const marker = c.querySelector('.current-time-marker');
    if(!marker) return;

    marker.style.left = (frac * 100) + '%';
    marker.style.display = (elapsedMs >= 0 && elapsedMs <= totalMs) ? 'block' : 'none';
  });
}

function isHourBusy(vehicle, hourIndex, now, totalHours){
  const hourStart = new Date(now.getTime() + hourIndex*3600*1000);
  const hourEnd = new Date(hourStart.getTime() + 3600*1000);

  return (vehicle.assignments || []).some(a=>{
    const aStart = a.start_at ? new Date(a.start_at) : new Date(0);
    const aEnd = a.end_at ? new Date(a.end_at) : new Date(8640000000000000);
    return aStart < hourEnd && aEnd > hourStart;
  });
}

function getHourAssignments(vehicle, hourIndex, now){
  const hourStart = new Date(now.getTime() + hourIndex*3600*1000);
  const hourEnd = new Date(hourStart.getTime() + 3600*1000);

  return (vehicle.assignments || []).filter(a=>{
    const aStart = a.start_at ? new Date(a.start_at) : new Date(0);
    const aEnd = a.end_at ? new Date(a.end_at) : new Date(8640000000000000);
    return aStart < hourEnd && aEnd > hourStart;
  });
}

function generateTimelineHours(vehicle, now, hours){
  let html='';

  for(let i=0;i<hours;i++){
    const segStart = new Date(now.getTime() + i*3600*1000);
    const segEnd = new Date(segStart.getTime() + 3600*1000);

    const assignments = getHourAssignments(vehicle,i,now);

    let statusClass = 'free';
    if(assignments.length > 0){
      const coversFullHour = assignments.some(a=>{
        const aStart = a.start_at ? new Date(a.start_at) : new Date(0);
        const aEnd = a.end_at ? new Date(a.end_at) : new Date(8640000000000000);
        return aStart <= segStart && aEnd >= segEnd;
      });
      statusClass = coversFullHour ? 'busy' : 'partial';
    }

    const timeLabel = segStart.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
    const showTick = (i % 6 === 0);
    const tickLabel = segStart.toLocaleTimeString([], {hour:'2-digit'});

    if(segStart.getHours() === 0){
      html += `<div class="day-sep" aria-hidden="true"><div class="day-badge">${escapeHtml(segStart.toLocaleDateString())}</div></div>`;
    }

    html += `
      <div class="segment ${statusClass}"
        data-start="${segStart.toISOString()}"
        data-end="${segEnd.toISOString()}"
        data-assignments='${JSON.stringify(assignments).replace(/'/g, "\\'")}'
        data-bs-toggle="tooltip"
        title="${escapeHtml(timeLabel + (assignments.length ? (' - ' + assignments.length + ' booking(s)') : ''))}"
        onclick="onSegmentClick(this)">
          ${assignments.length ? `<span class="seg-badge">${assignments.length}</span>` : ''}
          ${showTick ? `<span class="seg-time">${escapeHtml(tickLabel)}</span>` : ''}
      </div>
    `;
  }

  return html;
}

function onSegmentClick(el){
  const assignments = el.dataset.assignments ? JSON.parse(el.dataset.assignments) : [];
  const row = el.closest('.vehicle-timeline-row');
  const vehicleId = row ? (row.dataset.vehicleId || null) : null;
  const hourIndex = 0;
  showHourDetails(vehicleId || 0, hourIndex, assignments);
}

function exportTimeline(){
  const hours = document.getElementById('hoursSelect') ? document.getElementById('hoursSelect').value : '';
  const date = document.getElementById('dateSelect') ? document.getElementById('dateSelect').value : '';
  const vehicle = document.getElementById('vehicleSelect') ? document.getElementById('vehicleSelect').value : '';

  const params = new URLSearchParams();
  if(hours) params.set('hours', hours);
  if(date) params.set('date', date);
  if(vehicle) params.set('vehicle_id', vehicle);

  const url = `{{ route('admin.vehicle-timeline.export') }}?` + params.toString();
  window.open(url, '_blank');
}

function showHourDetails(vehicleId, hourIndex, assignments){
  const modal = new bootstrap.Modal(document.getElementById('bookingModal'));
  const modalBody = document.getElementById('bookingModalBody');
  const viewBtn = document.getElementById('viewBookingBtn');

  if(assignments.length===0){
    modalBody.innerHTML = `
      <div class="text-center py-4">
        <i class="fas fa-check-circle text-success" style="font-size:3rem;"></i>
        <h5 class="mt-3">Vehicle Available</h5>
        <p class="text-muted">This time slot is free for booking</p>
      </div>`;
    viewBtn.style.display='none';
    modal.show();
    return;
  }

  const assignment = assignments[0];

  if(assignment.booking_id){
    fetch(`/admin/bookings/${assignment.booking_id}/json`,{headers:{'X-Requested-With':'XMLHttpRequest'}})
      .then(r=>r.json())
      .then(booking=>{
        modalBody.innerHTML = `
          <div class="py-2">
            <h6 class="mb-3"><i class="fas fa-receipt me-2"></i>Order #${escapeHtml(booking.order_number||'N/A')}</h6>
            <div class="mb-2"><strong>Customer:</strong> ${escapeHtml(booking.user?.name||'N/A')}</div>
            <div class="mb-2"><strong>Phone:</strong> ${escapeHtml(booking.user?.phone||'N/A')}</div>
            <div class="mb-2"><strong>Total:</strong> ${escapeHtml(booking.total||'0')}</div>
            <div class="mb-2"><strong>Status:</strong> <span class="badge bg-primary">${escapeHtml(booking.status||'N/A')}</span></div>
          </div>`;
        viewBtn.href=`/admin/bookings/${booking.id}`;
        viewBtn.style.display='inline-block';
        modal.show();
      })
      .catch(err=>{
        console.error(err);
        modalBody.innerHTML = '<div class="alert alert-danger">Failed to load booking details</div>';
        viewBtn.style.display='none';
        modal.show();
      });
  } else {
    modalBody.innerHTML = `
      <div class="py-2">
        <p><strong>Assignment ID:</strong> ${escapeHtml(assignment.id)}</p>
        <p><strong>Start:</strong> ${assignment.start_at ? new Date(assignment.start_at).toLocaleString() : '-'}</p>
        <p><strong>End:</strong> ${assignment.end_at ? new Date(assignment.end_at).toLocaleString() : 'Ongoing'}</p>
      </div>`;
    viewBtn.style.display='none';
    modal.show();
  }
}

document.addEventListener('DOMContentLoaded', function(){
  loadTimeline();
  pollInterval = setInterval(loadTimeline, 30000);
});

window.addEventListener('beforeunload', function(){
  if(pollInterval){ clearInterval(pollInterval); }
});
</script>
@endsection
