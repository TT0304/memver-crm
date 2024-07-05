<?php

namespace App\Repositories\Tag;

use App\Core\Eloquent\Repository;

class TagRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\DataStructure\Tag\Contracts\Tag';
    }
}
