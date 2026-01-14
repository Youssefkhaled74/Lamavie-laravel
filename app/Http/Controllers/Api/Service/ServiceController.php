<?php

namespace App\Http\Controllers\Api\Service;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponse;

class ServiceController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $services = Service::with(['serviceTypes', 'photoServices'])->paginate(6);

        return $this->successResponse(
            200,
            trans('messages.services_listed'),
            ServiceResource::collection($services)
        );
    }

    /**
     * Display the specified service.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $service = Service::with(['serviceTypes', 'photoServices'])->find($id);

        if (!$service) {
            return $this->errorResponse(
                404,
                trans('messages.service_not_found')
            );
        }

        return $this->successResponse(
            200,
            trans('messages.service_found'),
            new ServiceResource($service)
        );
    }
}
