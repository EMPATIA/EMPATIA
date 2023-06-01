<?php

namespace App\Http\Livewire\Backend\Notifications;

use App\Helpers\HDatatable;
use App\Http\Controllers\Backend\Notifications\TemplatesController;
use App\Models\Backend\Notifications\Template;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Carbon\Carbon;

class TemplatesTable extends DataTableComponent
{
    private $prefix = "backend.notifications.templates.";

    /**
     * Laravel Livewire Tables query builder
     *
     * @return Builder
     */
    public function builder() : Builder {
        return Template::query();
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
            'subject' => [
                'class' => 'col-3 col-md-2',
            ],
            'created_at' => [
                'class' => 'col-2 col-md-2',
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
        return HDatatable::addFilterDates($filters);
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
            Column::make(__('backend.generic.code'), 'code')
                ->format(fn($value, $row, Column $column) => "<a href='".route('notifications.templates.show', ['id' => $row->id])."'>".$value."</a>")
                ->html()
                ->sortable()
                ->searchable(),
            Column::make(__($this->prefix.'generic.subject'), 'subject')
                ->format(function($value) {
                    return $value->{getLang()} ?? '--';
                })
                ->sortable(function(Builder $query, String $direction) {
                    return $query->orderBy('subject->'.getLang(), $direction);
                })
                ->searchable(function(Builder $builder, String $term) {
                    return $builder->orWhere('subject->'.getLang(), 'like', '%'.$term.'%');
                }),
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
                            'show' => route('notifications.templates.show', ['id' => $model->id]),
                            'edit' => route('notifications.templates.edit', ['id' => $model->id]),
                            'delete' => route('notifications.templates.delete', ['id' => $model->id]),
                            'restore' => route('notifications.templates.restore', ['id' => $model->id])
                        ];
                        return HDatatable::getTableActions($model, $routes, (bool)$this->getAppliedFilterWithValue('deleted_at'));
                    }
                )
                ->html()
        ];

        return $columns;
    }
}
