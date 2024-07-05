<?php

namespace App\DataGrids\Setting;

use Illuminate\Support\Facades\DB;
use App\DataGrids\DataGrid;
use Gate;

class AttributeDataGrid extends DataGrid
{
    /**
     * Create datagrid instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->tabFilters = [
            [
                'type'      => 'pill',
                'key'       => 'entity_type',
                'condition' => 'eq',
                'values'    => [
                    [
                        'name'      => trans('app.leads.all'),
                        'isActive'  => true,
                        'key'       => 'all',
                    ], [
                        'name'      => trans('app.leads.title'),
                        'isActive'  => false,
                        'key'       => 'leads',
                    ], [
                        'name'      => trans('app.contacts.persons.title'),
                        'isActive'  => false,
                        'key'       => 'persons',
                    ], [
                        'name'      => trans('app.contacts.organizations.title'),
                        'isActive'  => false,
                        'key'       => 'organizations',
                    ], [
                        'name'      => trans('app.products.title'),
                        'isActive'  => false,
                        'key'       => 'products',
                    ], [
                        'name'      => trans('app.quotes.title'),
                        'isActive'  => false,
                        'key'       => 'quotes',
                    ]
                ]
            ]
        ];
    }

    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('attributes')
            ->addSelect(
                'attributes.id',
                'attributes.code',
                'attributes.name',
                'attributes.type',
                'attributes.entity_type'
            );

        $this->addFilter('id', 'attributes.id');
        $this->addFilter('type', 'attributes.type');

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
            'index'    => 'code',
            'label'    => ('datagrid.code'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'name',
            'label'    => ('datagrid.name'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'      => 'entity_type',
            'label'      => ('datagrid.entity_type'),
            'type'       => 'string',
            'searchable' => false,
            'closure'    => function ($row) {
                return ucfirst($row->entity_type);
            },
        ]);

        $this->addColumn([
            'index'    => 'type',
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
        $this->addAction([
            'title'  => trans('app.ui.datagrid.edit'),
            'method' => 'GET',
            'route'  => 'settings.attributes.edit',
            'icon'   => 'pencil-icon',
        ]);

        $this->addAction([
            'title'        => trans('app.ui.datagrid.delete'),
            'method'       => 'DELETE',
            'route'        => 'settings.attributes.delete',
            'confirm_text' => trans('app.ui.datagrid.massaction.delete', ['resource' => 'attributes']),
            'icon'         => 'trash-icon',
        ]);
    }

    /**
     * Prepare mass actions.
     *
     * @return void
     */
    public function prepareMassActions()
    {
        $this->addMassAction([
            'type'   => 'delete',
            'label'  => ('ui.datagrid.delete'),
            'action' => route('settings.attributes.mass_delete'),
            'method' => 'PUT',
        ]);
    }
}
