<?php

namespace App\DataGrids\Setting;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Traits\Admin\ProvideDropdownOptions;
use App\DataGrids\DataGrid;
use Gate;

class PermissionDataGrid extends DataGrid
{
    use ProvideDropdownOptions;

    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('permissions')
            ->select(
                'permissions.id',
                'permissions.name',
                'permissions.guard_name',
                'permissions.created_at'
            );

        $this->addFilter('id', 'permissions.id');
        $this->addFilter('name', 'permissions.name');

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
            'label'    => ('datagrid.title'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'guard_name',
            'label'    => ('datagrid.type'),
            'type'     => 'string',
            'sortable' => true,
        ]);
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
        //     'route'  => 'permissions.view',
        //     'icon'   => 'ri-eye-line',
        // ]);
        if (Gate::allows('permission_edit')) {
            $this->addAction([
                'title'  => trans('app.ui.datagrid.edit'),
                'method' => 'GET',
                'route'  => 'permissions.edit',
                'icon'   => 'ri-pencil-line',
            ]);
        }
        if (Gate::allows('permission_delete')) {
            $this->addAction([
                'title'        => trans('app.ui.datagrid.delete'),
                'method'       => 'DELETE',
                'route'        => 'permissions.delete',
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
        if (Gate::allows('permission_delete')) {
            $this->addMassAction([
                'type'   => 'delete',
                'label'  => ('ui.datagrid.delete'),
                'action' => route('permissions.mass_delete'),
                'method' => 'PUT',
            ]);
        }
        if (Gate::allows('permission_edit')) {
            $this->addMassAction([
                'type'    => 'update',
                'label'   => ('ui.datagrid.update-status'),
                'action'  => route('permissions.mass_update'),
                'method'  => 'PUT',
                'options' => [
                    trans('app.datagrid.active')   => 1,
                    trans('app.datagrid.inactive') => 0,
                ],
            ]);
        }
    }
}
