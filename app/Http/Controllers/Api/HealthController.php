<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class HealthController extends Controller
{
    public function index()
    {
        try {
            DB::connection()->getPdo();

            return response()->json([
                'success' => true,
                'message' => 'Welcome to Omniflow Laravel Starter API Health Check',
                'data' => [
                    'database' => 'connected',
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'service unavailable',
                'data' => null,
            ], 503);
        }
    }
}
