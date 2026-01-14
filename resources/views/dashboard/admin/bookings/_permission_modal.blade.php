@if(session()->has('permission_denied'))
    @php $pd = session('permission_denied'); @endphp
    <div class="modal fade" id="permissionDeniedModal" tabindex="-1" aria-labelledby="permissionDeniedLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="permissionDeniedLabel"><i class="fas fa-exclamation-triangle me-2"></i>{{ $pd['title'] ?? 'Permission denied' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">{{ $pd['message'] ?? 'You are not allowed to perform this action.' }}</p>
                    @if(!empty($pd['attempted_status']))
                        <p class="small text-muted mt-2">Attempted status: <strong>{{ $pd['attempted_status'] }}</strong></p>
                    @endif
                    <p class="small text-muted">If you believe this is an error, check the account permissions or contact a system administrator.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const el = document.getElementById('permissionDeniedModal');
            if (el) {
                try { new bootstrap.Modal(el).show(); } catch (e) { console.warn('Bootstrap modal not available', e); }
            }
        });
    </script>
@endif
