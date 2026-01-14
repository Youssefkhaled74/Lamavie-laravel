<style>
    .labs-table-wrap{
        border: 1px solid rgba(15,23,42,.08);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 14px 40px rgba(2,6,23,.06);
        background: #fff;
    }

    .labs-table{
        margin: 0;
    }

    .labs-table thead th{
        position: sticky;
        top: 0;
        z-index: 2;
        background: linear-gradient(180deg,#ffffff,#f6f8ff);
        border-bottom: 1px solid rgba(15,23,42,.08);
        font-weight: 800;
        color: #0f172a;
        padding: 14px 14px;
        white-space: nowrap;
    }

    .labs-table tbody td{
        padding: 14px;
        vertical-align: middle;
        border-color: rgba(15,23,42,.06);
        color: rgba(15,23,42,.85);
    }

    .labs-table tbody tr{
        transition: transform .12s ease, box-shadow .12s ease, background .12s ease;
    }

    .labs-table tbody tr:hover{
        background: rgba(37,99,235,.04);
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(2,6,23,.06);
    }

    .labs-pill{
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding: 6px 10px;
        border-radius: 999px;
        border: 1px solid rgba(15,23,42,.08);
        background: rgba(37,99,235,.06);
        color: #0f172a;
        font-weight: 800;
        max-width: 260px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .labs-muted{
        color: rgba(15,23,42,.60);
        font-size: .88rem;
    }

    .labs-actions .btn{
        border-radius: 12px;
        font-weight: 800;
    }

    .labs-actions .btn-primary{
        background: linear-gradient(90deg,#2563eb,#3b82f6);
        border: none;
        box-shadow: 0 10px 24px rgba(37,99,235,.20);
    }

    .labs-actions .btn-danger{
        background: linear-gradient(90deg,#ef4444,#f97316);
        border: none;
        box-shadow: 0 10px 24px rgba(239,68,68,.18);
    }

    .labs-actions .btn-outline-secondary{
        border-color: rgba(15,23,42,.12);
    }

    .labs-dropdown .dropdown-menu{
        border-radius: 14px;
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 18px 45px rgba(2,6,23,.10);
        padding: 6px;
        min-width: 200px;
    }

    .labs-dropdown .dropdown-item{
        border-radius: 10px;
        font-weight: 700;
        padding: 10px 12px;
        display:flex;
        align-items:center;
        gap:10px;
    }

    .labs-dropdown .dropdown-item:hover{
        background: rgba(37,99,235,.08);
    }

    .labs-empty{
        padding: 48px 16px;
        text-align:center;
        color: rgba(15,23,42,.65);
    }

    .labs-empty .icon{
        width: 56px;
        height: 56px;
        border-radius: 16px;
        background: rgba(37,99,235,.10);
        display:inline-flex;
        align-items:center;
        justify-content:center;
        color: #2563eb;
        margin-bottom: 12px;
        font-size: 22px;
    }

    @media(max-width: 992px){
        .labs-table thead{ display:none; }
        .labs-table, .labs-table tbody, .labs-table tr, .labs-table td{
            display:block;
            width:100%;
        }
        .labs-table tr{
            border-bottom: 1px solid rgba(15,23,42,.06);
            padding: 10px 12px;
        }
        .labs-table td{
            padding: 10px 6px;
        }
        .labs-table td::before{
            content: attr(data-label);
            display:block;
            font-weight: 800;
            color: rgba(15,23,42,.70);
            margin-bottom: 4px;
        }
        .labs-actions{
            display:flex;
            gap: 10px;
            flex-wrap: wrap;
        }
    }
</style>

<div class="labs-table-wrap">
    <table class="table labs-table align-middle">
        <thead>
        <tr>
            <th style="width:70px;">#</th>
            <th>Name</th>
            <th>Email</th>
            <th style="width:160px;">Phone</th>
            <th>Address</th>
            <th style="width:110px; text-align:right;">Actions</th>
        </tr>
        </thead>

        <tbody>
        @forelse($labs as $lab)
            <tr>
                <td data-label="#">
                    <span class="labs-muted">#{{ $lab->id }}</span>
                </td>

                <td data-label="Name">
                    <span class="labs-pill" title="{{ $lab->name }}">
                        <i class="fas fa-flask"></i>
                        {{ $lab->name }}
                    </span>
                </td>

                <td data-label="Email">
                    @if($lab->email)
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-envelope text-primary"></i>
                            <span>{{ $lab->email }}</span>
                        </div>
                    @else
                        <span class="labs-muted">—</span>
                    @endif
                </td>

                <td data-label="Phone">
                    @if($lab->phone)
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-phone text-success"></i>
                            <span>{{ $lab->phone }}</span>
                        </div>
                    @else
                        <span class="labs-muted">—</span>
                    @endif
                </td>

                <td data-label="Address">
                    @php($addr = $lab->address ?? '')
                    @if($addr)
                        <span
                            class="labs-muted"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="{{ $addr }}"
                        >
                            {{ \Illuminate\Support\Str::limit($addr, 60) }}
                        </span>
                    @else
                        <span class="labs-muted">—</span>
                    @endif
                </td>

                <td data-label="Actions" style="text-align:right;">
                    <div class="dropdown labs-dropdown">
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius:12px;font-weight:900;">
                            <i class="fas fa-ellipsis-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a href="{{ route('admin.labs.show', $lab->id) }}" class="dropdown-item">
                                    <i class="fas fa-eye text-primary"></i>
                                    View
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.labs.edit', $lab->id) }}" class="dropdown-item">
                                    <i class="fas fa-pen text-primary"></i>
                                    Edit
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <button
                                    type="button"
                                    class="dropdown-item text-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteLabModal"
                                    data-lab-id="{{ $lab->id }}"
                                    data-lab-name="{{ $lab->name }}"
                                >
                                    <i class="fas fa-trash"></i>
                                    Delete
                                </button>
                            </li>
                        </ul>
                    </div>

                    {{-- Hidden delete form (submitted by modal) --}}
                    <form id="delete-lab-form-{{ $lab->id }}" method="POST" action="{{ route('admin.labs.destroy', $lab->id) }}" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6">
                    <div class="labs-empty">
                        <div class="icon"><i class="fas fa-flask"></i></div>
                        <div style="font-weight:900;font-size:1.05rem;">No labs found</div>
                        <div class="labs-muted mt-1">Try changing the search keyword, or create a new lab.</div>
                        <a href="{{ route('admin.labs.create') }}" class="btn btn-success mt-3" style="border-radius:12px;font-weight:900;">
                            <i class="fas fa-plus me-2"></i>Create Lab
                        </a>
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteLabModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(90deg,#ef4444,#f97316); color:#fff;">
                <h5 class="modal-title"><i class="fas fa-triangle-exclamation me-2"></i>Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2" style="font-weight:900;">Are you sure you want to delete this lab?</div>
                <div class="text-muted">Lab: <span id="deleteLabName" style="font-weight:800;"></span></div>
                <div class="text-muted mt-2" style="font-size:.9rem;">
                    This action cannot be undone.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:12px;font-weight:900;">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteLabBtn" style="border-radius:12px;font-weight:900;">
                    <i class="fas fa-trash me-2"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Bootstrap tooltips
    document.addEventListener('DOMContentLoaded', function () {
        try{
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }catch(e){}
    });

    // Delete modal logic
    (function(){
        let selectedLabId = null;

        const modal = document.getElementById('deleteLabModal');
        if(!modal) return;

        modal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            selectedLabId = btn?.getAttribute('data-lab-id');
            const name = btn?.getAttribute('data-lab-name') || '';
            const nameEl = document.getElementById('deleteLabName');
            if(nameEl) nameEl.textContent = name;
        });

        const confirmBtn = document.getElementById('confirmDeleteLabBtn');
        if(confirmBtn){
            confirmBtn.addEventListener('click', function(){
                if(!selectedLabId) return;
                const form = document.getElementById('delete-lab-form-' + selectedLabId);
                if(form) form.submit();
            });
        }
    })();
</script>
