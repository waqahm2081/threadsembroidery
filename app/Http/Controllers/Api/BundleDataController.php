<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BundleCatalogService;

class BundleDataController extends Controller
{
    public function __invoke(BundleCatalogService $catalog)
    {
        return response()->json($catalog->frontendPayload());
    }
}
