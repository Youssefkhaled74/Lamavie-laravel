@forelse ($carsAdditionalServices as $index => $carsAdditionalService)
    <tr class="fade-in">
        <td>{{ $carsAdditionalServices->firstItem() + $index }}</td>
        <td>{{ $carsAdditionalService->name['en'] }}</td>
        <td>{{ $carsAdditionalService->name['ar'] }}</td>
        <td>{{ $carsAdditionalService->serviceCategory ? $carsAdditionalService->serviceCategory->name['en'] : 'N/A' }}</td>
        <td>{{ $carsAdditionalService->price ? number_format($carsAdditionalService->price, 2) : 'N/A' }}</td>
        <td>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.cars-additional-service.show', $carsAdditionalService) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('admin.cars-additional-service.edit', $carsAdditionalService) }}" class="btn btn-outline-warning btn-sm">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('admin.cars-additional-service.destroy', $carsAdditionalService) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this service?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center text-muted">No cars additional services found.</td>
    </tr>
@endforelse