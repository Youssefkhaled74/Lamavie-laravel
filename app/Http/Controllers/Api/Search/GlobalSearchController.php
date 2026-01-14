<?php

namespace App\Http\Controllers\Api\Search;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceType;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class GlobalSearchController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:1',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(422, trans('messages.validation_failed'), $validator->errors());
        }

        $q = $request->input('query');
        $limit = (int) ($request->input('limit') ?? 15);
        $locale = $request->header('Accept-Language', app()->getLocale());
        $lowerQuery = strtolower($q); // Normalize search query to lowercase for English

        // Helper to get localized name
        $getLocalizedName = function ($name) use ($locale) {
            if (is_array($name)) {
                return $name[$locale] ?? $name['en'] ?? $name['ar'] ?? reset($name);
            }
            return $name;
        };

        // Helper to format logo URL
        $formatLogoUrl = function ($logo) {
            return $logo ? env('APP_URL') . Storage::url($logo) : null;
        };

        // Only search within this allowed set of service ids
        $allowedServiceIds = [1,2,3,4,5,6];

        $services = Service::query()
            ->whereIn('id', $allowedServiceIds)
            ->where(function($query) use ($locale, $lowerQuery, $q) {
                $query->whereRaw('LOWER(JSON_EXTRACT(name, ?)) LIKE ?', ['$.' . $locale, "%{$lowerQuery}%"])
                      ->orWhereRaw('LOWER(JSON_EXTRACT(name, "$.en")) LIKE ?', ["%{$lowerQuery}%"])
                      ->orWhereRaw('JSON_EXTRACT(name, "$.ar") LIKE ?', ["%{$q}%"]);
            })
            ->limit($limit)
            ->get()
            ->map(function (Service $s) use ($getLocalizedName, $formatLogoUrl) {
                // include related categories and types for this service
                $categories = $s->categories()->get()->map(function ($c) use ($getLocalizedName, $formatLogoUrl) {
                    return [
                        'id' => $c->id,
                        'name' => $getLocalizedName($c->name),
                        'logo' => $formatLogoUrl($c->logo),
                        'service_id' => $c->service_id,
                    ];
                });

                $types = $s->types()->get()->map(function ($t) use ($getLocalizedName, $formatLogoUrl) {
                    return [
                        'id' => $t->id,
                        'name' => $getLocalizedName($t->name),
                        'logo' => $formatLogoUrl($t->logo),
                        'service_id' => $t->service_id,
                    ];
                });

                return [
                    'id' => $s->id,
                    'type' => 'service',
                    'name' => $getLocalizedName($s->name),
                    'logo' => $formatLogoUrl($s->logo),
                    'service_id'=> $s->id,
                    'categories' => $categories,
                    'types' => $types,
                ];
            });

        // Limit categories to those that belong to allowed services
        $categories = ServiceCategory::query()
            ->whereIn('service_id', $allowedServiceIds)
            ->where(function($query) use ($locale, $lowerQuery, $q) {
                $query->whereRaw('LOWER(JSON_EXTRACT(name, ?)) LIKE ?', ['$.' . $locale, "%{$lowerQuery}%"])
                      ->orWhereRaw('LOWER(JSON_EXTRACT(name, "$.en")) LIKE ?', ["%{$lowerQuery}%"])
                      ->orWhereRaw('JSON_EXTRACT(name, "$.ar") LIKE ?', ["%{$q}%"]);
            })
            ->limit($limit)
            ->get()
            ->map(function (ServiceCategory $c) use ($getLocalizedName, $formatLogoUrl) {
                return [
                    'id' => $c->id,
                    'type' => 'service_category',
                    'name' => $getLocalizedName($c->name),
                    'logo' => $formatLogoUrl($c->logo),
                    'service_id' => $c->service_id,
                ];
            });

        // Limit types to those that belong to allowed services
        $types = ServiceType::query()
            ->whereIn('service_id', $allowedServiceIds)
            ->where(function($query) use ($locale, $lowerQuery, $q) {
                $query->whereRaw('LOWER(JSON_EXTRACT(name, ?)) LIKE ?', ['$.' . $locale, "%{$lowerQuery}%"])
                      ->orWhereRaw('LOWER(JSON_EXTRACT(name, "$.en")) LIKE ?', ["%{$lowerQuery}%"])
                      ->orWhereRaw('JSON_EXTRACT(name, "$.ar") LIKE ?', ["%{$q}%"]);
            })
            ->limit($limit)
            ->get()
            ->map(function (ServiceType $t) use ($getLocalizedName, $formatLogoUrl) {
                return [
                    'id' => $t->id,
                    'type' => 'service_type',
                    'name' => $getLocalizedName($t->name),
                    'logo' => $formatLogoUrl($t->logo),
                    'service_id' => $t->service_id,
                ];
            });

        $results = $services
            ->concat($categories)
            ->concat($types)
            ->take($limit)
            ->values();

        return $this->successResponse(200, trans('messages.operation_successful'), [
            'query' => $q,
            'count' => $results->count(),
            'results' => $results,
        ]);
    }
}