@php
use Illuminate\Support\Facades\Cache;
$adminId = isset($admin) ? ($admin->id ?? $admin) : ($id ?? null);
$isOnline = $adminId ? Cache::has("admin_online:{$adminId}") : false;
$lastSeen = $adminId ? Cache::get("admin_last_seen:{$adminId}") : null;
@endphp

<span class="badge {{ $isOnline ? 'bg-success' : 'bg-secondary' }}">
    {{ $isOnline ? 'Online' : 'Offline' }}
</span>
@if(!$isOnline && $lastSeen)
    <small class="text-muted ms-2">Last seen: {{ \Carbon\Carbon::parse($lastSeen)->diffForHumans() }}</small>
@endif
