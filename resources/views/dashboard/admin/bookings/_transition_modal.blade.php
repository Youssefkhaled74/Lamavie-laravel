@if(session()->has('transition_denied'))
    @php $td = session('transition_denied'); @endphp
    <div class="modal fade" id="transitionDeniedModal" tabindex="-1" aria-labelledby="transitionDeniedLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="transitionDeniedLabel"><i class="fas fa-ban me-2"></i>{{ $td['title'] ?? 'Invalid transition' }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">{{ $td['message'] ?? 'This status change is not allowed.' }}</p>
                    <div class="small text-muted mt-2">Current status: <strong>{{ $td['old_status'] ?? '—' }}</strong></div>
                    <div class="small text-muted">Attempted status: <strong>{{ $td['attempted_status'] ?? '—' }}</strong></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const el = document.getElementById('transitionDeniedModal');
            if (el) {
                try { new bootstrap.Modal(el).show(); } catch (e) { console.warn('Bootstrap modal not available', e); }
            }
        });
    </script>
@endif
