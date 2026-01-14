{{-- Premium Admin Send Notification Modal --}}
<style>
/* Scoped styles فقط للمودال ده */
.nm{
    --p:#0d6efd;
    --ink:#0f172a;
    --muted:#64748b;
    --b: rgba(15,23,42,.10);
    --soft: rgba(13,110,253,.10);
    --soft2: rgba(16,185,129,.10);
    --warn: rgba(245,158,11,.12);
    --danger: rgba(239,68,68,.10);
    --r: 18px;
    --sh: 0 22px 60px rgba(2,6,23,.16);
    --sh2: 0 10px 22px rgba(2,6,23,.10);
}

#notifyModal .modal-dialog{ max-width: 620px; }
#notifyModal .modal-content{
    border: 1px solid var(--b);
    border-radius: var(--r);
    overflow: hidden;
    box-shadow: var(--sh);
    background:#fff;
}

.nm-head{
    padding: 16px 18px;
    border-bottom: 1px solid var(--b);
    background:
        radial-gradient(900px 220px at 10% 10%, rgba(13,110,253,.18), transparent 60%),
        radial-gradient(800px 240px at 90% 0%, rgba(16,185,129,.12), transparent 60%),
        linear-gradient(180deg, rgba(15,23,42,.02), #fff);
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap: 12px;
}

.nm-title-wrap{ display:flex; gap:12px; align-items:flex-start; }
.nm-ico{
    width:44px;height:44px;border-radius: 16px;
    display:grid;place-items:center;
    background: var(--soft);
    border: 1px solid rgba(13,110,253,.22);
    color: var(--p);
    box-shadow: var(--sh2);
    flex: 0 0 auto;
}
.nm-title{
    margin:0;
    font-weight: 950;
    color: var(--ink);
    letter-spacing:.2px;
}
.nm-sub{
    margin:4px 0 0;
    color: var(--muted);
    font-weight: 650;
    font-size: 13px;
}

#notifyModal .btn-close{ margin-top: 6px; }

.nm-body{ padding: 16px 18px; }
.nm-field{ margin-bottom: 14px; }

.nm-label{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap: 10px;
    margin-bottom: 6px;
    font-weight: 900;
    color: var(--ink);
}
.nm-hint{
    font-size: 12px;
    color: #94a3b8;
    font-weight: 800;
}
.nm-counter{
    font-size: 12px;
    color: #94a3b8;
    font-weight: 900;
}
.nm-counter strong{ color: var(--p); }

.nm-input, .nm-textarea, .nm-select{
    width:100%;
    border-radius: 14px;
    border: 1px solid var(--b);
    background: #fff;
    padding: 10px 12px;
    font-weight: 650;
    font-size: 14px;
    outline: none;
    transition: border .15s ease, box-shadow .15s ease, transform .12s ease;
}
.nm-textarea{ min-height: 140px; resize: vertical; }

.nm-input:focus, .nm-textarea:focus, .nm-select:focus{
    border-color: rgba(13,110,253,.45);
    box-shadow: 0 0 0 6px rgba(13,110,253,.10);
}

.nm-row{
    display:flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items:center;
    justify-content: space-between;
    margin-bottom: 10px;
}
.nm-chip{
    display:inline-flex;
    align-items:center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 999px;
    border: 1px solid var(--b);
    background: #fff;
    font-weight: 900;
    font-size: 12px;
    color: var(--ink);
    box-shadow: 0 6px 16px rgba(2,6,23,.04);
    user-select:none;
}

.nm-quick{
    display:flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content:flex-end;
}
.nm-pill{
    border-radius: 999px;
    padding: 7px 11px;
    border: 1px solid rgba(13,110,253,.22);
    background: rgba(13,110,253,.08);
    color: var(--p);
    font-weight: 950;
    font-size: 12px;
    transition:.15s ease;
}
.nm-pill:hover{ transform: translateY(-1px); background: rgba(13,110,253,.12); border-color: rgba(13,110,253,.35); }

.nm-foot{
    padding: 14px 18px;
    border-top: 1px solid var(--b);
    background: rgba(15,23,42,.01);
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap: 10px;
    flex-wrap: wrap;
}

.nm-btn{
    border-radius: 14px;
    padding: 10px 14px;
    font-weight: 950;
    border:1px solid var(--b);
    background:#fff;
    color: var(--ink);
    display:inline-flex;
    align-items:center;
    gap: 8px;
    transition: .15s ease;
}
.nm-btn:hover{ transform: translateY(-1px); box-shadow: 0 10px 22px rgba(2,6,23,.10); }
.nm-btn.primary{
    border-color: rgba(13,110,253,.25);
    background: rgba(13,110,253,.10);
    color: var(--p);
}
.nm-btn.primary:hover{ background: rgba(13,110,253,.14); border-color: rgba(13,110,253,.35); }

.nm-note{
    font-size:12px;
    color:#94a3b8;
    font-weight: 800;
}
</style>

<div class="modal fade nm" id="notifyModal" tabindex="-1" aria-labelledby="notifyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="notifyForm" method="POST" action="{{ route('admin.bookings.notify.send', $booking->id) }}">
                @csrf

                {{-- Header --}}
                <div class="nm-head">
                    <div class="nm-title-wrap">
                        <div class="nm-ico"><i class="fas fa-paper-plane"></i></div>
                        <div>
                            <h5 class="nm-title" id="notifyModalLabel">Send notification</h5>
                            <div class="nm-sub">Send a message to the user about this booking.</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="nm-body">
                    <div class="nm-row">
                        <span class="nm-chip"><i class="fas fa-user"></i> User notification</span>

                        {{-- Quick templates (اختياري) --}}
                        <div class="nm-quick">
                            <button type="button" class="nm-pill" data-template="arrive">Arrival</button>
                            <button type="button" class="nm-pill" data-template="delay">Delay</button>
                            <button type="button" class="nm-pill" data-template="done">Completed</button>
                        </div>
                    </div>

                    <div class="nm-field">
                        <div class="nm-label">
                            <span>Title <span class="nm-hint">(optional)</span></span>
                            <span class="nm-counter" id="titleCounter"><strong>0</strong>/255</span>
                        </div>
                        <input
                            id="notify-title"
                            name="title"
                            class="nm-input"
                            maxlength="255"
                            value="{{ old('title') }}"
                            placeholder="e.g., Update about your booking"
                        >
                    </div>

                    <div class="nm-field">
                        <div class="nm-label">
                            <span>Message <span class="nm-hint">(required)</span></span>
                            <span class="nm-counter" id="msgCounter"><strong>0</strong>/1000</span>
                        </div>
                        <textarea
                            id="notify-message"
                            name="message"
                            class="nm-textarea"
                            rows="5"
                            maxlength="1000"
                            required
                            placeholder="Write a clear message…"
                        >{{ old('message') }}</textarea>
                        <div class="nm-note mt-2">
                            Tip: Keep it short and actionable (what happened + what the user should do).
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="nm-foot">
                    <span class="nm-note"><i class="fas fa-shield-halved me-1"></i>Sent as an official system notification.</span>

                    <div class="d-flex gap-2">
                        <button type="button" class="nm-btn" data-bs-dismiss="modal">
                            <i class="fas fa-xmark"></i> Cancel
                        </button>
                        <button id="notifySubmit" type="submit" class="nm-btn primary">
                            <i class="fas fa-paper-plane"></i> Send
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
(function(){
    const title = document.getElementById('notify-title');
    const msg = document.getElementById('notify-message');
    const titleCounter = document.getElementById('titleCounter');
    const msgCounter = document.getElementById('msgCounter');
    const form = document.getElementById('notifyForm');
    const submitBtn = document.getElementById('notifySubmit');

    function setCounter(el, counterEl, max){
        const n = (el.value || '').length;
        counterEl.innerHTML = `<strong>${n}</strong>/${max}`;
    }

    if(title && titleCounter){
        setCounter(title, titleCounter, 255);
        title.addEventListener('input', () => setCounter(title, titleCounter, 255));
    }
    if(msg && msgCounter){
        setCounter(msg, msgCounter, 1000);
        msg.addEventListener('input', () => setCounter(msg, msgCounter, 1000));
    }

    // Quick templates
    document.querySelectorAll('#notifyModal [data-template]').forEach(btn => {
        btn.addEventListener('click', () => {
            const t = btn.getAttribute('data-template');
            if(!msg) return;

            if(t === 'arrive'){
                if(title && !title.value) title.value = 'Technician on the way';
                msg.value = 'Our technician is on the way and will arrive shortly. Thank you for your patience.';
            }
            if(t === 'delay'){
                if(title && !title.value) title.value = 'Slight delay';
                msg.value = 'We’re sorry—there is a slight delay. We will update you with the new arrival time shortly.';
            }
            if(t === 'done'){
                if(title && !title.value) title.value = 'Booking completed';
                msg.value = 'Your booking has been completed successfully. If you have any feedback, we’d love to hear it.';
            }

            // refresh counters
            if(title && titleCounter) setCounter(title, titleCounter, 255);
            if(msg && msgCounter) setCounter(msg, msgCounter, 1000);
            msg.focus();
        });
    });

    // Loading UX on submit
    if(form && submitBtn){
        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        });
    }
})();
</script>
