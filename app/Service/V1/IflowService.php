<?php

declare(strict_types=1);

namespace App\Service\V1;

use Illuminate\Http\Request;

class IflowService
{
    private $iflowApiService;

    public function __construct(
        IflowApiService $iflowApiService
    ) {
        $this->iflowApiService = $iflowApiService;
    }

    public function getToken(Request $request)
    {
        return $this->iflowApiService->getToken($request);
    }

    public function getStatusOrder(string $trackId, string $token)
    {
        return $this->iflowApiService->getStatusOrder($trackId, $token);
    }

    public function getSellerOrders(string $token)
    {
        return $this->iflowApiService->getSellerOrders($token);
    }
}
