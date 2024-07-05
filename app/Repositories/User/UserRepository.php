<?php

namespace App\Repositories\User;

use App\Core\Eloquent\Repository;

class UserRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\DataStructure\User\Contracts\User';
    }

    /**
     * This function will return user ids of current user's groups
     *
     * @return array
     */
    public function getCurrentUserGroupsUserIds()
    {
        $userIds = $this->scopeQuery(function ($query) {
            return $query->select('users.*')
                ->leftJoin('user_groups', 'users.id', '=', 'user_groups.user_id')
                ->leftJoin('groups', 'user_groups.group_id', 'groups.id')
                ->whereIn('groups.id', auth()->user()->groups()->pluck('id'));
        })->get()->pluck('id')->toArray();

        return $userIds;
    }
}
