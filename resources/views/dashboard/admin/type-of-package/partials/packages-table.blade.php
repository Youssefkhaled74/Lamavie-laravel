@forelse ($typeOfPackages as $typeOfPackage)
    <tr class="fade-in">
        <td>{{ $loop->iteration + ($typeOfPackages->firstItem() - 1) }}</td>
        <td>{{ $typeOfPackage->name['en'] }}</td>
        <td>{{ $typeOfPackage->name['ar'] }}</td>
        <td>{{ $typeOfPackage->serviceCategory ? $typeOfPackage->serviceCategory->name['en'] : 'N/A' }}</td>
        <td>{{ $typeOfPackage->price ? number_format($typeOfPackage->price, 2) : 'N/A' }}</td>
        <td>
            <a href="{{ route('admin.type-of-package.show', $typeOfPackage) }}" class="btn btn-sm btn-info me-1">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('admin.type-of-package.edit', $typeOfPackage) }}" class="btn btn-sm btn-warning me-1">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('admin.type-of-package.destroy', $typeOfPackage) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this package type?');">
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
        <td colspan="6" class="text-center text-muted">No package types found.</td>
    </tr>
@endforelse