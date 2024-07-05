<?php

namespace App\DataGrids\Product;

use App\DataGrids\DataGrid;
use Illuminate\Support\Facades\DB;
use Gate;

class ProductDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('crm_products')
            ->addSelect(
                'crm_products.id',
                'crm_products.sku',
                'crm_products.name',
                'crm_products.price',
                'crm_products.quantity'
            );

        $this->addFilter('id', 'crm_products.id');

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
            'index'    => 'sku',
            'label'    => ('datagrid.sku'),
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
            'index'    => 'price',
            'label'    => ('datagrid.price'),
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                return round($row->price, 2);
            },
        ]);

        $this->addColumn([
            'index'    => 'quantity',
            'label'    => ('datagrid.quantity'),
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
        if (Gate::allows('product_edit')) {
            $this->addAction([
                'title'  => trans('app.ui.datagrid.edit'),
                'method' => 'GET',
                'route'  => 'products.edit',
                'icon'   => 'ri-edit-2-line',
            ]);
        }
        if (Gate::allows('product_delete')) {
            $this->addAction([
                'title'        => trans('app.ui.datagrid.delete'),
                'method'       => 'DELETE',
                'route'        => 'products.delete',
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
        if (Gate::allows('product_delete')) {
            $this->addMassAction([
                'type'   => 'delete',
                'label'  => ('ui.datagrid.delete'),
                'action' => route('products.mass_delete'),
                'method' => 'PUT',
            ]);
        }
    }
}
