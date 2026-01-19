<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        // ถ้า User มี role ตรงกับหนึ่งใน roles ที่ส่งเข้ามา ให้ผ่านไปได้
        // ตัวอย่างการใช้: middleware('role:admin,hr')
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // ถ้าไม่มีสิทธิ์ ให้ abort 403
        abort(403, 'Unauthorized action.');
    }
}