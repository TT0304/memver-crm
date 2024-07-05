<?php

namespace App\Repositories\Lead;

use App\Core\Eloquent\Repository;

class TypeRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\DataStructure\Lead\Contracts\Type';
    }
}
