@extends('dashboard.admin.layouts.main')

@section('content')
<style>
    /* ===== Premium Notifications (Scoped) ===== */
    .noti-wrap{
        --n-primary:#0d6efd;
        --n-ink:#0f172a;
        --n-muted:#64748b;
        --n-border: rgba(15,23,42,.10);
        --n-soft: rgba(13,110,253,.10);
        --n-soft2: rgba(16,185,129,.12);
        --n-warn: rgba(245,158,11,.12);
        --n-danger: rgba(239,68,68,.12);
        --n-shadow: 0 20px 50px rgba(2,6,23,.10);
        --n-shadow2: 0 10px 25px rgba(2,6,23,.06);
        --n-radius: 18px;
    }

    .noti-shell{
        border-radius: var(--n-radius);
        overflow: hidden;
        border: 1px solid var(--n-border);
        background: #fff;
        box-shadow: var(--n-shadow);
    }

    /* Header */
    .noti-header{
        padding: 18px 18px 14px;
        border-bottom: 1px solid var(--n-border);
        background:
            radial-gradient(900px 220px at 10% 10%, rgba(13,110,253,.18), transparent 60%),
            radial-gradient(800px 240px at 90% 0%, rgba(16,185,129,.12), transparent 60%),
            linear-gradient(180deg, rgba(15,23,42,.02), #fff);
    }

    .noti-head-row{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap: 14px;
        flex-wrap: wrap;
    }

    .noti-brand{
        display:flex;
        gap: 12px;
        align-items:flex-start;
        min-width: 260px;
    }

    .noti-brand .icon{
        width:46px;height:46px;border-radius: 16px;
        display:grid;place-items:center;
        background: rgba(13,110,253,.12);
        border: 1px solid rgba(13,110,253,.22);
        color: var(--n-primary);
        font-size: 18px;
        flex: 0 0 auto;
        box-shadow: var(--n-shadow2);
    }

    .noti-brand h3{
        margin:0;
        font-weight: 900;
        color: var(--n-ink);
        letter-spacing: .2px;
    }
    .noti-brand p{
        margin:4px 0 0;
        color: var(--n-muted);
        font-size: 13px;
        font-weight: 650;
        max-width: 520px;
    }

    .noti-tools{
        display:flex;
        align-items:center;
        gap: 10px;
        flex-wrap: wrap;
        justify-content:flex-end;
    }

    .chip{
        display:inline-flex;
        align-items:center;
        gap: 8px;
        background:#fff;
        border:1px solid var(--n-border);
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        color: var(--n-ink);
        box-shadow: 0 6px 16px rgba(2,6,23,.04);
        user-select:none;
        white-space:nowrap;
    }
    .chip b{ color: var(--n-primary); }

    .btn-pill{
        border-radius: 999px;
        font-weight: 900;
        padding: 8px 12px;
        font-size: 12px;
        border: 1px solid rgba(13,110,253,.25);
        background: rgba(13,110,253,.10);
        color: var(--n-primary);
        transition: all .15s ease;
        display:inline-flex;
        align-items:center;
        gap: 8px;
    }
    .btn-pill:hover{ transform: translateY(-1px); background: rgba(13,110,253,.15); border-color: rgba(13,110,253,.40); }
    .btn-pill:disabled{ opacity:.55; cursor:not-allowed; transform:none; }

    .btn-ghost{
        border-radius: 999px;
        font-weight: 900;
        padding: 8px 12px;
        font-size: 12px;
        border: 1px solid var(--n-border);
        background: #fff;
        color: var(--n-ink);
        transition: all .15s ease;
        display:inline-flex;
        align-items:center;
        gap: 8px;
    }
    .btn-ghost:hover{ transform: translateY(-1px); background: rgba(15,23,42,.02); }

    /* Search + Tabs */
    .noti-subbar{
        display:flex;
        gap: 10px;
        align-items:center;
        justify-content:space-between;
        flex-wrap: wrap;
        margin-top: 12px;
    }

    .noti-search{
        position: relative;
        flex: 1;
        min-width: 260px;
        max-width: 520px;
    }

    .noti-search input{
        width:100%;
        border-radius: 14px;
        border: 1px solid var(--n-border);
        background: #fff;
        padding: 10px 12px 10px 38px;
        font-weight: 700;
        font-size: 13px;
        outline: none;
        transition: border .15s ease, box-shadow .15s ease;
    }
    .noti-search input:focus{
        border-color: rgba(13,110,253,.40);
        box-shadow: 0 0 0 6px rgba(13,110,253,.10);
    }
    .noti-search .i{
        position:absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 14px;
    }

    .noti-tabs{
        display:flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content:flex-end;
    }

    .tab{
        border:1px solid var(--n-border);
        background:#fff;
        color: var(--n-ink);
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        cursor:pointer;
        transition: all .15s ease;
        display:inline-flex;
        align-items:center;
        gap: 8px;
    }
    .tab:hover{ transform: translateY(-1px); background: rgba(15,23,42,.02); }
    .tab.active{
        border-color: rgba(13,110,253,.35);
        background: rgba(13,110,253,.10);
        color: var(--n-primary);
    }

    /* List */
    .noti-list{ margin:0; padding:0; list-style:none; }
    .noti-item{
        display:flex;
        gap: 14px;
        justify-content:space-between;
        align-items:flex-start;
        padding: 14px 16px;
        border-bottom: 1px solid var(--n-border);
        position: relative;
        background:#fff;
        transition: background .15s ease, transform .12s ease;
    }
    .noti-item:hover{ background: rgba(15,23,42,.015); }

    .noti-item.unread{
        background: linear-gradient(90deg, rgba(13,110,253,.12), transparent 55%);
    }
    .noti-item.unread::before{
        content:"";
        position:absolute;
        right:0; top:0; bottom:0;
        width:4px;
        background: var(--n-primary);
    }

    .noti-left{
        flex:1;
        min-width: 0;
        display:flex;
        gap: 12px;
        align-items:flex-start;
    }

    .noti-dot{
        width: 10px;
        height: 10px;
        margin-top: 8px;
        border-radius: 999px;
        background: #cbd5e1;
        flex: 0 0 auto;
    }
    .noti-item.unread .noti-dot{
        background: var(--n-primary);
        box-shadow: 0 0 0 6px rgba(13,110,253,.12);
    }

    .noti-content{ min-width:0; }
    .noti-title-line{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap: 10px;
        flex-wrap: wrap;
    }

    .noti-content .title{
        font-weight: 950;
        color: var(--n-ink);
        line-height: 1.35;
        margin-bottom: 4px;
        word-break: break-word;
        font-size: 14px;
    }

    .noti-content .body{
        color: var(--n-muted);
        font-size: 13px;
        font-weight: 650;
        line-height: 1.65;
        word-break: break-word;
    }

    /* NEW: bilingual toggle */
    .lang-toggle{
        display:inline-flex;
        align-items:center;
        gap: 6px;
        border-radius: 999px;
        padding: 6px 10px;
        border: 1px solid var(--n-border);
        background: rgba(15,23,42,.02);
        color: var(--n-ink);
        font-weight: 950;
        font-size: 11px;
        cursor:pointer;
        user-select:none;
        transition: all .15s ease;
        white-space:nowrap;
    }
    .lang-toggle:hover{ transform: translateY(-1px); background: rgba(13,110,253,.08); border-color: rgba(13,110,253,.25); }
    .lang-toggle:disabled{ opacity:.55; cursor:not-allowed; transform:none; }
    .lang-pill{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        width: 28px;
        height: 20px;
        border-radius: 999px;
        background:#fff;
        border:1px solid var(--n-border);
        font-size: 10px;
        font-weight: 950;
        color:#334155;
    }

    .lang-block{ margin-top: 8px; }
    .lang-block[hidden]{ display:none !important; }

    .meta{
        margin-top: 8px;
        display:flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items:center;
        color: #94a3b8;
        font-size: 12px;
        font-weight: 800;
    }

    .status-badge{
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 11px;
        font-weight: 950;
        border: 1px solid var(--n-border);
        background: rgba(15,23,42,.03);
        color: #334155;
        text-transform: capitalize;
        white-space: nowrap;
    }

    .noti-actions{
        min-width: 220px;
        display:flex;
        flex-direction: column;
        gap: 8px;
        align-items:flex-end;
        text-align: right;
    }

    .btn-soft{
        border-radius: 12px;
        font-weight: 950;
        padding: 9px 12px;
        font-size: 12px;
        border: 1px solid rgba(13,110,253,.25);
        background: rgba(13,110,253,.10);
        color: var(--n-primary);
        transition: all .15s ease;
        display:inline-flex;
        align-items:center;
        gap: 8px;
    }
    .btn-soft:hover{ background: rgba(13,110,253,.16); border-color: rgba(13,110,253,.40); transform: translateY(-1px); }

    .flag{
        font-size: 11px;
        font-weight: 950;
        padding: 6px 10px;
        border-radius: 999px;
        border: 1px solid var(--n-border);
        background: rgba(16,185,129,.10);
        color: #065f46;
        display:inline-flex;
        align-items:center;
        gap: 6px;
    }

    .empty{
        padding: 32px 16px;
        text-align:center;
        color: var(--n-muted);
    }
    .empty .emoji{ font-size: 44px; margin-bottom: 10px; opacity:.95; }
    .empty .t{ font-weight: 950; color: var(--n-ink); margin-bottom: 6px; }
    .empty .s{ font-size: 13px; font-weight: 650; }

    .noti-pagination{
        padding: 14px 16px;
        border-top: 1px solid var(--n-border);
        background: rgba(15,23,42,.01);
        display:flex;
        justify-content:flex-end;
    }

    @media (max-width: 768px){
        .noti-item{ flex-direction: column; align-items: stretch; }
        .noti-actions{
            min-width: 0;
            align-items:flex-start;
            text-align:left;
            flex-direction: row;
            justify-content: space-between;
        }
        .noti-search{ max-width: 100%; }
    }
</style>

@php
    $unreadCount = $notifications->whereNull('read_at')->count();
    $totalCount  = $notifications->count();
    $uiLocale = app()->getLocale(); // 'en' or 'ar'
@endphp

<div class="noti-wrap">
    <div class="noti-shell">
        {{-- Header --}}
        <div class="noti-header">
            <div class="noti-head-row">
                <div class="noti-brand">
                    <div class="icon"><i class="fas fa-bell"></i></div>
                    <div>
                        <h3>Notifications</h3>
                        <p>Track updates, actions, and system alerts. Use filters & search to find items faster.</p>
                    </div>
                </div>

                <div class="noti-tools">
                    <span class="chip"><i class="far fa-envelope-open"></i> <b id="ui-unread-count">{{ $unreadCount }}</b> Unread</span>
                    <span class="chip"><i class="far fa-list-alt"></i> <b>{{ $totalCount }}</b> On this page</span>

                    <form id="markAllForm" method="POST" action="{{ route('admin.notifications.markAll') }}" style="margin:0;">
                        @csrf
                        @foreach($notifications->whereNull('read_at') as $n)
                            <input type="hidden" name="ids[]" value="{{ $n->id }}">
                        @endforeach
                        <button id="markAllBtn" class="btn-pill" type="submit" @if($unreadCount === 0) disabled @endif>
                            <i class="fas fa-check-double"></i> Mark all as read
                        </button>
                    </form>

                    <button class="btn-ghost" type="button" onclick="window.location.reload()">
                        <i class="fas fa-rotate"></i> Refresh
                    </button>
                </div>
            </div>

            {{-- Search + Tabs --}}
            <div class="noti-subbar">
                <div class="noti-search">
                    <i class="fas fa-search i"></i>
                    <input id="notiSearch" type="text" placeholder="Search by title, body, status (EN/AR)..." autocomplete="off">
                </div>

                <div class="noti-tabs">
                    <button class="tab active" type="button" data-filter="all"><i class="fas fa-layer-group"></i> All</button>
                    <button class="tab" type="button" data-filter="unread"><i class="fas fa-circle-dot"></i> Unread</button>
                    <button class="tab" type="button" data-filter="read"><i class="fas fa-check"></i> Read</button>
                </div>
            </div>
        </div>

        {{-- Body --}}
        @if($notifications->count() === 0)
            <div class="empty">
                <div class="emoji">🔔</div>
                <div class="t">No notifications yet</div>
                <div class="s">When something happens, you’ll see it here.</div>
            </div>
        @else
            <ul class="noti-list" id="notiList">
                @foreach($notifications as $n)
                    @php
                        $isUnread = is_null($n->read_at);

                        $titleEn = data_get($n->title, 'en') ?? ($n->getLocalizedTitle('en') ?? 'Notification');
                        $titleAr = data_get($n->title, 'ar') ?? ($n->getLocalizedTitle('ar') ?? '');
                        $bodyEn  = data_get($n->body, 'en')  ?? ($n->getLocalizedBody('en') ?? '');
                        $bodyAr  = data_get($n->body, 'ar')  ?? ($n->getLocalizedBody('ar') ?? '');
                        $status  = $n->status ?? '';

                        $hasAr = (bool) trim((string)$titleAr . (string)$bodyAr);
                        $hasEn = (bool) trim((string)$titleEn . (string)$bodyEn);

                        // which language to show by default:
                        $defaultLang = in_array($uiLocale, ['ar','en']) ? $uiLocale : 'en';

                        // Search should include both languages always:
                        $searchString = strtolower(trim(($titleEn.' '.$titleAr.' '.$bodyEn.' '.$bodyAr.' '.$status)));
                    @endphp

                    <li class="noti-item {{ $isUnread ? 'unread' : '' }}"
                        data-id="{{ $n->id }}"
                        data-state="{{ $isUnread ? 'unread' : 'read' }}"
                        data-search="{{ $searchString }}"
                        data-default-lang="{{ $defaultLang }}"
                    >
                        <div class="noti-left">
                            <div class="noti-dot" title="{{ $isUnread ? 'Unread' : 'Read' }}"></div>

                            <div class="noti-content">
                                <div class="noti-title-line">
                                    <div style="min-width:0;">
                                        {{-- EN block --}}
                                        <div class="lang-block lang-en" @if($defaultLang === 'ar') hidden @endif>
                                            <div class="title">{{ $titleEn }}</div>
                                            @if($bodyEn)
                                                <div class="body">{{ $bodyEn }}</div>
                                            @endif
                                        </div>

                                        {{-- AR block --}}
                                        <div class="lang-block lang-ar" dir="rtl" @if($defaultLang !== 'ar') hidden @endif>
                                            <div class="title">{{ $titleAr ?: $titleEn }}</div>
                                            @if($bodyAr)
                                                <div class="body">{{ $bodyAr }}</div>
                                            @elseif($bodyEn)
                                                <div class="body">{{ $bodyEn }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2 align-items-start flex-wrap" style="justify-content:flex-end;">
                                        <span class="status-badge">{{ $status }}</span>

                                        {{-- Toggle Language (only if both exist) --}}
                                        <button
                                            class="lang-toggle"
                                            type="button"
                                            data-action="toggle-lang"
                                            data-has-en="{{ $hasEn ? '1' : '0' }}"
                                            data-has-ar="{{ $hasAr ? '1' : '0' }}"
                                            @if(!($hasEn && $hasAr)) disabled @endif
                                            title="Switch language"
                                        >
                                            <span class="lang-pill">EN</span>
                                            <i class="fas fa-arrows-rotate"></i>
                                            <span class="lang-pill">AR</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="meta">
                                    <span><i class="far fa-clock"></i> {{ $n->created_at?->toDayDateTimeString() }}</span>
                                    @if($isUnread)
                                        <span class="flag"><i class="fas fa-sparkles"></i> New</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="noti-actions">
                            <form class="toggle-read-form" method="POST" action="{{ route('admin.notifications.toggle', $n) }}" data-id="{{ $n->id }}" style="margin:0;">
                                @csrf
                                <button class="btn-soft toggle-read-button" type="submit">
                                    @if($isUnread)
                                        <i class="fas fa-check-circle"></i> Mark as read
                                    @else
                                        <i class="fas fa-undo"></i> Mark as unread
                                    @endif
                                </button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="noti-pagination">
                {{ $notifications->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

<script>
(function(){
    const list = document.getElementById('notiList');
    if(!list) return;

    const tabs = Array.from(document.querySelectorAll('.tab'));
    const search = document.getElementById('notiSearch');
    const unreadCountEl = document.getElementById('ui-unread-count');

    let activeFilter = 'all';
    let query = '';

    function apply(){
        const items = Array.from(list.querySelectorAll('.noti-item'));
        let visibleUnread = 0;

        items.forEach(item => {
            const state = item.dataset.state;
            const hay = item.dataset.search || '';

            const matchFilter =
                activeFilter === 'all' ? true :
                activeFilter === 'unread' ? state === 'unread' :
                state === 'read';

            const matchQuery = !query ? true : hay.includes(query);

            const show = matchFilter && matchQuery;
            item.style.display = show ? '' : 'none';

            if(show && state === 'unread') visibleUnread++;
        });

        if(unreadCountEl) unreadCountEl.textContent = String(visibleUnread);
    }

    tabs.forEach(t => {
        t.addEventListener('click', () => {
            tabs.forEach(x => x.classList.remove('active'));
            t.classList.add('active');
            activeFilter = t.dataset.filter || 'all';
            apply();
        });
    });

    if(search){
        search.addEventListener('input', () => {
            query = (search.value || '').trim().toLowerCase();
            apply();
        });
    }

    // NEW: toggle language per item
    list.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-action="toggle-lang"]');
        if(!btn) return;

        const li = btn.closest('.noti-item');
        if(!li) return;

        const en = li.querySelector('.lang-en');
        const ar = li.querySelector('.lang-ar');
        if(!en || !ar) return;

        const enHidden = en.hasAttribute('hidden');
        // switch
        if(enHidden){
            en.removeAttribute('hidden');
            ar.setAttribute('hidden', '');
        }else{
            en.setAttribute('hidden','');
            ar.removeAttribute('hidden');
        }
    });

    apply();
})();
</script>

<script>
(function(){
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const notifBadge = document.getElementById('notif-badge');
    const uiUnread = document.getElementById('ui-unread-count');
    const markAllForm = document.getElementById('markAllForm');

    function updateCounts(unread){
        if(typeof unread !== 'number') return;
        if(uiUnread) uiUnread.textContent = String(unread);
        if(notifBadge){
            notifBadge.textContent = String(unread);
            notifBadge.style.display = unread > 0 ? '' : 'none';
        }
    }

    document.querySelectorAll('.toggle-read-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = form.dataset.id;
            try{
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if(data.success){
                    const li = form.closest('.noti-item');
                    if(li){
                        const isRead = data.read;
                        li.dataset.state = isRead ? 'read' : 'unread';
                        li.classList.toggle('unread', !isRead);

                        const flag = li.querySelector('.flag');
                        if(isRead && flag) flag.remove();
                        if(!isRead && !flag){
                            const meta = li.querySelector('.meta');
                            if(meta){
                                const span = document.createElement('span');
                                span.className = 'flag';
                                span.innerHTML = '<i class="fas fa-sparkles"></i> New';
                                meta.appendChild(span);
                            }
                        }

                        const btn = form.querySelector('.toggle-read-button');
                        if(btn){
                            btn.innerHTML = isRead
                                ? '<i class="fas fa-undo"></i> Mark as unread'
                                : '<i class="fas fa-check-circle"></i> Mark as read';
                        }

                        if(markAllForm){
                            const inp = markAllForm.querySelector('input[name="ids[]"][value="'+id+'"]');
                            if(inp) inp.remove();
                        }
                    }
                    updateCounts(Number(data.unreadCount || 0));
                }
            }catch(err){
                console.error('Toggle read failed', err);
            }
        });
    });

    if(markAllForm){
        markAllForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const url = form.action;
            const fd = new FormData(form);
            try{
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: fd
                });
                const data = await res.json();
                if(data.success){
                    const items = Array.from(document.querySelectorAll('.noti-item'));
                    items.forEach(li => {
                        const state = li.dataset.state;
                        if(state === 'unread'){
                            li.dataset.state = 'read';
                            li.classList.remove('unread');
                            const flag = li.querySelector('.flag'); if(flag) flag.remove();
                            const btn = li.querySelector('.toggle-read-button');
                            if(btn) btn.innerHTML = '<i class="fas fa-undo"></i> Mark as unread';
                        }
                    });

                    form.querySelectorAll('input[name="ids[]"]').forEach(i=>i.remove());
                    const markBtn = document.getElementById('markAllBtn');
                    if(markBtn) markBtn.disabled = true;

                    updateCounts(Number(data.unreadCount || 0));
                }
            }catch(err){
                console.error('Mark all failed', err);
            }
        });
    }
})();
</script>
@endsection
