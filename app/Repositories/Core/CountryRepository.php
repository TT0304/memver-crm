<?php

namespace App\Repositories\Core;

use App\Eloquent\Repository;
use Prettus\Repository\Traits\CacheableRepository;

class CountryRepository extends Repository
{
    use CacheableRepository;

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\DataStructure\Core\Contracts\Country';
    }
}
