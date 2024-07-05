<?php

namespace App\DataGrids\Setting;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\DataGrids\DataGrid;
use App\Traits\Attribute\CustomAttribute;
use Gate;

class UserDataGrid extends DataGrid
{
    use CustomAttribute;

    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('client_users')
            ->addSelect(
                'client_users.id',
                'client_users.name',
                'client_users.email',
                'client_users.email_verified_at',
                'client_users.image',
                'client_users.created_at'
            );

        $this->addFilter('id', 'client_users.id');

        $this->setQueryBuilder($queryBuilder);
    }

    /**
     * Add columns.
     *
     * @return void
     */
    public function addColumns()
    {
        $this->addColumn([
            'index'    => 'id',
            'label'    => ('datagrid.id'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'name',
            'label'    => ('datagrid.name'),
            'type'     => 'string',
            'sortable' => true,
            // 'closure'  => function ($row) {
            //     if ($row->image) {
            //         return '<div class="avatar"><img src="' . Storage::url($row->image) . '"></div>' . $row->name;
            //     } else {
            //         return '<div class="avatar"><span class="icon avatar-icon"></span></div>' . $row->name;
            //     }
            // },
        ]);

        $this->addColumn([
            'index'    => 'email',
            'label'    => ('datagrid.email'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        
        // $this->addColumn([
        //     'index'            => 'roles',
        //     'label'            => ('datagrid.roles'),
        //     'type'             => 'dropdown',
        //     'dropdown_options' => $this->getRoleDropdownOptions(),
        //     'sortable'         => false,
        // ]);

        // $this->addColumn([
        //     'index'            => 'status',
        //     'label'            => ('datagrid.status'),
        //     'type'             => 'dropdown',
        //     'dropdown_options' => $this->getBooleanDropdownOptions(),
        //     'searchable'       => false,
        //     'closure'          => function ($row) {
        //         if ($row->status == 1) {
        //             return '<span class="badge badge-round badge-primary"></span>' . trans('app.datagrid.active');
        //         } else {
        //             return '<span class="badge badge-round badge-danger"></span>' . trans('app.datagrid.inactive');
        //         }
        //     },
        // ]);

        // $this->addColumn([
        //     'index'      => 'created_at',
        //     'label'      => ('datagrid.created_at'),
        //     'type'       => 'date_range',
        //     'searchable' => false,
        //     'sortable'   => true,
        //     'closure'    => function ($row) {
        //         return core()->formatDate($row->created_at);
        //     },
        // ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        // $this->addAction([
        //     'title'  => trans('app.ui.datagrid.view'),
        //     'method' => 'GET',
        //     'route'  => 'users.view',
        //     'icon'   => 'ri-eye-line',
        // ]);
        if (Gate::allows('user_edit')) {
            $this->addAction([
                'title'  => trans('app.ui.datagrid.edit'),
                'method' => 'GET',
                'route'  => 'users.edit',
                'icon'   => 'ri-pencil-line',
            ]);
        }
        if (Gate::allows('user_delete')) {
            $this->addAction([
                'title'        => trans('app.ui.datagrid.delete'),
                'method'       => 'DELETE',
                'route'        => 'users.delete',
                'confirm_text' => trans('app.ui.datagrid.massaction.delete', ['resource' => 'user']),
                'icon'         => 'ri-delete-bin-5-line',
            ]);
        }
    }

    /**
     * Prepare mass actions.
     *
     * @return void
     */
    public function prepareMassActions()
    {
        if (Gate::allows('user_delete')) {
            $this->addMassAction([
                'type'   => 'delete',
                'label'  => ('ui.datagrid.delete'),
                'action' => route('users.mass_delete'),
                'method' => 'PUT',
            ]);
        }
        if (Gate::allows('user_edit')) {
            $this->addMassAction([
                'type'    => 'update',
                'label'   => ('ui.datagrid.update-status'),
                'action'  => route('users.mass_update'),
                'method'  => 'PUT',
                'options' => [
                    trans('app.datagrid.active')   => 1,
                    trans('app.datagrid.inactive') => 0,
                ],
            ]);
        }
    }
}
