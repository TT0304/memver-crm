<?php

namespace App\Repositories\EmailTemplate;

use App\Core\Eloquent\Repository;

class EmailTemplateRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\DataStructure\EmailTemplate\Contracts\EmailTemplate';
    }
}