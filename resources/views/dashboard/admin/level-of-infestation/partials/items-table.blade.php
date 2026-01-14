@forelse ($levelOfInfestation as $level)
    <tr class="fade-in">
        <td>{{ $loop->iteration + ($levelOfInfestation->firstItem() - 1) }}</td>
        <td>{{ $level->name['en'] }}</td>
        <td>{{ $level->name['ar'] }}</td>
        <td>{{ $level->serviceCategory ? $level->serviceCategory->name['en'] : 'N/A' }}</td>
        <td>{{ $level->price ? number_format($level->price, 2) : 'N/A' }}</td>
        <td>
            <a href="{{ route('admin.level-of-infestation.show', $level) }}" class="btn btn-sm btn-info me-1">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('admin.level-of-infestation.edit', $level) }}" class="btn btn-sm btn-warning me-1">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('admin.level-of-infestation.destroy', $level) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this level of infestation?');">
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
        <td colspan="6" class="text-center text-muted">No level of infestation found.</td>
    </tr>
@endforelse