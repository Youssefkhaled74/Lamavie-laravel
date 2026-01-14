<div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="invoiceModalLabel"><i class="fas fa-file-invoice me-2"></i>Invoice - Booking #{{ $booking->order_number }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @php
                    $payload = is_array($booking->payload_data) ? $booking->payload_data : (array) $booking->payload_data;
                    $items = $payload['item'] ?? null;
                    $prices = $payload['price'] ?? null;
                    $qtys = $payload['quantity'] ?? null;
                    $itemNames = [0 => 'مصبغة', 1 => 'تنظيف', 2 => 'بطانية'];
                @endphp

                <div class="mb-3">
                    <label for="invoice-status-select" class="form-label">Select new status to include in invoice (shows old → new)</label>
                    <select id="invoice-status-select" class="form-select">
                        @php $statuses = ['pending' => 'Pending','pickup' => 'Pickup','delivered' => 'Delivered','canceled' => 'Canceled']; @endphp
                        @foreach($statuses as $k => $label)
                            <option value="{{ $k }}" {{ $booking->status === $k ? 'selected' : '' }}>{{ $label }} ({{ $k }})</option>
                        @endforeach
                    </select>
                    <div class="small text-muted mt-1">Old status: <strong>{{ $booking->status }}</strong></div>
                </div>

                @php
                    $isCarWash = isset($payload['car_wash_type']) || isset($payload['number_of_cars']) || isset($payload['cars_additional_services']) || isset($payload['additional_services']);
                    $additionalServices = $payload['cars_additional_services'] ?? $payload['additional_services'] ?? null;

                    $renderVal = function($v) {
                        if (is_null($v)) return '—';
                        if (is_array($v) || is_object($v)) {
                            try {
                                $arr = (array) $v;
                                $locale = app()->getLocale() ?? config('app.locale', 'en');
                                if (isset($arr[$locale]) && is_string($arr[$locale]) && $arr[$locale] !== '') return $arr[$locale];
                                if (isset($arr['ar']) && is_string($arr['ar']) && $arr['ar'] !== '') return $arr['ar'];
                                if (isset($arr['en']) && is_string($arr['en']) && $arr['en'] !== '') return $arr['en'];
                                foreach ($arr as $val) {
                                    if (is_string($val) && $val !== '') return $val;
                                }
                                return json_encode($arr, JSON_UNESCAPED_UNICODE);
                            } catch (\Throwable $e) {
                                return '—';
                            }
                        }
                        return (string) $v;
                    };

                    // Arabic-only renderer (handles arrays/objects or JSON strings)
                    $renderArabic = function($v) use ($renderVal) {
                        if (is_null($v)) return '—';
                        if (is_array($v) || is_object($v)) {
                            $arr = (array) $v;
                            if (isset($arr['ar']) && $arr['ar'] !== '') return $arr['ar'];
                            // fallback to any string
                            foreach ($arr as $val) {
                                if (is_string($val) && $val !== '') return $val;
                            }
                            return json_encode($arr, JSON_UNESCAPED_UNICODE);
                        }
                        if (is_string($v)) {
                            $trim = trim($v);
                            if (strlen($trim) > 0 && ($trim[0] === '{' || $trim[0] === '[')) {
                                $decoded = json_decode($trim, true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                    if (isset($decoded['ar']) && $decoded['ar'] !== '') return $decoded['ar'];
                                    foreach ($decoded as $val) {
                                        if (is_string($val) && $val !== '') return $val;
                                    }
                                }
                            }
                            return $v;
                        }
                        return (string) $v;
                    };

                    // Safe date display
                    $displayDate = '—';
                    if (!empty($payload['date']) && is_string($payload['date'])) {
                        try {
                            $displayDate = \Carbon\Carbon::parse($payload['date'])->format('d/m/Y');
                        } catch (\Exception $e) {
                            $displayDate = $renderVal($payload['date']);
                        }
                    } elseif ($booking->created_at) {
                        $displayDate = $booking->created_at->format('d/m/Y');
                    }
                @endphp

                @if($isCarWash)
                    <div class="enhanced-order-card p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 text-primary"><i class="fas fa-car-side me-2"></i>تفاصيل غسيل السيارات</h5>
                        </div>
                        @php
                            // human-friendly labels for car_wash_type codes
                            $washLabels = [
                                'normalWash' => 'غسيل عادي',
                                'premiumWash' => 'غسيل مميز',
                                'interiorWash' => 'تنظيف داخلي',
                                'exteriorWash' => 'تنظيف خارجي',
                            ];
                            $cwType = $payload['car_wash_type'] ?? null;
                            $cwLabel = $washLabels[$cwType] ?? $renderArabic($cwType ?? $payload['car_wash_type'] ?? null);
                            // normalize additional services (string -> array)
                            if (is_string($additionalServices) && trim($additionalServices) !== '') {
                                $parts = preg_split('/[,\n]+/', trim($additionalServices));
                                $additionalServices = array_values(array_filter(array_map('trim', $parts)));
                            }
                        @endphp

                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-item mb-2"><strong style="display:inline-block;width:130px;text-align:right">نوع الغسيل:</strong> <span class="text-muted">{{ $cwLabel }}</span></div>
                                <div class="detail-item mb-2"><strong style="display:inline-block;width:130px;text-align:right">عدد السيارات:</strong> <span class="text-muted">{{ $renderArabic($payload['number_of_cars'] ?? null) }}</span></div>
                                <div class="detail-item mb-2"><strong style="display:inline-block;width:130px;text-align:right">مكان التنظيف:</strong> <span class="text-muted">{{ $renderArabic($payload['place_of_cleaning'] ?? $payload['place'] ?? null) }}</span></div>
                                <div class="detail-item mb-2"><strong style="display:inline-block;width:130px;text-align:right">طريقة الدفع:</strong> <span class="text-muted">{{ $renderArabic(data_get($booking, 'paymentMethod.name') ?? data_get($booking, 'payment_method')) }}</span></div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item mb-2"><strong style="display:inline-block;width:120px;text-align:right">اسم العميل:</strong> <span class="text-muted">{{ $renderVal(data_get($booking, 'user.name')) }}</span></div>
                                <div class="detail-item mb-2"><strong style="display:inline-block;width:120px;text-align:right">هاتف العميل:</strong> <span class="text-muted">{{ $renderVal(data_get($booking, 'user.phone')) }}</span></div>
                                <div class="detail-item mb-2"><strong style="display:inline-block;width:120px;text-align:right">عنوان:</strong> <span class="text-muted">{{ $renderVal($payload['address'] ?? $payload['delivery_location'] ?? null) }}</span></div>
                                <div class="detail-item mb-2"><strong style="display:inline-block;width:120px;text-align:right">التاريخ:</strong> <span class="text-muted">{{ $displayDate }}</span></div>
                            </div>
                        </div>

                        @if(is_array($additionalServices) && count($additionalServices) > 0)
                            <div class="table-responsive mt-3">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr class="table-light">
                                            <th>الخدمة</th>
                                            <th style="width:120px">الكمية</th>
                                            <th style="width:140px">سعر الوحدة ({{ config('app.currency', 'SAR') }})</th>
                                            <th style="width:140px">المجموع</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $grand = 0; @endphp
                                        @foreach($additionalServices as $s)
                                            @php
                                                if(is_array($s) || is_object($s)) {
                                                    $sArr = (array) $s;
                                                    $sName = $sArr['name'] ?? $sArr['title'] ?? json_encode($sArr, JSON_UNESCAPED_UNICODE);
                                                    $sQty = isset($sArr['quantity']) ? (int)$sArr['quantity'] : (isset($sArr['qty']) ? (int)$sArr['qty'] : 1);
                                                    $sUnit = isset($sArr['price']) ? (float)$sArr['price'] : (isset($sArr['unit_price']) ? (float)$sArr['unit_price'] : 0.0);
                                                } else {
                                                    $sName = (string) $s;
                                                    $sQty = 1;
                                                    $sUnit = 0.0;
                                                }
                                                $sSub = $sQty * $sUnit;
                                                $grand += $sSub;
                                            @endphp
                                            <tr>
                                                <td class="fw-medium">{{ $sName }}</td>
                                                <td>{{ $sQty }}</td>
                                                <td>{{ number_format($sUnit, 2) }}</td>
                                                <td class="fw-semibold">{{ number_format($sSub, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold">المجموع الفرعي</td>
                                            <td class="fw-bold">{{ number_format($grand, 2) }} {{ config('app.currency', 'SAR') }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold">المجموع (الحجز)</td>
                                            <td class="fw-bold">{{ number_format($booking->total ?? ($payload['total'] ?? 0), 2) }} {{ config('app.currency', 'SAR') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="mt-3"><em class="text-muted">لا توجد خدمات إضافية مفصّلة.</em></div>
                            <div class="mt-2"><strong>المجموع (الحجز):</strong> <span class="fw-bold">{{ number_format($booking->total ?? ($payload['total'] ?? 0), 2) }} {{ config('app.currency', 'SAR') }}</span></div>
                        @endif
                    </div>

                @elseif(is_array($items) && is_array($prices) && is_array($qtys) && count($items) === count($prices) && count($items) === count($qtys))
                    <div class="enhanced-order-card p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 text-primary"><i class="fas fa-shopping-cart me-2"></i>تفاصيل الطلب</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr class="table-light">
                                        <th style="width:48%">الخدمة</th>
                                        <th style="width:16%">الكمية</th>
                                        <th style="width:18%">سعر الوحدة ({{ config('app.currency', 'SAR') }})</th>
                                        <th style="width:18%">المجموع</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $grand = 0; @endphp
                                    @foreach($items as $i => $it)
                                        @php
                                            $name = $itemNames[$it] ?? (is_string($it) ? $it : "Item #{$i}");
                                            $q = isset($qtys[$i]) ? (int)$qtys[$i] : 0;
                                            $p = isset($prices[$i]) ? (float)$prices[$i] : 0.0;
                                            $sub = $q * $p;
                                            $grand += $sub;
                                        @endphp
                                        <tr>
                                            <td class="fw-medium">{{ $name }}</td>
                                            <td>{{ $q }}</td>
                                            <td>{{ number_format($p, 2) }}</td>
                                            <td class="fw-semibold">{{ number_format($sub, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">المجموع الكلي</td>
                                        <td class="fw-bold">{{ number_format($grand, 2) }} {{ config('app.currency', 'SAR') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="detail-item mb-2"><i class="fas fa-box-open text-primary me-2"></i><strong>Clothes Returned:</strong> <span class="text-muted">{{ $payload['clothes_returned'] ?? '—' }}</span></div>
                                <div class="detail-item mb-2"><i class="fas fa-map-pin text-primary me-2"></i><strong>Pickup Location:</strong> <span class="text-muted">{{ $payload['pickup_location'] ?? '—' }}</span></div>
                                <div class="detail-item mb-2"><i class="fas fa-map-marker-alt text-primary me-2"></i><strong>Delivery Location:</strong> <span class="text-muted">{{ $payload['delivery_location'] ?? '—' }}</span></div>
                            </div>
                            <div class="col-md-6">
                                @php
                                    $pickupRaw = $payload['pickup_date'] ?? null;
                                    $pickupDate = null;
                                    try {
                                        if ($pickupRaw) $pickupDate = \Carbon\Carbon::parse($pickupRaw);
                                    } catch (Exception $e) {
                                        $pickupDate = null;
                                    }
                                    $today = \Carbon\Carbon::today();
                                @endphp
                                <div class="detail-item mb-2"><i class="fas fa-calendar-alt text-primary me-2"></i><strong>Pickup Date:</strong>
                                    <span class="text-muted">
                                        @if($pickupDate)
                                            {{ $pickupDate->format('d/m/Y') }} @if($pickupDate->isSameDay($today)) <span class="badge bg-info text-dark ms-2">Today</span> @endif
                                        @else
                                            —
                                        @endif
                                    </span>
                                </div>
                                <div class="detail-item mb-2"><i class="fas fa-clock text-primary me-2"></i><strong>Pickup Time:</strong> <span class="text-muted">{{ $payload['pickup_time'] ?? '—' }}</span></div>
                            </div>
                        </div>
                    </div>
                @else
                    <pre class="small text-muted">No structured items to show in invoice. Showing raw payload for inspection:
{{ json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @endif
            </div>
            <div class="modal-footer">
                <a id="downloadInvoiceBtn" class="btn btn-primary" href="#" target="_blank"><i class="fas fa-download me-2"></i>Download PDF</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
            </div>

            <script>
                (function(){
                    var base = "{{ route('admin.bookings.invoice', $booking) }}";
                    var select = document.getElementById('invoice-status-select');
                    var link = document.getElementById('downloadInvoiceBtn');
                    function updateLink(){
                        if(!link) return;
                        var s = select ? select.value : '';
                        var url = base;
                        if(s) url = base + '?status=' + encodeURIComponent(s);
                        link.setAttribute('href', url);
                    }
                    if(select && link){
                        updateLink();
                        select.addEventListener('change', updateLink);
                        var modal = document.getElementById('invoiceModal');
                        if(modal){
                            modal.addEventListener('show.bs.modal', updateLink);
                        }
                    } else if(link) {
                        // fallback
                        link.setAttribute('href', base);
                    }
                })();
            </script>
