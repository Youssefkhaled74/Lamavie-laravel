@forelse ($paymentMethods as $paymentMethod)
    @php
        $isActive = (bool)$paymentMethod->status;
    @endphp

    <tr class="pm-row">
        <td class="pm-td-muted">
            {{ $loop->iteration + ($paymentMethods->firstItem() - 1) }}
        </td>

        <td>
            <div class="pm-name">
                <div class="pm-lang-pill">EN</div>
                <div class="pm-text">{{ $paymentMethod->name['en'] }}</div>
            </div>
        </td>

        <td>
            <div class="pm-name" dir="rtl">
                <div class="pm-lang-pill pm-ar">AR</div>
                <div class="pm-text">{{ $paymentMethod->name['ar'] }}</div>
            </div>
        </td>

        <td>
            <span class="pm-badge {{ $isActive ? 'pm-active' : 'pm-inactive' }}"
                  data-en="{{ $isActive ? 'Active' : 'Inactive' }}"
                  data-ar="{{ $isActive ? 'نشط' : 'غير نشط' }}">
                <i class="fas {{ $isActive ? 'fa-circle-check' : 'fa-circle-minus' }}"></i>
                {{ $isActive ? 'Active' : 'Inactive' }}
            </span>
        </td>

        <td class="text-nowrap">
            <div class="pm-actions">
                <a href="{{ route('admin.payment-methods.show', $paymentMethod) }}"
                   class="pm-icon-btn pm-view" data-en="View" data-ar="عرض" title="View">
                    <i class="fas fa-eye"></i>
                </a>

                {{-- Toggle Status (same PUT update) --}}
                <form action="{{ route('admin.payment-methods.update', $paymentMethod) }}"
                      method="POST"
                      class="d-inline pm-toggle-form"
                      onsubmit="return confirm('Are you sure you want to change the status of this payment method?');">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="name_en" value="{{ $paymentMethod->name['en'] }}">
                    <input type="hidden" name="name_ar" value="{{ $paymentMethod->name['ar'] }}">
                    <input type="hidden" name="status" value="{{ $isActive ? 0 : 1 }}">

                    <button type="submit"
                            class="pm-switch {{ $isActive ? 'is-on' : '' }}"
                            title="Toggle Status"
                            data-en="Toggle Status" data-ar="تغيير الحالة">
                        <span class="pm-switch-track">
                            <span class="pm-switch-thumb"></span>
                        </span>
                        <span class="pm-switch-label">
                            {{ $isActive ? 'On' : 'Off' }}
                        </span>
                    </button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center text-muted py-4"
            data-en="No payment methods found."
            data-ar="لم يتم العثور على طرق دفع.">
            No payment methods found.
        </td>
    </tr>
@endforelse
