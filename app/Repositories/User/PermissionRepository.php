<?php

namespace App\Repositories\User;

use App\Core\Eloquent\Repository;

class PermissionRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\DataStructure\User\Contracts\Permission';
    }
}
