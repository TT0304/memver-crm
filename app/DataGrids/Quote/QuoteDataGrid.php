<?php

namespace App\DataGrids\Quote;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Traits\Admin\ProvideDropdownOptions;
use App\DataGrids\DataGrid;
use App\Repositories\User\UserRepository;
use Gate;

class QuoteDataGrid extends DataGrid
{
    use ProvideDropdownOptions;

    /**
     * User repository instance.
     *
     * @var \App\Repositories\User\UserRepository
     */
    protected $userRepository;

    /**
     * Create datagrid instance.
     *
     * @param \App\Repositories\User\UserRepository  $userRepository
     * @return void
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

        parent::__construct();
    }

    /**
     * Place your datagrid extra settings here.
     *
     * @return void
     */
    public function init()
    {
        $this->setRowProperties([
            'backgroundColor' => '#ffd0d6',
            'condition' => function ($row) {
                if (Carbon::createFromFormat('Y-m-d H:i:s',  $row->expired_at)->endOfDay() < Carbon::now()) {
                    return true;
                }

                return false;
            }
        ]);
    }

    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('quotes')
            ->addSelect(
                'quotes.id',
                'quotes.subject',
                'quotes.expired_at',
                'quotes.sub_total',
                'quotes.discount_amount',
                'quotes.tax_amount',
                'quotes.adjustment_amount',
                'quotes.grand_total',
                'quotes.created_at',
                'client_users.id as user_id',
                'client_users.name as sales_person',
                'persons.id as person_id',
                'persons.name as person_name'
            )
            ->leftJoin('client_users', 'quotes.user_id', '=', 'client_users.id')
            ->leftJoin('persons', 'quotes.person_id', '=', 'persons.id');

        $currentUser = auth()->user();

        // if ($currentUser->view_permission != 'global') {
        //     if ($currentUser->view_permission == 'group') {
        //         $queryBuilder->whereIn('quotes.user_id', $this->userRepository->getCurrentUserGroupsUserIds());
        //     } else {
        //         $queryBuilder->where('quotes.user_id', $currentUser->id);
        //     }
        // }

        $this->addFilter('id', 'quotes.id');
        $this->addFilter('user', 'quotes.user_id');
        $this->addFilter('sales_person', 'quotes.user_id');
        $this->addFilter('person_name', 'persons.name');
        $this->addFilter('expired_at', 'quotes.expired_at');
        $this->addFilter('created_at', 'quotes.created_at');

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
            'index'    => 'subject',
            'label'    => ('datagrid.subject'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'            => 'sales_person',
            'label'            => ('datagrid.sales-person'),
            'type'             => 'dropdown',
            'dropdown_options' => $this->getUserDropdownOptions(),
            'sortable'         => true,
            'closure'          => function ($row) {
                $route = urldecode(route('settings.users.index', ['id[eq]' => $row->user_id]));

                return "<a href='" . $route . "'>" . $row->sales_person . "</a>";
            },
        ]);

        $this->addColumn([
            'index'    => 'person_name',
            'label'    => ('datagrid.person'),
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                $route = urldecode(route('contacts.persons.index', ['id[eq]' => $row->person_id]));

                return "<a href='" . $route . "'>" . $row->person_name . "</a>";
            },
        ]);

        $this->addColumn([
            'index'    => 'sub_total',
            'label'    => ('datagrid.sub-total'),
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                return core()->formatBasePrice($row->sub_total, 2);
            },
        ]);

        $this->addColumn([
            'index'    => 'discount_amount',
            'label'    => ('datagrid.discount'),
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                return core()->formatBasePrice($row->discount_amount, 2);
            },
        ]);

        $this->addColumn([
            'index'    => 'tax_amount',
            'label'    => ('datagrid.tax'),
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                return core()->formatBasePrice($row->tax_amount, 2);
            },
        ]);

        $this->addColumn([
            'index'    => 'adjustment_amount',
            'label'    => ('datagrid.adjustment'),
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                return core()->formatBasePrice($row->adjustment_amount, 2);
            },
        ]);

        $this->addColumn([
            'index'    => 'grand_total',
            'label'    => ('datagrid.grand-total'),
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                return core()->formatBasePrice($row->grand_total, 2);
            },
        ]);

        $this->addColumn([
            'index'      => 'expired_at',
            'label'      => ('leads.expired-at'),
            'type'       => 'date_range',
            'searchable' => false,
            'sortable'   => true,
            'closure'    => function ($row) {
                return core()->formatDate($row->expired_at, 'd M Y');
            },
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => ('datagrid.created_at'),
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
        if (Gate::allows('quote_edit')) {
            $this->addAction([
                'title'  => trans('app.ui.datagrid.edit'),
                'method' => 'GET',
                'route'  => 'quotes.edit',
                'icon'   => 'pencil-icon',
            ]);
        }
        if (Gate::allows('quote_delete')) {
            $this->addAction([
                'title'        => trans('app.ui.datagrid.delete'),
                'method'       => 'DELETE',
                'route'        => 'quotes.delete',
                'confirm_text' => trans('app.ui.datagrid.massaction.delete', ['resource' => 'user']),
                'icon'         => 'trash-icon',
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
        if (Gate::allows('quote_delete')) {
            $this->addMassAction([
                'type'   => 'delete',
                'label'  => ('ui.datagrid.delete'),
                'action' => route('quotes.mass_delete'),
                'method' => 'PUT',
            ]);
        }
    }
}
