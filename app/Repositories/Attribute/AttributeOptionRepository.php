<?php

namespace App\Repositories\Attribute;

use App\Core\Eloquent\Repository;

class AttributeOptionRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\DataStructure\Attribute\Contracts\AttributeOption';
    }
}
