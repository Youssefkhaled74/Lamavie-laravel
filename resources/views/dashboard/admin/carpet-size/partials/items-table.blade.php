@forelse ($carpetSizes as $carpetSize)
    <tr class="fade-in">
        <td>{{ $loop->iteration + ($carpetSizes->firstItem() - 1) }}</td>
        <td>{{ $carpetSize->name['en'] }}</td>
        <td>{{ $carpetSize->name['ar'] }}</td>
        <td>{{ $carpetSize->serviceCategory ? $carpetSize->serviceCategory->name['en'] : 'N/A' }}</td>
        <td>{{ $carpetSize->price ? number_format($carpetSize->price, 2) : 'N/A' }}</td>
        <td>
            <a href="{{ route('admin.carpet-size.show', $carpetSize) }}" class="btn btn-sm btn-info me-1">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('admin.carpet-size.edit', $carpetSize) }}" class="btn btn-sm btn-warning me-1">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('admin.carpet-size.destroy', $carpetSize) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this carpet size?');">
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
        <td colspan="6" class="text-center text-muted">No carpet sizes found.</td>
    </tr>
@endforelse