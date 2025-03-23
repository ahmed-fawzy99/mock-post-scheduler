<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Api\V1\ApiResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    use ApiResponse;
    protected $policyClass;

    public function isAble($ability, $targetModel)
    {
        return Gate::authorize($ability, [$targetModel, $this->policyClass]);
    }
}
