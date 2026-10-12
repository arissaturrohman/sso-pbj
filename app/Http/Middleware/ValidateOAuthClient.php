<?php

namespace App\Http\Middleware;

use App\Models\Application;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateOAuthClient
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        $clientId = $request->input('client_id')
            ?? $request->query('client_id');

        if (! $clientId) {
            return response()->json([
                'success' => false,
                'message' => 'OAuth client_id wajib diberikan.',
            ], 400);
        }

        $application = Application::where(
            'passport_client_id',
            $clientId
        )->first();

        if (! $application) {
            return response()->json([
                'success' => false,
                'message' => 'OAuth Client tidak terdaftar.',
            ], 403);
        }

        if (! $application->status) {
            return response()->json([
                'success' => false,
                'message' => 'Aplikasi sedang dinonaktifkan.',
            ], 403);
        }

        return $next($request);
    }
}