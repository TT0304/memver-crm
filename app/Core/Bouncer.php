<?php

namespace App\Core;

class Bouncer
{
    /**
     * Checks if user allowed or not for certain action
     *
     * @param  string  $permission
     * @return void
     */
    public function hasPermission($permission)
    {
        if (auth()->check() && auth()->user()->role->permission_type == 'all') {
            return true;
        } else {
            if (! auth()->check() || ! auth()->user()->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if user allowed or not for certain action
     *
     * @param  string  $permission
     * @return void
     */
    public static function allow($permission)
    {
        if (! auth()->check() || ! auth()->user()->hasPermission($permission)) {
            abort(401, 'This action is unauthorized');
        }
    }
}