<?php

namespace App\Http\Livewire\Backend\Notifications;

use App\Helpers\HDatatable;
use App\Http\Controllers\Backend\Notifications\SmsController;
use App\Models\Backend\Notifications\SMS;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;


class SmsTable extends DataTableComponent
{
    private $prefix = "backend.notifications.sms.";

    /**
     * Laravel Livewire Tables query builder
     *
     * @return Builder
     */
    public function builder() : Builder {
        return SMS::query();
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
            'phone_number' => [
                'class' => 'col-2',
            ],
            'content' => [
                'class' => 'col-3 col-md-2',
            ],
            'created_at' => [
                'class' => 'col-3 col-md-2',
            ],
        ]);

        HDatatable::applyTableConfigureColumn($this, [
        ]);

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
        $columns = [
            Column::make(__('backend.generic.id'), 'id')
                ->sortable()
                ->searchable()
                ->collapseOnTablet(),
            Column::make(__($this->prefix.'generic.recipient-number'), 'phone_number')
                ->searchable(),
            Column::make(__('backend.generic.content'), 'content')
                ->sortable()
                ->searchable(),
            Column::make(__('backend.generic.created-at'), 'created_at')
                ->searchable()
                ->sortable()
                ->format(function ($value) {
                    return Carbon::parse($value)->isoFormat('Y-MM-DD • HH:mm:ss');
                })
                ->collapseOnTablet(),
            Column::make('Action', 'action')
                ->label(
                    function ($model) {
                        $routes = [
                            'show' => route('notifications.sms.show', ['id' => $model->id]),
                            'delete' => route('notifications.sms.delete', ['id' => $model->id]),
                            'restore' => route('notifications.sms.restore', ['id' => $model->id])
                        ];
                        return HDatatable::getTableActions($model, $routes, (bool)$this->getAppliedFilterWithValue('deleted_at'), ["show", "delete"]);
                    }
                )
                ->html()
        ];


        return $columns;
    }
}
