<?php

namespace App\Repositories\Activity;

use App\Core\Eloquent\Repository;
use Illuminate\Container\Container;

class FileRepository extends Repository
{
    /**
     * ActivityRepository object
     *
     * @var \App\Repositories\Activity\ActivityRepository
     */
    protected $activityRepository;

    /**
     * Create a new repository instance.
     *
     * @param  \App\Repositories\Activity\ActivityRepository  $activityRepository
     * @return void
     */
    public function __construct(
        ActivityRepository $activityRepository,
        Container $container
    )
    {
        $this->activityRepository = $activityRepository;

        parent::__construct($container);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\DataStructure\Activity\Contracts\File';
    }

    /**
     * @param array  $data
     * @return mixed|void
     */
    public function upload(array $data)
    {
        if (! request()->hasFile('file')) {
            return;
        }

        $leadActivity = $this->activityRepository->create([
            'is_done' => 1,
            'type'    => 'file',
            'comment' => $data['comment'],
            'user_id' => auth()->user()->id,
        ]);

        return parent::create([
            'name'        => $data['name'] ?? request()->file('file')->getClientOriginalName(),
            'path'        => request()->file('file')->store('activities/' . $leadActivity->id),
            'activity_id' => $leadActivity->id,
        ]);
    }
}
