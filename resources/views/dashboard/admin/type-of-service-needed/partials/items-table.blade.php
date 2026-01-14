@forelse ($typeOfServiceNeeded as $typeOfService)
    <tr class="fade-in">
        <td>{{ $loop->iteration + ($typeOfServiceNeeded->firstItem() - 1) }}</td>
        <td>{{ $typeOfService->name['en'] }}</td>
        <td>{{ $typeOfService->name['ar'] }}</td>
        <td>{{ $typeOfService->serviceCategory ? $typeOfService->serviceCategory->name['en'] : 'N/A' }}</td>
        <td>{{ $typeOfService->price ? number_format($typeOfService->price, 2) : 'N/A' }}</td>
        <td>
            <a href="{{ route('admin.type-of-service-needed.show', $typeOfService) }}" class="btn btn-sm btn-info me-1">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('admin.type-of-service-needed.edit', $typeOfService) }}" class="btn btn-sm btn-warning me-1">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('admin.type-of-service-needed.destroy', $typeOfService) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this type of service needed?');">
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
        <td colspan="6" class="text-center text-muted">No type of service needed found.</td>
    </tr>
@endforelse