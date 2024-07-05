<?php

namespace App\Repositories\Activity;

use App\Core\Eloquent\Repository;

class ParticipantRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\DataStructure\Activity\Contracts\Participant';
    }
}
