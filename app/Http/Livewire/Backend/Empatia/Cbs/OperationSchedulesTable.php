<?php

namespace App\Http\Livewire\Backend\Empatia\Cbs;

use App\Helpers\HDatatable;
use App\Http\Controllers\Backend\Empatia\Cbs\OperationSchedulesController;
use App\Models\Empatia\Cbs\Cb;
use App\Models\Empatia\Cbs\OperationSchedule;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use Illuminate\Database\Eloquent\Builder;
use \Illuminate\Support\Carbon;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Illuminate\Support\Facades\Route;


class OperationSchedulesTable extends DataTableComponent
{
    private $prefix = "backend.empatia.cbs.default.operation-schedules";

    public $type;
    public $cbId;

    /**
     * Laravel Livewire Tables query builder
     *
     * @return Builder
     */
    public function builder(): Builder {

            $cb = Cb::findOrfail($this->cbId);

            OperationSchedule::linkModel($cb);

            return OperationSchedule::query();
    }

    /**
     * Laravel Livewire Tables columns builder
     *
     * @return array
     */
    public function configure() : void {
        HDatatable::applyTableConfigureDefaults($this);

        $this->setDefaultSort('id');
        $this->setAdditionalSelects(['id']);

        HDatatable::applyTableConfigureTable($this);

        HDatatable::applyTableConfigureHeader($this, [
            'code' => [
                'class' => 'col-2',
            ],
            'description' => [
                'class' => 'col-3 col-md-2',
            ],
            'start_date' => [
                'class' => 'col-2 col-md-2',
            ],
            'end_date' => [
                'class' => 'col-2 col-md-2',
            ],
            'enabled' => [
                'class' => 'col-2 col-md-2',
            ],
            'action' => [
                'class' => 'col-2 col-md-2',
            ],
        ]);

        HDatatable::applyTableConfigureColumn($this, [
        ]);

    }

    public function updated($name, $value): void
    {
        if ($name === 'deleted') {
            $this->resetPage();
        }
    }

    /**
     * Laravel Livewire Tables filters
     *
     * @return array
     */
    public function filters(): array {
        $filters = [];

        $filters = HDatatable::addFilterDeleted($filters);
        $filters = HDatatable::addFilterDates($filters);

        return $filters;
    }

    /**
     * Laravel Livewire Tables columns builder
     *
     * @return array
     */
    public function columns() : array {
        $cb = Cb::findOrfail($this->cbId);

        OperationSchedule::linkModel($cb);
        $columns = [
            Column::make(__('backend.generic.code'), 'code')
                ->format(fn($value, $row, Column $column) => "<a href='".route('cbs.operation-schedules.show', ['type' => $this->type, 'cbId'=> $this->cbId, 'code' => $row->code])."'>".$value."</a>")
                ->html()
                ->sortable()
                ->searchable(),
            Column::make(__($this->prefix.'generic.description'), 'description')
                ->format(function($value) {
                    return $value;
                })
                ->sortable()
                ->searchable(function(Builder $builder, String $term) {
                    return $builder->orWhere('description->'.getLang(), 'like', '%'.$term.'%');
                }),
            Column::make(__('backend.generic.start_date'), 'start_date')
                ->searchable()
                ->sortable()
                ->format(function ($value) {
                    return $value ? Carbon::parse($value)->isoFormat('Y-MM-DD • HH:mm:ss') : null;
                })
                ->collapseOnTablet(),

            Column::make(__('backend.generic.end_date'), 'end_date')
                ->searchable()
                ->sortable()
                ->format(function ($value) {
                    return $value ? Carbon::parse($value)->isoFormat('Y-MM-DD • HH:mm:ss') : null;
                })
                ->collapseOnTablet(),
            BooleanColumn::make(__('backend.generic.enabled'), 'enabled')
                ->format(function ($value) {
                    return $value ? __('backend.generic.enabled') : __('backend.generic.disabled');
                }),
            Column::make('Action', 'action')
                ->label(
                    function ($model) {
                        $routes = [
                            'show' => route('cbs.operation-schedules.show', ['type' => $this->type, 'cbId' => $this->cbId, 'code' => $model->code]),
                            'edit' => route('cbs.operation-schedules.edit', ['type' => $this->type, 'cbId' => $this->cbId, 'code' => $model->code]),
                            'delete' => route('cbs.operation-schedules.delete', ['type' => $this->type, 'cbId' => $this->cbId, 'code' => $model->code]),
                        ];
                        return HDatatable::getTableActions($model, $routes, (bool)$this->getAppliedFilterWithValue('deleted_at'));
                    }
                )
                ->html()
        ];

        return $columns;
    }
}
