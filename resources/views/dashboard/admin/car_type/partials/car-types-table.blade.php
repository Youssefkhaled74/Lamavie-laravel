@forelse ($carTypes as $carType)
    <tr class="fade-in">
        <td>{{ $loop->iteration + ($carTypes->firstItem() - 1) }}</td>
        <td>{{ $carType->name['en'] }}</td>
        <td>{{ $carType->name['ar'] }}</td>
        <td>{{ $carType->serviceCategory ? $carType->serviceCategory->name['en'] : 'N/A' }}</td>
        <td>{{ $carType->price ? number_format($carType->price, 2) : 'N/A' }}</td>
        <td>
            <a href="{{ route('admin.car_type.show', $carType) }}" class="btn btn-sm btn-info me-1">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('admin.car_type.edit', $carType) }}" class="btn btn-sm btn-warning me-1">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('admin.car_type.destroy', $carType) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this car type?');">
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
        <td colspan="6" class="text-center text-muted">No car types found.</td>
    </tr>
@endforelse
