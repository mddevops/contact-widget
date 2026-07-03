<?php

namespace SiteApps\ContactWidget\Http\Controllers\Embed;

use SiteApps\ContactWidget\Http\Controllers\Controller;
use SiteApps\ContactWidget\Services\EmbedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmbedConfigController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(
            EmbedService::config($request->query('path', $request->path()))
        );
    }
}
