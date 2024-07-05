<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class Bouncer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, \Closure $next, $guard = 'web')
    {
        if (! auth()->guard($guard)->check()) {
            return redirect()->route('login');
        }

        /**
         * If user status is changed by admin. Then session should be
         * logged out.
         */
        // if (! (bool) auth()->guard($guard)->user()->status) {
        //     auth()->guard($guard)->logout();

        //     return redirect()->route('login');
        // }

        /**
         * If somehow the user deleted all permissions, then it should be
         * auto logged out and need to contact the administrator again.
         */
        if ($this->isPermissionsEmpty()) {
            auth()->guard($guard)->logout();

            return redirect()->route('login');
        }

        return $next($request);
    }

    /**
     * Check for user, if they have empty permissions or not except admin.
     *
     * @return bool
     */
    public function isPermissionsEmpty()
    {
        if (! $roles = auth()->user()->roles) {
            abort(401, 'This action is unauthorized.');
        }

        if ($roles->name === 'admin') {
            return false;
        }

        if ($roles->name !== 'admin' && empty($roles->permissions)) {
            return true;
        }

        $this->checkIfAuthorized();

        return false;
    }

    /**
     * Check authorization.
     *
     * @return null
     */
    public function checkIfAuthorized()
    {
        $acl = app('acl');

        if ($acl && isset($acl->roles[Route::currentRouteName()])) {
            bouncer()->allow($acl->roles[Route::currentRouteName()]);
        }
    }
}
