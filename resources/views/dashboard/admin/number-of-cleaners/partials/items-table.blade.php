@forelse ($numberOfCleaners as $index => $cleaner)
    <tr class="fade-in">
        <td>{{ $numberOfCleaners->firstItem() + $index }}</td>
        <td>{{ $cleaner->name['en'] }}</td>
        <td>{{ $cleaner->name['ar'] }}</td>
        <td>{{ $cleaner->serviceCategory ? $cleaner->serviceCategory->name['en'] : 'N/A' }}</td>
        <td>{{ $cleaner->price ? number_format($cleaner->price, 2) : 'N/A' }}</td>
        <td>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.number-of-cleaners.show', $cleaner) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('admin.number-of-cleaners.edit', $cleaner) }}" class="btn btn-outline-warning btn-sm">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('admin.number-of-cleaners.destroy', $cleaner) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this number of cleaners?');">
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
        <td colspan="6" class="text-center text-muted">No number of cleaners found.</td>
    </tr>
@endforelse