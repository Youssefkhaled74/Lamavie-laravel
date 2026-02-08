<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use App\Models\Area;

class YourItemsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $getLocalizedValue = function ($value) {
            $locale = App::getLocale();
            if (is_array($value)) {
                return $value[$locale] ?? $value['en'] ?? $value['ar'] ?? reset($value) ?? $value;
            }
            return $value;
        };

        // Adjust price according to authenticated user's area percentage (if any)
        $adjustPrice = function ($price) use ($request) {
            if ($price === null) {
                return null;
            }
            $percent = optional(optional($request->user())->area)->price_increase_percentage ?? 0;
            $final = (float) $price * (1 + ((float) $percent / 100));
            return $final !== null ? number_format($final, 2) : null;
        };

        $locale = App::getLocale();
        $priceOptions = [
            [
                'key' => 'washing',
                'label' => $locale === 'ar' ? 'سعر الغسيل' : 'Washing Price',
            ],
            [
                'key' => 'ironing',
                'label' => $locale === 'ar' ? 'سعر الكي' : 'Ironing Price',
            ],
        ];

        $priceKey = $request->query('price_key', null);
        $priceKey = in_array($priceKey, ['washing', 'ironing', 'both'], true) ? $priceKey : null;
        $selectedPrice = null;
        if ($priceKey === 'washing') {
            $selectedPrice = $adjustPrice($this->washing_price);
        } elseif ($priceKey === 'ironing') {
            $selectedPrice = $adjustPrice($this->ironing_price);
        } elseif ($priceKey === 'both') {
            $wash = $adjustPrice($this->washing_price);
            $iron = $adjustPrice($this->ironing_price);
            if ($wash !== null && $iron !== null) {
                $selectedPrice = number_format(((float) $wash) + ((float) $iron), 2);
            } elseif ($wash !== null) {
                $selectedPrice = $wash;
            } elseif ($iron !== null) {
                $selectedPrice = $iron;
            }
        }

        $includeAreaPrices = $request->query('include_area_prices', false);
        $includeAreaPrices = $includeAreaPrices === '1' || $includeAreaPrices === 1 || $includeAreaPrices === true || $includeAreaPrices === 'true';

        $areaPrices = null;
        if ($includeAreaPrices) {
            $areas = Area::orderBy('name->en')->get();
            $areaPrices = $areas->map(function ($area) use ($getLocalizedValue, $priceKey) {
                $washing = $this->washing_price !== null ? $area->adjustedPrice((float)$this->washing_price) : null;
                $ironing = $this->ironing_price !== null ? $area->adjustedPrice((float)$this->ironing_price) : null;
                $selected = null;
                if ($priceKey === 'washing') {
                    $selected = $washing;
                } elseif ($priceKey === 'ironing') {
                    $selected = $ironing;
                } elseif ($priceKey === 'both') {
                    if ($washing !== null && $ironing !== null) {
                        $selected = round(((float) $washing) + ((float) $ironing), 2);
                    } elseif ($washing !== null) {
                        $selected = $washing;
                    } elseif ($ironing !== null) {
                        $selected = $ironing;
                    }
                }
                return [
                    'area_id' => $area->id,
                    'area_name' => $getLocalizedValue($area->name),
                    'increase_percentage' => $area->price_increase_percentage ?? 0,
                    'washing_price' => $washing,
                    'ironing_price' => $ironing,
                    'price_key' => $priceKey,
                    'price' => $selected,
                ];
            })->values();
        }

        return [
            'id' => $this->id,
            'name' => $getLocalizedValue($this->name),
            'service_category_id' => $this->service_category_id,
            'service_category_name' => $this->serviceCategory ? $getLocalizedValue($this->serviceCategory->name) : null,
            'logo' => $this->logo ? Storage::url($this->logo) : null,
            'price' => $selectedPrice ?? $adjustPrice($this->price ?? $this->washing_price),
            'washing_price' => $adjustPrice($this->washing_price),
            'ironing_price' => $adjustPrice($this->ironing_price),
            'prices' => [
                'washing' => $adjustPrice($this->washing_price),
                'ironing' => $adjustPrice($this->ironing_price),
            ],
            'selected_price_key' => $priceKey,
            'selected_price' => $selectedPrice,
            'area_prices' => $areaPrices,
            'price_options' => $priceOptions,
            'prices_sum' => $selectedPrice,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
