<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyRoleRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\DataStructure\User\Models\Permission;
use App\DataStructure\User\Models\Role;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Inertia;
use App\Repositories\User\RoleRepository;

class RolesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('role_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::with(['permissions'])->get();

        return Inertia::render('settings/roles/index', ['roles' => $roles]);
    }

    public function create()
    {
        abort_if(Gate::denies('role_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $permissions = Permission::all();
        $sortedPermissions = $permissions->toArray();
        return Inertia::render('settings/roles/create', ['permissions' => $sortedPermissions]);
    }

    public function store()
    {
        $role = Role::create([...request()->all(),'guard_name'=>'web']);
        $role->permissions()->sync(request()->input('permissions', []));
        session()->flash('success', trans('app.settings.roles.create-success'));

        return Inertia::location(route('roles.index', []));
    }

    public function edit($id)
    {
        abort_if(Gate::denies('role_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $role = Role::with(['permissions'])->find($id);
        $permissions = Permission::all();
        $sortedPermissions = $permissions->toArray();
        return Inertia::render('settings/roles/create', ['permissions' => $sortedPermissions, 'role'=>$role]);
    }

    public function update($id)
    {
        $role = Role::findOrFail($id);
        $role->update(request()->all());
        $role->permissions()->sync(request()->input('permissions', []));
        session()->flash('success', trans('app.settings.roles.update-success'));

        return Inertia::location(route('roles.index', []));
    }

    public function show()
    {
        abort_if(Gate::denies('role_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $role = Role::with(['permissions'])->find(request('id'));
        return Inertia::render('settings/roles/show', ['role'=>$role]);
    }

    public function destroy(Request $request, $id)
    {
        abort_if(Gate::denies('role_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $res = Role::destroy($id);
        if ($res) {
            return response()->json([
                'status' => 1,
                'message' => 'success'
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'fail'
            ]);
        }

        return back();
    }

    public function massDestroy(MassDestroyRoleRequest $request)
    {
        $roles = Role::find(request('ids'));

        foreach ($roles as $role) {
            $role->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
