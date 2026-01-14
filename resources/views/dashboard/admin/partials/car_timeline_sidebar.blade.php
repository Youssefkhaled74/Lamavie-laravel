<style>
    .sidebar-actions { padding: 0.75rem 1rem; border-top: 1px dashed rgba(255,255,255,0.06); }
    .sidebar-actions .btn { display:block; width:100%; margin-bottom:0.5rem; text-align:left; }
    .sidebar-actions .btn .fa { margin-right:10px; }
    .btn-users { background: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%); color: #fff; border-radius:8px; padding:0.5rem 0.75rem; border: none; box-shadow: 0 6px 18px rgba(59,130,246,0.12); }
    .btn-users:hover { transform: translateY(-2px); }
    .btn-timeline { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border-radius:8px; padding:0.5rem 0.75rem; border: none; box-shadow: 0 6px 18px rgba(118,75,162,0.12); }
    .btn-timeline:hover { transform: translateY(-2px); }
</style>

<div class="sidebar-actions">
    <a href="{{ route('admin.users.index') }}" class="btn btn-users">
        <i class="fas fa-users"></i>
        All Users
    </a>
    <a href="{{ route('admin.vehicle-timeline.full') }}" class="btn btn-timeline">
        <i class="fas fa-car"></i>
        Vehicle Timeline
    </a>
</div>
