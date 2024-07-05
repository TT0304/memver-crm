<?php

namespace App\Repositories\Contact;

use App\Core\Eloquent\Repository;
use App\Repositories\Attribute\AttributeValueRepository;
use Illuminate\Container\Container;

class OrganizationRepository extends Repository
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
        return 'App\DataStructure\Contact\Contracts\Organization';
    }

    /**
     * @param array $data
     * @return \App\DataStructure\Contact\Contracts\Organization
     */
    public function create(array $data)
    {
        $organization = parent::create($data);

        $this->attributeValueRepository->save($data, $organization->id);

        return $organization;
    }

    /**
     * @param array  $data
     * @param int    $id
     * @param string $attribute
     * @return \App\DataStructure\Contact\Contracts\Organization
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $organization = parent::update($data, $id);

        $this->attributeValueRepository->save($data, $id);

        return $organization;
    }
}
