@forelse ($settings as $index => $setting)
    @php
        $restrict = (int) env('RESTRICT_SETTINGS', 1); // 0 = restricted actions
        $canManage = $restrict !== 0;
        $value = $setting->value ?? 'N/A';
    @endphp

    <tr class="st-row">
        <td class="st-td-muted">{{ $settings->firstItem() + $index }}</td>

        <td>
            <div class="st-name">
                <span class="st-lang-pill">EN</span>
                <span class="st-text">{{ $setting->name['en'] }}</span>
            </div>
        </td>

        <td dir="rtl">
            <div class="st-name">
                <span class="st-lang-pill st-ar">AR</span>
                <span class="st-text">{{ $setting->name['ar'] }}</span>
            </div>
        </td>

        <td>
            <span class="st-key" title="{{ $setting->key }}">
                <i class="fas fa-key me-1"></i>{{ $setting->key }}
            </span>
        </td>

        <td>
            <span class="st-value {{ $value === 'N/A' ? 'is-empty' : '' }}" title="{{ $value }}">
                {{ $value }}
            </span>
        </td>

        <td class="text-nowrap">
            <div class="st-actions">
                <a href="{{ route('admin.settings.show', $setting) }}"
                   class="st-icon-btn st-view" data-en="View" data-ar="عرض" title="View">
                    <i class="fas fa-eye"></i>
                </a>

                <a href="{{ route('admin.settings.edit', $setting) }}"
                   class="st-icon-btn st-edit {{ !$canManage ? 'st-disabled' : '' }}"
                   data-restrict="{{ $restrict }}"
                   data-en="Edit" data-ar="تعديل"
                   title="{{ $canManage ? 'Edit' : 'Restricted' }}"
                   @if(!$canManage) aria-disabled="true" @endif>
                    <i class="fas fa-pen"></i>
                </a>

                <form action="{{ route('admin.settings.destroy', $setting) }}"
                      method="POST"
                      class="d-inline st-del-form"
                      @if($canManage) onsubmit="return confirm('Are you sure you want to delete this setting?');" @endif>
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="st-icon-btn st-del {{ !$canManage ? 'st-disabled' : '' }}"
                            data-restrict="{{ $restrict }}"
                            data-en="Delete" data-ar="حذف"
                            title="{{ $canManage ? 'Delete' : 'Restricted' }}"
                            @if(!$canManage) disabled @endif>
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center text-muted py-4"
            data-en="No settings found."
            data-ar="لم يتم العثور على إعدادات.">
            No settings found.
        </td>
    </tr>
@endforelse
