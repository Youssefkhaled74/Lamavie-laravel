@forelse ($yourItems as $yourItem)
    <tr class="fade-in">
        <td>
            <input type="checkbox" class="row-select" value="{{ $yourItem->id }}">
        </td>
        <td>{{ $loop->iteration + ($yourItems->firstItem() - 1) }}</td>
        <td>
            <div class="fw-semibold text-dark">{{ $yourItem->name['en'] ?? 'N/A' }}</div>
            <div class="text-muted small">{{ $yourItem->name['ar'] ?? 'N/A' }}</div>
        </td>
        <td>
            @if($yourItem->serviceCategory)
                <span class="badge-soft">{{ $yourItem->serviceCategory->name['en'] ?? 'N/A' }}</span>
            @else
                <span class="text-muted">N/A</span>
            @endif
        </td>
        <td>
            @if ($yourItem->logo)
                <span class="logo-frame">
                    <img src="{{ Storage::url($yourItem->logo) }}" alt="{{ $yourItem->name['en'] }}">
                </span>
            @else
                <span class="logo-frame text-muted fw-semibold">
                    {{ strtoupper(substr($yourItem->name['en'] ?? $yourItem->name['ar'] ?? 'N', 0, 1)) }}
                </span>
            @endif
        </td>
        <td class="text-end">
            @php($washingPrice = $yourItem->washing_price ?? $yourItem->price)
            @if($washingPrice)
                <span class="price-pill">{{ number_format($washingPrice, 2) }}</span>
                <button class="btn btn-sm btn-outline-secondary btn-area-prices ms-2" data-base="{{ $washingPrice }}" data-label="Washing" type="button">Prices</button>
            @else
                <span class="text-muted">N/A</span>
            @endif
        </td>
        <td class="text-end">
            @if($yourItem->ironing_price)
                <span class="price-pill">{{ number_format($yourItem->ironing_price, 2) }}</span>
                <button class="btn btn-sm btn-outline-secondary btn-area-prices ms-2" data-base="{{ $yourItem->ironing_price }}" data-label="Ironing" type="button">Prices</button>
            @else
                <span class="text-muted">N/A</span>
            @endif
        </td>
        <td class="text-end action-group">
            <a href="{{ route('admin.your-items.show', $yourItem) }}" class="btn btn-sm btn-outline-info me-1" title="View">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('admin.your-items.edit', $yourItem) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('admin.your-items.destroy', $yourItem) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this item?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center text-muted" data-en="No items found." data-ar="لا توجد عناصر.">No items found.</td>
    </tr>
@endforelse
