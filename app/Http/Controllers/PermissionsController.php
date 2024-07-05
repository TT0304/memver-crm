<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyPermissionRequest;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\DataStructure\User\Models\Permission;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Inertia;
use App\Repositories\User\PermissionRepository;

class PermissionsController extends Controller
{
    protected $permissionRepository;

    public function __construct(
        PermissionRepository $permissionRepository,
    ) {
        $this->permissionRepository = $permissionRepository;

        request()->request->add(['entity_type' => 'permissions']);
    }

    public function index()
    {
        abort_if(Gate::denies('permission_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        if (request()->ajax()) {
            return app(\App\DataGrids\Setting\PermissionDataGrid::class)->toJson();
        }
        return Inertia::render('settings/permissions/index', []);
    }

    public function create()
    {
        abort_if(Gate::denies('permission_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $customAttributes = app('App\Repositories\Attribute\AttributeRepository')->findWhere([
            'entity_type' => 'permissions',
        ]);
        $currencyCode = core()->currencySymbol(config('app.currency'));
        return Inertia::render('settings/permissions/create', [
            'customAttributes' => $customAttributes,
            'currencyCode'     => $currencyCode,
        ]);

    }

    public function store()
    {
        Permission::create([...request()->all(), 'guard_name' => 'web']);

        return Inertia::location(route('permissions.index', []));
    }

    public function edit($id)
    {
        abort_if(Gate::denies('permission_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $permission = $this->permissionRepository->findOrFail($id);
        $customAttributes = app('App\Repositories\Attribute\AttributeRepository')->findWhere([
            'entity_type' => 'permissions',
        ]);
        $currencyCode = core()->currencySymbol(config('app.currency'));
        return Inertia::render('settings/permissions/create', [
            'customAttributes' => $customAttributes,
            'currencyCode'     => $currencyCode,
            'permission'    => $permission
        ]);

    }

    public function show($permission)
    {
        abort_if(Gate::denies('permission_view'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        return Inertia::render('settings/permissions/view', ['permission' => $permission]);
    }

    public function update($id)
    {
        $data = request()->all();
        $permission = $this->permissionRepository->update($data, request('id') ?? $id);

        return Inertia::location(route('permissions.index', []));
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('permission_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        Permission::whereIn('id',[$id])->delete();
        session()->flash('success', trans('app.settings.permissions.delete-success'));

        return Inertia::render('settings/permissions/index', []);
    }

    public function massDestroy()
    {
        $permissions = Permission::find(request('ids'));

        foreach ($permissions as $permission) {
            $permission->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
