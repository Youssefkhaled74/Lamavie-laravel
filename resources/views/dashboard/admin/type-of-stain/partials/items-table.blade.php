@forelse ($typeOfStains as $typeOfStain)
    <tr class="fade-in">
        <td>{{ $loop->iteration + ($typeOfStains->firstItem() - 1) }}</td>
        <td>{{ $typeOfStain->name['en'] }}</td>
        <td>{{ $typeOfStain->name['ar'] }}</td>
        <td>{{ $typeOfStain->serviceCategory ? $typeOfStain->serviceCategory->name['en'] : 'N/A' }}</td>
        <td>{{ $typeOfStain->price ? number_format($typeOfStain->price, 2) : 'N/A' }}</td>
        <td>
            <a href="{{ route('admin.type-of-stain.show', $typeOfStain) }}" class="btn btn-sm btn-info me-1">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('admin.type-of-stain.edit', $typeOfStain) }}" class="btn btn-sm btn-warning me-1">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('admin.type-of-stain.destroy', $typeOfStain) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this type of stain?');">
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
        <td colspan="6" class="text-center text-muted">No type of stains found.</td>
    </tr>
@endforelse