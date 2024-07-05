<?php

namespace App\DataGrids\Contact;

use Illuminate\Support\Facades\DB;
use App\Repositories\Contact\PersonRepository;
use App\DataGrids\DataGrid;
use Gate;

class OrganizationDataGrid extends DataGrid
{
    /**
     * Person repository instance.
     *
     * @var \App\Repositories\Contact\PersonRepository
     */
    protected $personRepository;

    /**
     * Create datagrid instance.
     *
     * @return void
     */
    public function __construct(PersonRepository $personRepository)
    {
        parent::__construct();

        $this->personRepository = $personRepository;
    }

    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('organizations')
            ->addSelect(
                'organizations.id',
                'organizations.name',
                // 'organizations.address',
                'organizations.created_at'
            );

        $this->addFilter('id', 'organizations.id');

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
            'label'    => 'datagrid.id',
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
            'index'      => 'persons_count',
            'label'      => ('datagrid.persons_count'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
            'filterable' => false,
            'closure'    => function ($row) {
                $personsCount = $this->personRepository->findWhere(['organization_id' => $row->id])->count();

                $route = urldecode(route('contacts.persons.index', ['organization[in]' => $row->id]));

                return "<a href='" . $route . "'>" . $personsCount . "</a>";
            },
        ]);

        $this->addColumn([
            'index'    => 'created_at',
            'label'    => ('datagrid.created_at'),
            'type'     => 'date_range',
            'sortable' => true,
            'closure'  => function ($row) {
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
        if (Gate::allows('organization_edit')) {
            $this->addAction([
                'title'  => trans('app.ui.datagrid.edit'),
                'method' => 'GET',
                'route'  => 'contacts.organizations.edit',
                'icon'   => 'ri-pencil-line',
            ]);
        }
        if (Gate::allows('organization_delete')) {
            $this->addAction([
                'title'        => trans('app.ui.datagrid.delete'),
                'method'       => 'DELETE',
                'route'        => 'contacts.organizations.delete',
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
        if (Gate::allows('organization_delete')) {
            $this->addMassAction([
                'type'   => 'delete',
                'label'  => ('ui.datagrid.delete'),
                'action' => route('contacts.organizations.mass_delete'),
                'method' => 'PUT',
            ]);
        }
    }
}
