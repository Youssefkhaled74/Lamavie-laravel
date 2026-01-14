@php
    $currency = config('app.currency', 'SAR');
    $invoicePayload = $payload ?? [];

    $svcName = data_get($booking, 'service.name');
    if (is_array($svcName)) $svcName = $svcName[app()->getLocale()] ?? ($svcName['en'] ?? reset($svcName));

    $customerLabel = data_get($booking, 'user.name') ?: data_get($booking, 'user.phone', '-');

    $items = $invoicePayload['item'] ?? null;
    $prices = $invoicePayload['price'] ?? null;
    $qtys = $invoicePayload['quantity'] ?? null;

    $lineItems = [];
    $itemNames = [0 => 'Wash & Fold', 1 => 'Ironing', 2 => 'Dry Clean'];

    if (is_array($items) && is_array($prices) && is_array($qtys) && count($items) === count($prices) && count($items) === count($qtys)) {
        foreach ($items as $i => $it) {
            $name = $itemNames[$it] ?? (is_string($it) ? $it : "Item #{$i}");
            $q = isset($qtys[$i]) ? (int) $qtys[$i] : 0;
            $p = isset($prices[$i]) ? (float) $prices[$i] : 0.0;
            $lineItems[] = [
                'name' => $name,
                'qty' => $q,
                'price' => $p,
                'subtotal' => $q * $p,
            ];
        }
    }

    $total = (float) ($booking->total ?? ($invoicePayload['total'] ?? 0));

    $isCarWash = isset($invoicePayload['car_wash_type']) || isset($invoicePayload['number_of_cars']) || isset($invoicePayload['cars_additional_services']) || isset($invoicePayload['additional_services']);
    $carAdditional = $invoicePayload['cars_additional_services'] ?? $invoicePayload['additional_services'] ?? [];
    if (!is_array($carAdditional)) $carAdditional = [$carAdditional];
@endphp

<div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white no-print">
                <h5 class="modal-title" id="invoiceModalLabel">
                    <i class="fa-solid fa-file-invoice me-2"></i>
                    <span class="lang-en">Invoice</span>
                    <span class="lang-ar">الفاتورة</span>
                    - #{{ $booking->order_number ?? $booking->id }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="drv-invoice" id="invoicePrintRoot">
                    <div class="drv-invoice-head">
                        <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                            <div>
                                <div class="fw-bold fs-5">
                                    {{ config('app.name') }}
                                </div>
                                <div class="text-muted small">
                                    {{ optional($booking->created_at)->toDayDateTimeString() }}
                                </div>
                            </div>

                            <div class="text-end">
                                <div class="fw-bold">
                                    <span class="lang-en">Total</span>
                                    <span class="lang-ar">الإجمالي</span>
                                </div>
                                <div class="fs-4 fw-bold text-primary">
                                    {{ number_format($total, 2) }} {{ $currency }}
                                </div>
                            </div>
                        </div>

                        <div class="drv-invoice-meta mt-3">
                            <span class="pill">
                                <i class="fa-solid fa-hashtag"></i>
                                <span class="lang-en">Booking</span>
                                <span class="lang-ar">الطلب</span>
                                : #{{ $booking->order_number ?? $booking->id }}
                            </span>
                            <span class="pill">
                                <i class="fa-solid fa-user"></i>
                                <span class="lang-en">Customer</span>
                                <span class="lang-ar">العميل</span>
                                : {{ $customerLabel }}
                            </span>
                            <span class="pill">
                                <i class="fa-solid fa-receipt"></i>
                                <span class="lang-en">Service</span>
                                <span class="lang-ar">الخدمة</span>
                                : {{ $svcName }}
                            </span>
                        </div>
                    </div>

                    <div class="p-3">
                        @if(count($lineItems))
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:48%">
                                                <span class="lang-en">Item</span>
                                                <span class="lang-ar">البند</span>
                                            </th>
                                            <th style="width:16%">
                                                <span class="lang-en">Qty</span>
                                                <span class="lang-ar">الكمية</span>
                                            </th>
                                            <th style="width:18%">
                                                <span class="lang-en">Price</span>
                                                <span class="lang-ar">السعر</span>
                                            </th>
                                            <th style="width:18%">
                                                <span class="lang-en">Subtotal</span>
                                                <span class="lang-ar">المجموع</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $sum = 0; @endphp
                                        @foreach($lineItems as $it)
                                            @php $sum += (float) $it['subtotal']; @endphp
                                            <tr>
                                                <td class="fw-medium">{{ $it['name'] }}</td>
                                                <td>{{ $it['qty'] }}</td>
                                                <td>{{ number_format($it['price'], 2) }}</td>
                                                <td class="fw-semibold">{{ number_format($it['subtotal'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold">
                                                <span class="lang-en">Total</span>
                                                <span class="lang-ar">الإجمالي</span>
                                            </td>
                                            <td class="fw-bold">{{ number_format($sum, 2) }} {{ $currency }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @elseif($isCarWash)
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="border rounded-4 p-3 h-100">
                                        <div class="fw-bold mb-2">
                                            <span class="lang-en">Car wash details</span>
                                            <span class="lang-ar">تفاصيل غسيل السيارات</span>
                                        </div>
                                        <div class="text-muted">
                                            <div><strong>Type:</strong> {{ $invoicePayload['car_wash_type'] ?? '-' }}</div>
                                            <div><strong>Cars:</strong> {{ $invoicePayload['number_of_cars'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded-4 p-3 h-100">
                                        <div class="fw-bold mb-2">
                                            <span class="lang-en">Additional services</span>
                                            <span class="lang-ar">خدمات إضافية</span>
                                        </div>
                                        @if(count(array_filter($carAdditional, fn($x) => (string)$x !== '')))
                                            <ul class="mb-0">
                                                @foreach($carAdditional as $svc)
                                                    @if((string)$svc !== '')
                                                        <li>{{ is_scalar($svc) ? $svc : json_encode($svc, JSON_UNESCAPED_UNICODE) }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="text-muted">
                                                <span class="lang-en">None</span>
                                                <span class="lang-ar">لا يوجد</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-muted">
                                <span class="lang-en">No detailed invoice items were found for this booking.</span>
                                <span class="lang-ar">لا توجد عناصر فاتورة تفصيلية لهذا الطلب.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="modal-footer no-print">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:14px;">
                    <span class="lang-en">Close</span>
                    <span class="lang-ar">إغلاق</span>
                </button>
                <button type="button" class="btn btn-primary" id="invoicePrintBtn" style="border-radius:14px;">
                    <i class="fa-solid fa-print me-2"></i>
                    <span class="lang-en">Print</span>
                    <span class="lang-ar">طباعة</span>
                </button>
            </div>
        </div>
    </div>
</div>
