<?php

namespace App\Traits\Api\V1;

use Illuminate\Support\Facades\Log;

trait ApiResponse
{
    public function res(string $message, $data, int $code, $realCode = null)
    {
        // log data:
        return response()->json(
            array_filter([
                'status' => $realCode ?? $code,
                'message' => $message ?: null,
                'data' => $data
            ], fn($item) => $item !== null),
            $code);
    }

    public function ok(string $message, $data = null, int $realCode = 200)
    {
        return $this->res($message, $data, 200, $realCode);
    }

    public function error(string $message, $data = null, int $code = 400, $realCode = null)
    {
        return $this->res($message, $data, $code, $realCode);
    }
}
