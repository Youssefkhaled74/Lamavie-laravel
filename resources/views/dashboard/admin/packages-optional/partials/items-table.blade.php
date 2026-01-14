@forelse ($packagesOptional as $index => $package)
    <tr class="fade-in">
        <td>{{ $packagesOptional->firstItem() + $index }}</td>
        <td>{{ $package->name['en'] }}</td>
        <td>{{ $package->name['ar'] }}</td>
        <td>{{ $package->serviceCategory ? $package->serviceCategory->name['en'] : 'N/A' }}</td>
        <td>{{ $package->price ? number_format($package->price, 2) : 'N/A' }}</td>
        <td>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.packages-optional.show', $package) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('admin.packages-optional.edit', $package) }}" class="btn btn-outline-warning btn-sm">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('admin.packages-optional.destroy', $package) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this package?');">
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
        <td colspan="6" class="text-center text-muted">No optional packages found.</td>
    </tr>
@endforelse