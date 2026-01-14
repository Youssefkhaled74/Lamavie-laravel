@forelse ($placeOfTheCleanings as $index => $placeOfTheCleaning)
    @php
        $categoryEn = $placeOfTheCleaning->serviceCategory?->name['en'] ?? 'N/A';
        $categoryAr = $placeOfTheCleaning->serviceCategory?->name['ar'] ?? '';
        $price = $placeOfTheCleaning->price;
        $priceText = $price !== null ? number_format($price, 2) : 'N/A';
    @endphp

    <tr class="pc-row">
        <td class="pc-td-muted">{{ $placeOfTheCleanings->firstItem() + $index }}</td>

        <td>
            <div class="pc-name">
                <span class="pc-lang">EN</span>
                <span class="pc-text">{{ $placeOfTheCleaning->name['en'] }}</span>
            </div>
        </td>

        <td dir="rtl">
            <div class="pc-name">
                <span class="pc-lang pc-ar">AR</span>
                <span class="pc-text">{{ $placeOfTheCleaning->name['ar'] }}</span>
            </div>
        </td>

        <td>
            <div class="pc-chip" title="{{ $categoryEn }}">
                <i class="fas fa-layer-group"></i>
                <span class="pc-chip-text">{{ $categoryEn }}</span>
            </div>
        </td>

        <td>
            <div class="pc-price {{ $price === null ? 'is-empty' : '' }}">
                <i class="fas fa-tag"></i>
                <span>{{ $priceText }}</span>
            </div>
        </td>

        <td class="text-nowrap">
            <div class="pc-actions">
                <a href="{{ route('admin.place-of-the-cleaning.show', $placeOfTheCleaning) }}"
                   class="pc-icon pc-view" title="View">
                    <i class="fas fa-eye"></i>
                </a>

                <a href="{{ route('admin.place-of-the-cleaning.edit', $placeOfTheCleaning) }}"
                   class="pc-icon pc-edit" title="Edit">
                    <i class="fas fa-pen"></i>
                </a>

                <form action="{{ route('admin.place-of-the-cleaning.destroy', $placeOfTheCleaning) }}"
                      method="POST"
                      class="d-inline"
                      onsubmit="return confirm('Are you sure you want to delete this place?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="pc-icon pc-del" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center text-muted py-4">
            No place of the cleaning records found.
        </td>
    </tr>
@endforelse
