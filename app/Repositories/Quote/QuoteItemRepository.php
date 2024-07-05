<?php

namespace App\Repositories\Quote;

use App\Core\Eloquent\Repository;
use App\Repositories\Product\ProductRepository;
use Illuminate\Container\Container;

class QuoteItemRepository extends Repository
{
    /**
     * ProductRepository object
     *
     * @var App\Repositories\Product\ProductRepository
     */
    protected $productRepository;

    /**
     * Create a new repository instance.
     *
     * @param  App\Repositories\Product\ProductRepository  $productRepository
     * @param  \Illuminate\Container\Container  $container
     * @return void
     */
    public function __construct(
        ProductRepository $productRepository,
        Container $container
    )
    {
        $this->productRepository = $productRepository;

        parent::__construct($container);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\DataStructure\Quote\Contracts\QuoteItem';
    }

    /**
     * @param array $data
     * @return \App\DataStructure\Quote\Contracts\QuoteItem
     */
    public function create(array $data)
    {
        $product = $this->productRepository->findOrFail($data['product_id']);

        $quoteItem = parent::create(array_merge($data, [
            'sku'  => $product->sku,
            'name' => $product->name,
        ]));

        return $quoteItem;
    }

    /**
     * @param array  $data
     * @param int    $id
     * @param string $attribute
     * @return \App\DataStructure\Quote\Contracts\QuoteItem
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $product = $this->productRepository->findOrFail($data['product_id']);

        $quoteItem = parent::update(array_merge($data, [
            'sku'  => $product->sku,
            'name' => $product->name,
        ]), $id);

        return $quoteItem;
    }
}
