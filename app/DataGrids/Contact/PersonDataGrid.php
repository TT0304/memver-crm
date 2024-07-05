<?php

namespace App\DataGrids\Contact;

use Illuminate\Support\Facades\DB;
use App\Traits\Contact\ProvideDropdownOptions;
use App\DataGrids\DataGrid;
use Gate;

class PersonDataGrid extends DataGrid
{
    use ProvideDropdownOptions;

    /**
     * Export option.
     *
     * @var boolean
     */
    protected $export;

    /**
     * Create datagrid instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // $this->export = bouncer()->hasPermission('contacts.persons.export') ? true : false;
    }

    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('persons')
            ->addSelect(
                'persons.id',
                'persons.name as person_name',
                'persons.emails',
                'persons.contact_numbers',
                'organizations.name as organization'
            )
            ->leftJoin('organizations', 'persons.organization_id', '=', 'organizations.id');

        $this->addFilter('id', 'persons.id');
        $this->addFilter('person_name', 'persons.name');
        $this->addFilter('organization', 'organizations.id');

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
            'index'      => 'id',
            'label'      => ('datagrid.id'),
            'type'       => 'string',
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'    => 'person_name',
            'label'    => ('datagrid.name'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'emails',
            'label'    => ('datagrid.emails'),
            'type'     => 'string',
            'sortable' => false,
            'closure'  => function ($row) {
                $emails = json_decode($row->emails, true);

                if ($emails) {
                    return collect($emails)->pluck('value')->join(', ');
                }
            },
        ]);

        $this->addColumn([
            'index'    => 'contact_numbers',
            'label'    => ('datagrid.contact_numbers'),
            'type'     => 'string',
            'sortable' => false,
            'closure'  => function ($row) {
                $contactNumbers = json_decode($row->contact_numbers, true);

                if ($contactNumbers) {
                    return collect($contactNumbers)->pluck('value')->join(', ');
                }
            },
        ]);

        $this->addColumn([
            'index'            => 'organization',
            'label'            => ('datagrid.organization_name'),
            'type'             => 'dropdown',
            'dropdown_options' => $this->getOrganizationDropdownOptions(),
            'sortable'         => false,
        ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        if (Gate::allows('person_edit')) {
            $this->addAction([
                'title'  => trans('app.ui.datagrid.edit'),
                'method' => 'GET',
                'route'  => 'contacts.persons.edit',
                'icon'   => 'ri-pencil-line',
            ]);
        }
        if (Gate::allows('person_delete')) {
            $this->addAction([
                'title'        => trans('app.ui.datagrid.delete'),
                'method'       => 'DELETE',
                'route'        => 'contacts.persons.delete',
                'confirm_text' => trans('app.ui.datagrid.massaction.delete', ['resource' => trans('app.contacts.persons.person')]),
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
        if (Gate::allows('person_delete')) {
            $this->addMassAction([
                'type'   => 'delete',
                'label'  => ('ui.datagrid.delete'),
                'action' => route('contacts.persons.mass_delete'),
                'method' => 'PUT',
            ]);
        }
    }
}
