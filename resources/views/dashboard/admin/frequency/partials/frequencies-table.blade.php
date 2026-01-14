@forelse ($frequencies as $frequency)
    <tr class="fade-in">
        <td>{{ $loop->iteration + ($frequencies->firstItem() - 1) }}</td>
        <td>{{ $frequency->name['en'] }}</td>
        <td>{{ $frequency->name['ar'] }}</td>
        <td>{{ $frequency->serviceCategory ? $frequency->serviceCategory->name['en'] : 'N/A' }}</td>
        <td>{{ $frequency->price ? number_format($frequency->price, 2) : 'N/A' }}</td>
        <td>
            <a href="{{ route('admin.frequency.show', $frequency) }}" class="btn btn-sm btn-info me-1">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('admin.frequency.edit', $frequency) }}" class="btn btn-sm btn-warning me-1">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('admin.frequency.destroy', $frequency) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this frequency?');">
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
        <td colspan="6" class="text-center text-muted">No frequencies found.</td>
    </tr>
@endforelse