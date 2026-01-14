<style>
  :root{
    --bg:#f5f7fb;
    --card:#fff;
    --text:#0f172a;
    --muted:rgba(15,23,42,.6);
    --border:rgba(15,23,42,.08);
    --shadow:0 10px 30px rgba(2,6,23,.08);
    --shadow-sm:0 6px 18px rgba(2,6,23,.06);
    --primary:#0b5ed7;
    --radius:18px;
  }

  body{
    background:
      radial-gradient(1200px 500px at 20% 0%, #e8f0ff 0%, transparent 60%),
      radial-gradient(900px 500px at 100% 30%, #e6fff7 0%, transparent 55%),
      var(--bg);
  }

  .booking-page{
    font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
    color:var(--text);
    max-width:1240px;
    margin:0 auto;
    padding:22px;
  }

  .booking-grid{
    display:grid;
    grid-template-columns:minmax(0,1fr) 380px;
    gap:18px;
    align-items:start;
  }

  @media (max-width: 1200px){
    .booking-grid{ grid-template-columns:minmax(0,1fr) 340px; }
  }
  @media (max-width: 991px){
    .booking-grid{ grid-template-columns:1fr; }
    .booking-grid aside{ position:static !important; }
  }

  .card-modern{
    background:var(--card);
    border-radius:var(--radius);
    border:1px solid var(--border);
    box-shadow:var(--shadow-sm);
    padding:18px;
  }

  /* ✅ بدل nested card-modern */
  .inner-card{
    background:#fbfdff;
    border:1px solid rgba(15,23,42,.06);
    border-radius:14px;
    padding:16px;
  }

  .section-title{
    display:flex;
    align-items:flex-start;
    gap:12px;
    margin-bottom:14px;
  }
  .section-title i{
    font-size:20px;
    color:var(--primary);
    margin-top:2px;
  }

  .label{ font-size:13px; font-weight:800; color:var(--text); }
  .value{ font-size:14px; color:rgba(15,23,42,.78); margin-top:6px; }
  .muted{ color:var(--muted); font-size:13px; }

  /* ✅ Aside stack */
  .aside-stack{
    display:flex;
    flex-direction:column;
    gap:14px;
  }
  .booking-grid aside{
    position:sticky;
    top:86px;
  }

  /* ✅ Key/Value rows for assignment cards */
  .kv{
    display:grid;
    grid-template-columns: 120px 1fr;
    gap:10px 12px;
    margin-top:10px;
  }
  .kv .k{
    font-size:12px;
    font-weight:800;
    color:rgba(15,23,42,.65);
  }
  .kv .v{
    font-size:13.5px;
    color:rgba(15,23,42,.85);
  }

  .btn-primary{
    background:linear-gradient(90deg,#0b5ed7,#2f7bff);
    color:#fff;
    border:none;
    padding:10px 14px;
    border-radius:12px;
    font-weight:800;
    box-shadow:0 10px 24px rgba(11,94,215,.22);
  }
  .btn-outline{
    background:#fff;
    border:1px solid rgba(15,23,42,.12);
    color:var(--text);
    padding:10px 12px;
    border-radius:12px;
    font-weight:800;
  }

  /* Avatar in meta card */
  .avatar-wrapper{
    width:56px;height:56px;border-radius:12px;overflow:hidden;flex:0 0 56px;display:inline-block;background:#f1f5f9;align-items:center;justify-content:center;display:flex;
  }
  .avatar-img{width:100%;height:100%;object-fit:cover;display:block;border-radius:8px}
  .avatar-initials{font-weight:700;color:var(--primary);font-size:18px}

  /* Actions buttons in meta card */
  .meta-list .actions{display:flex;gap:10px;margin-top:12px}
  .meta-list .actions .btn{padding:10px 14px;border-radius:12px;font-weight:800}
  .meta-list .actions .btn-primary{box-shadow:0 12px 30px rgba(11,94,215,.18)}
  .meta-list .actions .btn-outline{background:#fff;border:1px solid rgba(15,23,42,.08)}
  @media (max-width:600px){
    .meta-list .actions{flex-direction:column}
    .meta-list .actions .btn{width:100%}
  }

  /* Car-wash cars section */
  .cars-section{ margin-top:12px; display:flex; flex-direction:column; gap:12px; }
  .car-type-block{ background:var(--card); border:1px solid rgba(15,23,42,.06); border-radius:12px; padding:12px; }
  .car-type-header{ display:flex; align-items:center; justify-content:space-between; gap:12px; }
  .car-type-title{ font-weight:800; font-size:14px; display:flex; align-items:center; gap:8px; }
  .car-type-badge{ background:linear-gradient(90deg,#eef5ff,#f1fcf9); color:var(--primary); padding:6px 12px; border-radius:999px; font-weight:800; font-size:13px; border:1px solid rgba(11,94,215,.06); }
  .car-qty-badge{ background:var(--primary); color:#fff; padding:6px 10px; border-radius:999px; font-weight:800; font-size:12px; }
  .car-details-grid{ display:grid; grid-template-columns:repeat(2,1fr); gap:10px; margin-top:12px; }
  @media (max-width:600px){ .car-details-grid{ grid-template-columns:1fr; } }
  .car-detail-card{ background:#fbfdff; border-radius:10px; border:1px solid rgba(15,23,42,.04); padding:10px; display:flex; flex-direction:column; gap:6px; }
  .car-detail-row{ font-size:13px; color:var(--muted); display:flex; gap:8px; align-items:center; }
  .car-detail-key{ font-weight:800; color:rgba(15,23,42,.75); width:80px; flex:0 0 80px; }
  .car-detail-value{ color:rgba(15,23,42,.85); }
</style>
