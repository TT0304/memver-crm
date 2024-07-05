<?php

namespace App\DataGrids\Mail;

use Illuminate\Support\Facades\DB;
use App\DataGrids\DataGrid;
use Gate;

class EmailDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('emails')
            ->select(
                'emails.id',
                'emails.name',
                'emails.subject',
                'emails.reply',
                'emails.created_at',
                DB::raw('COUNT(DISTINCT ' . DB::getTablePrefix() . 'email_attachments.id) as attachments')
            )
            ->leftJoin('email_attachments', 'emails.id', '=', 'email_attachments.email_id')
            ->groupBy('emails.id')
            ->where('folders', 'like', '%"' . request('route') . '"%')
            ->whereNull('parent_id');

        $this->addFilter('id', 'emails.id');
        $this->addFilter('name', 'emails.name');
        $this->addFilter('created_at', 'emails.created_at');
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
            'index'      => 'attachments',
            'label'      => '<i class="ri-attachment-line"></i>',
            'type'       => 'string',
            'searchable' => false,
            'filterable' => false,
            'sortable'   => false,
            'closure'    => function ($row) {
                if ($row->attachments) {
                    return '<i class="ri-attachment-line"></i>';
                }
            },
        ]);

        $this->addColumn([
            'index'    => 'name',
            'label'    => 'datagrid.from',
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'subject',
            'label'    => 'datagrid.subject',
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                return '<div class="subject-wrapper"><span class="subject-content">' . $row->subject . '</span><span class="reply"> - ' . substr(strip_tags($row->reply), 0, 225) . '<span></div>';
            },
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => 'datagrid.created_at',
            'type'       => 'date_range',
            'searchable' => false,
            'sortable'   => true,
            'closure'    => function ($row) {
                return core()->formatDate($row->created_at);
            },
        ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        if (Gate::allows('mail_delete')) {
            $this->addAction([
                'title'  => request('route') == 'draft'
                    ? trans('app.ui.datagrid.edit')
                    : trans('app.ui.datagrid.view'),
                'method' => 'GET',
                'route'  => 'mail.view',
                'params' => ['route' => request('route')],
                'icon'   => request('route') == 'draft'
                    ? 'ri-pencil-line'
                    : 'ri-eye-line'
            ]);
        }

        if (Gate::allows('mail_delete')) {
            $this->addAction([
                'title'        => trans('app.ui.datagrid.delete'),
                'method'       => 'DELETE',
                'route'        => 'mail.delete',
                'params'       => [
                                    'type' => request('route') == 'trash'
                                        ? 'delete'
                                        : 'trash'
                                ],
                'confirm_text' => trans('app.ui.datagrid.massaction.delete', ['resource' => 'email']),
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

        if (request('route') == 'trash') {
            if (Gate::allows('mail_edit')) {
                $this->addMassAction([
                    'type'   => 'delete',
                    'label'  => ('datagrid.move-to-inbox'),
                    'action' => route('mail.mass_update', [
                                    'folders' => ['inbox'],
                                ]),
                    'method' => 'PUT',
                ]);
            }
        }
        if (Gate::allows('mail_delete')) {
            $this->addMassAction([
                'type'   => 'delete',
                'label'  => ('ui.datagrid.delete'),
                'action' => route('mail.mass_delete', [
                                'type' => request('route') == 'trash'
                                    ? 'delete'
                                    : 'trash',
                            ]),
                'method' => 'PUT',
            ]);
        }
    }
}
