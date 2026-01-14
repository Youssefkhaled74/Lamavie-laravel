@foreach ($maintenanceOrCleanings as $index => $maintenanceOrCleaning)
    <tr class="fade-in">
        <td>{{ $maintenanceOrCleanings->firstItem() + $index }}</td>
        <td>{{ $maintenanceOrCleaning->name['en'] }}</td>
        <td>{{ $maintenanceOrCleaning->name['ar'] }}</td>
        <td>{{ $maintenanceOrCleaning->serviceCategory ? $maintenanceOrCleaning->serviceCategory->name['en'] : 'N/A' }}</td>
        <td>{{ $maintenanceOrCleaning->price ? number_format($maintenanceOrCleaning->price, 2) : 'N/A' }}</td>
        <td>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.maintenance-or-cleaning.show', $maintenanceOrCleaning) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('admin.maintenance-or-cleaning.edit', $maintenanceOrCleaning) }}" class="btn btn-outline-warning btn-sm">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('admin.maintenance-or-cleaning.destroy', $maintenanceOrCleaning) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
@endforeach