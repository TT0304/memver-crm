<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\DataStructure\User\Models\Role;
use App\DataStructure\User\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Inertia;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Mail;
use App\Notifications\User\Create;
use App\Repositories\EmailTemplate\EmailTemplateRepository;

class UsersController extends Controller
{
    protected $userRepository;
    protected $emailTemplateRepository;

    public function __construct(
        UserRepository $userRepository,
        EmailTemplateRepository $emailTemplateRepository

    ) {
        $this->userRepository = $userRepository;
        $this->emailTemplateRepository = $emailTemplateRepository;

        request()->request->add(['entity_type' => 'users']);
    }

    public function index()
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if (request()->ajax()) {
            return app(\App\DataGrids\Setting\UserDataGrid::class)->toJson();
        }
        return Inertia::render('settings/users/index', []);
    }

    public function create()
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $customAttributes = app('App\Repositories\Attribute\AttributeRepository')->findWhere([
            'entity_type' => 'users',
        ]);
        $currencyCode = core()->currencySymbol(config('app.currency'));
        $roles = Role::all();

        return Inertia::render('settings/users/create', [
            'customAttributes' => $customAttributes,
            'currencyCode'     => $currencyCode,
            'roles'            => $roles
        ]);
    }

    public function store()
    {
        $user = User::create(request()->all());
        $user->roles()->sync(request()->input('roles', []));
        session()->flash('success', trans('app.settings.users.create-success'));

        try {
            Mail::queue(new Create($user));
        } catch (\Exception $e) {}

        return Inertia::location(route('users.index', []));
    }

    public function edit($id)
    {
        abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        $customAttributes = app('App\Repositories\Attribute\AttributeRepository')->findWhere([
            'entity_type' => 'users',
        ]);
        $currencyCode = core()->currencySymbol(config('app.currency'));
        $roles = Role::all();
        $user = user::with(['roles'])->find($id);

        return Inertia::render('settings/users/create', [
            'customAttributes' => $customAttributes,
            'currencyCode'     => $currencyCode,
            'roles'            => $roles,
            'user'            => $user
        ]);

    }

    public function update($id)
    {
        $user = User::findOrFail($id);
        $user->update(request()->all());
        $user->roles()->sync(request()->input('roles', []));
        session()->flash('success', trans('app.settings.users.update-success'));

        return Inertia::location(route('users.index', []));
    }

    public function show(User $user)
    {
        abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        $user = User::with(['roles'])->find(request('id'));
        $roles = Role::all();
        return Inertia::render('settings/users/show', ['roles' => $roles, 'user'=>$user]);
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $res = User::destroy($id);
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

    public function massDestroy(MassDestroyUserRequest $request)
    {
        $users = User::find(request('ids'));

        foreach ($users as $user) {
            $user->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
