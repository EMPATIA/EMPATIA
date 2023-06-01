<?php

namespace App\Http\Livewire\Backend\Empatia;

use App\Helpers\HDatatable;
use App\Models\Empatia\LoginLevel;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Carbon\Carbon;

class LoginLevelsTable extends DataTableComponent
{
    private $prefix = "backend.empatia.login-levels.";

    /**
     * Laravel Livewire Tables query builder
     *
     * @return Builder
     */
    public function builder() : Builder {
        return LoginLevel::query();
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
            'name' => [
                'class' => 'col-3 col-md-2',
            ],
            'dependencies' => [
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
                ->format(fn($value, $row, Column $column) => "<a href='".route('login-levels.show', ['id' => $row->id])."'>".$value."</a>")
                ->html()
                ->sortable()
                ->searchable(),
            Column::make(__('backend.generic.name'), 'name')
                ->format(function($value) {
                    return $value->{getLang()} ?? '--';
                })
                ->sortable(function(Builder $query, String $direction) {
                    return $query->orderBy('name->'.getLang(), $direction);
                })
                ->searchable(function(Builder $builder, String $term) {
                    return $builder->orWhere('name->'.getLang(), 'like', '%'.$term.'%');
                }),
            Column::make(__($this->prefix.'dependencies'), 'dependencies')
                ->format(function($value) {
                    return $value->{getLang()} ?? '--';
                })
                ->sortable(function(Builder $query, String $direction) {
                    return $query->orderBy('name->'.getLang(), $direction);
                })
                ->searchable(function(Builder $builder, String $term) {
                    return $builder->orWhere('name->'.getLang(), 'like', '%'.$term.'%');
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
                            'show' => route('login-levels.show', ['id' => $model->id]),
                            'edit' => route('login-levels.edit', ['id' => $model->id]),
                            'delete' => route('login-levels.delete', ['id' => $model->id]),
                            'restore' => route('login-levels.restore', ['id' => $model->id])
                        ];
                        return HDatatable::getTableActions($model, $routes, (bool)$this->getAppliedFilterWithValue('deleted_at'));
                    }
                )
                ->html()
        ];

        return $columns;
    }
}
