<?php

namespace App\Http\Controllers;

use App\Repositories\Attribute\AttributeRepository;
use App\Modules\Validations\Code;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

class AttributeController extends Controller
{
    /**
     * AttributeRepository object
     *
     * @var \App\Repositories\Attribute\AttributeRepository
     */
    protected $attributeRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Repositories\Attribute\AttributeRepository  $attributeRepository
     * @return void
     */
    public function __construct(AttributeRepository $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(\App\DataGrids\Setting\AttributeDataGrid::class)->toJson();
        }

        return view('settings.attributes.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('settings.attributes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'code' => ['required', 'unique:attributes,code,NULL,NULL,entity_type,' . request('entity_type'), new Code],
            'name' => 'required',
            'type' => 'required',
        ]);

        Event::dispatch('settings.attribute.create.before');

        request()->request->add(['quick_add' => 1]);

        $attribute = $this->attributeRepository->create(request()->all());

        Event::dispatch('settings.attribute.create.after', $attribute);

        session()->flash('success', trans('app.settings.attributes.create-success'));

        return redirect()->route('settings.attributes.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $attribute = $this->attributeRepository->findOrFail($id);

        return view('settings.attributes.edit', compact('attribute'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->validate(request(), [
            'code' => ['required', 'unique:attributes,code,NULL,NULL,entity_type,' . $id, new Code],
            'name' => 'required',
            'type' => 'required',
        ]);

        Event::dispatch('settings.attribute.update.before', $id);

        $attribute = $this->attributeRepository->update(request()->all(), $id);

        Event::dispatch('settings.attribute.update.after', $attribute);

        session()->flash('success', trans('app.settings.attributes.update-success'));

        return redirect()->route('settings.attributes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $attribute = $this->attributeRepository->findOrFail($id);

        if (! $attribute->is_user_defined) {
            return response()->json([
                'message' => trans('app.settings.attributes.user-define-error'),
            ], 400);
        }

        try {
            Event::dispatch('settings.attribute.delete.before', $id);

            $this->attributeRepository->delete($id);

            Event::dispatch('settings.attribute.delete.after', $id);

            return response()->json([
                'status'  => true,
                'message' => trans('app.response.destroy-success', ['name' => trans('app.settings.attributes.attribute')]),
            ], 200);
        } catch(\Exception $exception) {
            return response()->json([
                'message' => trans('app.settings.attributes.delete-failed'),
            ], 400);
        }
    }

    /**
     * Search attribute lookup results
     *
     * @param  string  $lookup
     * @return \Illuminate\Http\Response
     */
    public function lookup($lookup)
    {
        $results = $this->attributeRepository->getLookUpOptions($lookup, request()->input('query'));

        return response()->json($results);
    }

    /**
     * Search entity lookup results
     *
     * @param  string  $lookup
     * @return \Illuminate\Http\Response
     */
    public function lookup_entity($lookup)
    {
        $results = $this->attributeRepository->getLookUpEntity($lookup, request()->input('value'));

        return response()->json($results);
    }

    /**
     * Mass Delete the specified resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy()
    {
        $count = 0;

        foreach (request('rows') as $attributeId) {
            $attribute = $this->attributeRepository->find($attributeId);

            if (! $attribute->is_user_defined) {
                continue;
            }

            Event::dispatch('settings.attribute.delete.before', $attributeId);

            $this->attributeRepository->delete($attributeId);

            Event::dispatch('settings.attribute.delete.after', $attributeId);

            $count++;
        }

        if (! $count) {
            return response()->json([
                'message' => trans('app.settings.attributes.mass-delete-failed'),
            ], 400);
        }

        return response()->json([
            'message' => trans('app.response.destroy-success', ['name' => trans('app.settings.attributes.title')]),
        ]);
    }

    /**
     * Download image or file
     *
     * @return \Illuminate\Http\Response
     */
    public function download()
    {
        return Storage::download(request('path'));
    }
}
