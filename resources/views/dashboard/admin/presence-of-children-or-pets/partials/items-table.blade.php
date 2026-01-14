@forelse ($presenceOfChildrenOrPets as $presence)
    <tr class="fade-in">
        <td>{{ $loop->iteration + ($presenceOfChildrenOrPets->firstItem() - 1) }}</td>
        <td>{{ $presence->name['en'] }}</td>
        <td>{{ $presence->name['ar'] }}</td>
        <td>{{ $presence->serviceCategory ? $presence->serviceCategory->name['en'] : 'N/A' }}</td>
        <td>{{ $presence->price ? number_format($presence->price, 2) : 'N/A' }}</td>
        <td>
            <a href="{{ route('admin.presence-of-children-or-pets.show', $presence) }}" class="btn btn-sm btn-info me-1">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('admin.presence-of-children-or-pets.edit', $presence) }}" class="btn btn-sm btn-warning me-1">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('admin.presence-of-children-or-pets.destroy', $presence) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this presence of children or pets?');">
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
        <td colspan="6" class="text-center text-muted">No presence of children or pets found.</td>
    </tr>
@endforelse
