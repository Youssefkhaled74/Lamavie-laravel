@forelse ($yourItems as $yourItem)
    <tr class="fade-in">
        <td>{{ $loop->iteration + ($yourItems->firstItem() - 1) }}</td>
        <td>{{ $yourItem->name['en'] }}</td>
        <td>{{ $yourItem->name['ar'] }}</td>
        <td>{{ $yourItem->serviceCategory ? $yourItem->serviceCategory->name['en'] : 'N/A' }}</td>
        <td>
            @if ($yourItem->logo)
                <img src="{{ Storage::url($yourItem->logo) }}" alt="{{ $yourItem->name['en'] }}" style="max-width: 50px; max-height: 50px;">
            @else
                N/A
            @endif
        </td>
        <td>
            @php($washingPrice = $yourItem->washing_price ?? $yourItem->price)
            @if($washingPrice)
                {{ number_format($washingPrice, 2) }}
                <button class="btn btn-sm btn-outline-secondary btn-area-prices ms-2" data-base="{{ $washingPrice }}" type="button">Prices</button>
            @else
                N/A
            @endif
        </td>
        <td>
            @if($yourItem->ironing_price)
                {{ number_format($yourItem->ironing_price, 2) }}
            @else
                N/A
            @endif
        </td>
        <td>
            <a href="{{ route('admin.your-items.show', $yourItem) }}" class="btn btn-sm btn-info me-1">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('admin.your-items.edit', $yourItem) }}" class="btn btn-sm btn-warning me-1">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('admin.your-items.destroy', $yourItem) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this item?');">
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
        <td colspan="8" class="text-center text-muted">No items found.</td>
    </tr>
@endforelse
