<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th data-en="#" data-ar="#">#</th>
                <th data-en="Name" data-ar="الاسم">Name</th>
                <th data-en="Email" data-ar="البريد الإلكتروني">Email</th>
                <th class="text-end" data-en="Actions" data-ar="الإجراءات">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($drivers as $driver)
                <tr>
                    <td>{{ $driver->id }}</td>
                    <td class="fw-bold">{{ $driver->name }}</td>
                    <td class="text-muted">{{ $driver->email }}</td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-2 flex-wrap">
                            <a href="{{ route('admin.drivers.show', $driver) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                                <span class="ms-1" data-en="View" data-ar="عرض">View</span>
                            </a>
                            <a href="{{ route('admin.drivers.edit', $driver) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-pen"></i>
                                <span class="ms-1" data-en="Edit" data-ar="تعديل">Edit</span>
                            </a>
                            <form action="{{ route('admin.drivers.destroy', $driver) }}" method="POST"
                                  onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                    <span class="ms-1" data-en="Delete" data-ar="حذف">Delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4"
                        data-en="No drivers found." data-ar="لم يتم العثور على سائقين.">
                        No drivers found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
