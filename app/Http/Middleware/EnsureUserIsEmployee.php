<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsEmployee
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isEmployee() || ! $user->employee_id) {
            abort(403, 'Akses hanya untuk Karyawan.');
        }

        return $next($request);
    }
}
