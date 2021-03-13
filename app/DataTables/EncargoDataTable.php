<?php
/**
 * File name: EncargoDataTable.php
 * Last modified: 2020.04.30 at 08:21:08
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\DataTables;

use App\Models\CustomField;

use App\Models\Encargo;
use Barryvdh\DomPDF\Facade as PDF;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

class EncargoDataTable extends DataTable
{
    /**
     * custom fields columns
     * @var array
     */
    public static $customFields = [];

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);
        $columns = array_column($this->getColumns(), 'data');
        $dataTable = $dataTable
            ->editColumn('id', function ($encargo) {
                return "#".$encargo->id;
            })
            ->editColumn('updated_at', function ($encargo) {
                return getDateColumn($encargo, 'updated_at');
            })
            ->editColumn('monto', function ($encargo) {
                return getPriceColumn($encargo, 'monto');
            })
            ->editColumn('punto_b', function ($encargo) {
                return $encargo->direccion_b;
            })
            ->editColumn('punto_a', function ($encargo) {
                return $encargo->direccion_a;
            })
            // ->editColumn('pagada', function ($encargo) {
            //     return getBooleanColumn($encargo, 'pagada');
            // })
            ->editColumn('active', function ($encargo) {
                return getBooleanColumn($encargo, 'active');
            })
            ->editColumn('assigned', function ($encargo) {
                return getBooleanColumn($encargo, 'assigned');
            })

            ->addColumn('action', 'encargos.datatables_actions')
            ->rawColumns(array_merge($columns, ['action']));

        return $dataTable;
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        $columns = [
            [
                'data' => 'id',
                'title' => trans('lang.encargo_id'),

            ],
            [
                'data' => 'user.name',
                'name' => 'user.name',
                'title' => trans('lang.encargo_user_id'),

            ],
            [
                'data' => 'punto_a',
                'name' => 'punto_a',
                'title' => trans('Punto A'),


            ],

            [
                'data' => 'monto',
                'name' => 'monto',
                'title' => trans('Monto'),

            ],

            [
                'data' => 'punto_b',
                'name' => 'punto_b',
                
                'title' => trans('Punto B'),

            ],
            [
                'data' => 'active',
                'title' => trans('lang.order_active'),

            ],
            // [
            //     'data' => 'pagada',
            //     'name' => 'pagada',
            //     'title' => trans('Pagada'),

            // ],
            [
                'data' => 'assigned',
                'name' => 'assigned',
                'title' => trans('Asignada'),


            ],
            [
                'data' => 'updated_at',
                'title' => trans('lang.earning_updated_at'),
                'searchable' => false,
            ]

        ];

        $hasCustomField = in_array(Encargo::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', Encargo::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.order_' . $field->name),
                    'orderable' => false,
                    'searchable' => false,
                ]]);
            }
        }
        return $columns;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Post $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Encargo $model)
    {
        if (auth()->user()->hasRole('admin')) {
            return $model->newQuery()->with("user");
        } else if (auth()->user()->hasRole('manager')) {
            return $model->newQuery()->with("user")
                ->where('encargos.driver_id', auth()->id())
                ->groupBy('encargos.id')
                ->select('encargos.*');
            // return $model->newQuery()->with("user")
            //     ->join("product_orders", "orders.id", "=", "product_orders.order_id")
            //     ->join("products", "products.id", "=", "product_orders.product_id")
            //     ->join("user_markets", "user_markets.market_id", "=", "products.market_id")
            //     ->where('user_markets.user_id', auth()->id())
            //     ->groupBy('orders.id')
            //     ->select('encargos.*');
        } else if (auth()->user()->hasRole('client')) {
            return $model->newQuery()->with("user")
                ->where('encargos.user_id', auth()->id())
                ->groupBy('encargos.id')
                ->select('encargos.*');
        } else if (auth()->user()->hasRole('driver')) {
            return $model->newQuery()->with("user")
                ->where('encargos.driver_id', auth()->id())
                ->groupBy('encargos.id')
                ->select('encargos.*');
        } else {
            return $model->newQuery()->with("user");
        }

    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['title'=>trans('lang.actions'),'width' => '80px', 'printable' => false, 'responsivePriority' => '100'])
            ->parameters(array_merge(
                [
                    'language' => json_decode(
                        file_get_contents(base_path('resources/lang/' . app()->getLocale() . '/datatable.json')
                        ), true),
                    'order' => [ [0, 'desc'] ],
                ],
                config('datatables-buttons.parameters')
            ));
    }

    /**
     * Export PDF using DOMPDF
     * @return mixed
     */
    public function pdf()
    {
        $data = $this->getDataForPrint();
        $pdf = PDF::loadView($this->printPreview, compact('data'));
        return $pdf->download($this->filename() . '.pdf');
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'encargodatatable_' . time();
    }
}