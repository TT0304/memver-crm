<?php

namespace App\DataGrids\EmailTemplate;

use Illuminate\Support\Facades\DB;
use App\DataGrids\DataGrid;
use Gate;

class EmailTemplateDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('email_templates')
            ->addSelect(
                'email_templates.id',
                'email_templates.name',
                'email_templates.subject',
            );

        $this->addFilter('id', 'email_templates.id');

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
        ]);

        $this->addColumn([
            'index'    => 'subject',
            'label'    => ('datagrid.subject'),
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
        if (Gate::allows('email_template_edit')) {

            $this->addAction([
                'title'  => trans('app.ui.datagrid.edit'),
                'method' => 'GET',
                'route'  => 'email_templates.edit',
                'icon'   => 'ri-pencil-line',
            ]);
        }

        if (Gate::allows('email_template_edit')) {

            $this->addAction([
                'title'        => trans('app.ui.datagrid.delete'),
                'method'       => 'DELETE',
                'route'        => 'email_templates.delete',
                'confirm_text' => trans('app.ui.datagrid.massaction.delete', ['resource' => 'type']),
                'icon'         => 'ri-delete-bin-5-line',
            ]);
        }
    }
}
