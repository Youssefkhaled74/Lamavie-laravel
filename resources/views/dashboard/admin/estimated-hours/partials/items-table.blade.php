@forelse ($estimatedHours as $index => $hours)
    <tr class="fade-in">
        <td>{{ $estimatedHours->firstItem() + $index }}</td>
        <td>{{ $hours->name['en'] }}</td>
        <td>{{ $hours->name['ar'] }}</td>
        <td>{{ $hours->serviceCategory ? $hours->serviceCategory->name['en'] : 'N/A' }}</td>
        <td>{{ $hours->price ? number_format($hours->price, 2) : 'N/A' }}</td>
        <td>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.estimated-hours.show', $hours) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('admin.estimated-hours.edit', $hours) }}" class="btn btn-outline-warning btn-sm">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('admin.estimated-hours.destroy', $hours) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this estimated hours?');">
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
        <td colspan="6" class="text-center text-muted">No estimated hours found.</td>
    </tr>
@endforelse