<?php

namespace App\Http\Middleware;

use Closure;
    use Illuminate\Http\Request;

    class CheckRole
    {
        /**
         * Handle an incoming request.
         *
         * @param Request $request
         * @param Closure $next
         * @param array   $roles
         *
         * @return mixed
         */
        public function handle(Request $request, Closure $next, ...$roles)
        {
            foreach ($roles as $role) {
                // Check if user has the role This check will depend on how your roles are set up
                if ($request->user()->hasRole($role)) {
                    return $next($request);
                }
            }
            if ($request->user()) {
                return abort(403, 'No tienes autorización para Ralizar esta tarea.');
            }

            return abort(401, 'No estas logueado logueate e intentalo.');
        }
    }
