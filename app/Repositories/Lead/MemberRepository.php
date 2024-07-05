<?php

namespace App\Repositories\Lead;

use App\Core\Eloquent\Repository;
use App\Repositories\Attribute\AttributeValueRepository;
use App\Repositories\Contact\PersonRepository;
use Carbon\Carbon;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\DataStructure\Lead\Models\Member;

class MemberRepository extends Repository
{
    /**
     * PersonRepository object
     *
     * @var \App\Repositories\Contact\PersonRepository
     */
    protected $personRepository;

    /**
     * Create a new repository instance.
     *
     * @param  \App\Repositories\Contact\PersonRepository  $personRepository
     * @param  \Illuminate\Container\Container  $container
     * @return void
     */
    public function __construct(
        PersonRepository $personRepository,
        Container $container
    ) {
        $this->personRepository = $personRepository;

        parent::__construct($container);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\DataStructure\Lead\Contracts\Member';
    }

    /**
     * @param array $data
     * @return \App\DataStructure\Lead\Contracts\Member
     */
    public function create($id)
    {
        $person = $this->personRepository->findOrFail($id);

        $member = parent::create([
            'email'           => $person->emails,
            'mobile'          => $person->contact_numbers,
            'organization_id' => $person->organization_id,
        ]);
        
        return $member;
    }

    
    public function findByAttributes(array $attributes) {
        return Member::where($attributes)->get();
    }

}
