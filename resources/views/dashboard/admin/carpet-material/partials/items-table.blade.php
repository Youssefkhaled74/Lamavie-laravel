@forelse ($carpetMaterials as $carpetMaterial)
    <tr class="fade-in">
        <td>{{ $loop->iteration + ($carpetMaterials->firstItem() - 1) }}</td>
        <td>{{ $carpetMaterial->name['en'] }}</td>
        <td>{{ $carpetMaterial->name['ar'] }}</td>
        <td>{{ $carpetMaterial->serviceCategory ? $carpetMaterial->serviceCategory->name['en'] : 'N/A' }}</td>
        <td>{{ $carpetMaterial->price ? number_format($carpetMaterial->price, 2) : 'N/A' }}</td>
        <td>
            <a href="{{ route('admin.carpet-material.show', $carpetMaterial) }}" class="btn btn-sm btn-info me-1">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('admin.carpet-material.edit', $carpetMaterial) }}" class="btn btn-sm btn-warning me-1">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('admin.carpet-material.destroy', $carpetMaterial) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this carpet material?');">
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
        <td colspan="6" class="text-center text-muted">No carpet materials found.</td>
    </tr>
@endforelse