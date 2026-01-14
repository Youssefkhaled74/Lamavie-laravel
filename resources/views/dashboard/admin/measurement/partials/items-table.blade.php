@forelse ($measurements as $measurement)
    <tr class="fade-in">
        <td>{{ $loop->iteration + ($measurements->firstItem() - 1) }}</td>
        <td>{{ $measurement->name['en'] }}</td>
        <td>{{ $measurement->name['ar'] }}</td>
        <td>{{ $measurement->serviceCategory ? $measurement->serviceCategory->name['en'] : 'N/A' }}</td>
        <td>{{ $measurement->price ? number_format($measurement->price, 2) : 'N/A' }}</td>
        <td>
            <a href="{{ route('admin.measurement.show', $measurement) }}" class="btn btn-sm btn-info me-1">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('admin.measurement.edit', $measurement) }}" class="btn btn-sm btn-warning me-1">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('admin.measurement.destroy', $measurement) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this measurement?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center text-muted">No measurements found.</td>
    </tr>
@endforelse 