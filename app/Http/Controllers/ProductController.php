<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Event;
use Inertia\Inertia;
use App\Http\Requests\Attribute\AttributeForm;
use App\Repositories\Product\ProductRepository;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * ProductRepository object
     *
     * @var \App\Repositories\Product\ProductRepository
     */
    protected $productRepository;

    /**
     * Create a new controller instance.
     *
     * @param \App\Repositories\Product\ProductRepository  $productRepository
     *
     * @return void
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;

        request()->request->add(['entity_type' => 'products']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        abort_if(Gate::denies('product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if (request()->ajax()) {
            return app(\App\DataGrids\Product\ProductDataGrid::class)->toJson();
        }
        return Inertia::render('dashboards/products/index', [
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        abort_if(Gate::denies('product_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $customAttributes = app('App\Repositories\Attribute\AttributeRepository')->findWhere([
            'entity_type' => 'products',
        ]);
        
        $currencyCode = core()->currencySymbol(config('app.currency'));
        return Inertia::render('dashboards/products/create', [
            'customAttributes' => $customAttributes,
            'currencyCode' => $currencyCode,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Request\Attribute\AttributeForm $request
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeForm $request)
    {
        Event::dispatch('product.create.before');

        $product = $this->productRepository->create(request()->all());

        Event::dispatch('product.create.after', $product);

        session()->flash('success', trans('app.products.create-success'));

        return Inertia::location(route('products.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        abort_if(Gate::denies('product_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $product = $this->productRepository->findOrFail($id);

        $customAttributes = app('App\Repositories\Attribute\AttributeRepository')->findWhere([
            'entity_type' => 'products',
        ]);
        
        $currencyCode = core()->currencySymbol(config('app.currency'));
        return Inertia::render('dashboards/products/create', [
            'customAttributes' => $customAttributes,
            'currencyCode'     => $currencyCode,
            'product'          => [
                'id'              => $product->id,
                'name'            => $product->name,
                'description'     => $product->description,
                'sku'             => $product->sku,
                'quantity'        => $product->quantity,
                'price'           => $product->price,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Request\Attribute\AttributeForm $request
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AttributeForm $request, $id)
    {
        Event::dispatch('product.update.before', $id);

        $product = $this->productRepository->update(request()->all(), $id);

        Event::dispatch('product.update.after', $product);

        session()->flash('success', trans('app.products.update-success'));

        return Inertia::location(route('products.index'));
    }

    /**
     * Search product results
     *
     * @return \Illuminate\Http\Response
     */
    public function search()
    {
        $results = $this->productRepository->findWhere([
            ['name', 'like', '%' . urldecode(request()->input('query')) . '%']
        ]);

        return response()->json($results);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('product_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->productRepository->findOrFail($id);

        try {
            Event::dispatch('settings.products.delete.before', $id);

            $this->productRepository->delete($id);

            Event::dispatch('settings.products.delete.after', $id);

            return response()->json([
                'message' => trans('app.response.destroy-success', ['name' => trans('app.products.product')]),
            ], 200);
        } catch(\Exception $exception) {
            return response()->json([
                'message' => trans('app.response.destroy-failed', ['name' => trans('app.products.product')]),
            ], 400);
        }
    }

    /**
     * Mass Delete the specified resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy()
    {
        abort_if(Gate::denies('product_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        foreach (request('rows') as $productId) {
            Event::dispatch('product.delete.before', $productId);

            $this->productRepository->delete($productId);

            Event::dispatch('product.delete.after', $productId);
        }

        return response()->json([
            'message' => trans('app.response.destroy-success', ['name' => trans('app.products.title')]),
        ]);
    }
}
