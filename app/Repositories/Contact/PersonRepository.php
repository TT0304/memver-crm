<?php

namespace App\Repositories\Contact;

use App\Core\Eloquent\Repository;
use App\Repositories\Attribute\AttributeValueRepository;
use Illuminate\Container\Container;

class PersonRepository extends Repository
{
    /**
     * AttributeValueRepository object
     *
     * @var \App\Repositories\Attribute\AttributeValueRepository
     */
    protected $attributeValueRepository;

    /**
     * Create a new repository instance.
     *
     * @param  \App\Repositories\Attribute\AttributeValueRepository $attributeValueRepository
     * @param  \Illuminate\Container\Container  $container
     * @return void
     */
    public function __construct(
        AttributeValueRepository $attributeValueRepository,
        Container $container
    )
    {
        $this->attributeValueRepository = $attributeValueRepository;

        parent::__construct($container);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\DataStructure\Contact\Contracts\Person';
    }

    /**
     * @param array $data
     * @return \App\DataStructure\Contact\Contracts\Person
     */
    public function create(array $data)
    {
        $person = parent::create($data);
        
        $this->attributeValueRepository->save($data, $person->id);

        return $person;
    }

    /**
     * @param array  $data
     * @param int    $id
     * @param string $attribute
     * @return \App\DataStructure\Contact\Contracts\Person
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $person = parent::update($data, $id);

        $this->attributeValueRepository->save($data, $id);

        return $person;
    }

    /**
     * Retrieves customers count based on date
     *
     * @return number
     */
    public function getCustomerCount($startDate, $endDate)
    {
        return $this
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get()
                ->count();
    }
}
