@forelse ($fabricTypes as $fabricType)
    <tr class="fade-in">
        <td>{{ $loop->iteration + ($fabricTypes->firstItem() - 1) }}</td>
        <td>{{ $fabricType->name['en'] }}</td>
        <td>{{ $fabricType->name['ar'] }}</td>
        <td>{{ $fabricType->serviceCategory ? $fabricType->serviceCategory->name['en'] : 'N/A' }}</td>
        <td>{{ $fabricType->price ? number_format($fabricType->price, 2) : 'N/A' }}</td>
        <td>
            <a href="{{ route('admin.fabric-type.show', $fabricType) }}" class="btn btn-sm btn-info me-1">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('admin.fabric-type.edit', $fabricType) }}" class="btn btn-sm btn-warning me-1">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('admin.fabric-type.destroy', $fabricType) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this fabric type?');">
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
        <td colspan="6" class="text-center text-muted">No fabric types found.</td>
    </tr>
@endforelse