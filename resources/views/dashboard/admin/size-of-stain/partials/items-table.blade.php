@forelse ($sizeOfStains as $sizeOfStain)
    <tr class="fade-in">
        <td>{{ $loop->iteration + ($sizeOfStains->firstItem() - 1) }}</td>
        <td>{{ $sizeOfStain->name['en'] }}</td>
        <td>{{ $sizeOfStain->name['ar'] }}</td>
        <td>{{ $sizeOfStain->serviceCategory ? $sizeOfStain->serviceCategory->name['en'] : 'N/A' }}</td>
        <td>{{ $sizeOfStain->price ? number_format($sizeOfStain->price, 2) : 'N/A' }}</td>
        <td>
            <a href="{{ route('admin.size-of-stain.show', $sizeOfStain) }}" class="btn btn-sm btn-info me-1">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('admin.size-of-stain.edit', $sizeOfStain) }}" class="btn btn-sm btn-warning me-1">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('admin.size-of-stain.destroy', $sizeOfStain) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this size of stain?');">
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
        <td colspan="6" class="text-center text-muted">No size of stains found.</td>
    </tr>
@endforelse