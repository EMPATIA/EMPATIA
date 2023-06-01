<?php

namespace App\Http\Livewire\Backend;

use App\Helpers\HDatatable;
use App\Http\Controllers\Backend\ConfigurationsController;
use App\Models\Backend\Configuration;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ConfigurationsTable extends DataTableComponent
{

    /**
     * Laravel Livewire Tables query builder
     *
     * @return Builder
     */
    public function builder() : Builder {
        return Configuration::query();
    }

    /**
     * Laravel Livewire Tables columns configuration
     *
     * @return void
     */
    public function configure() : void {
        HDatatable::applyTableConfigureDefaults($this);

        $this->setDefaultSort('code');
        $this->setAdditionalSelects(['id']);

        HDatatable::applyTableConfigureTable($this);

        HDatatable::applyTableConfigureHeader($this, [
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
            Column::make(__('backend::generic.id'), 'id')
                ->searchable()
                ->sortable()
                ->collapseOnTablet(),
            Column::make(__('backend::generic.code'), 'code')
                ->format(fn($value, $row, Column $column) => "<a href='".route('configurations.show', ['id' => $row->id])."'>".$value."</a>")
                ->html()
                ->sortable()
                ->searchable(),
            Column::make('Action', 'action')
                ->label(
                    function ($model) {
                        $routes = [
                            'show' => route('configurations.show', ['id' => $model->id]),
                            'edit' => route('configurations.edit', ['id' => $model->id]),
                            'delete' => route('configurations.delete', ['id' => $model->id]),
                            'restore' => route('configurations.restore', ['id' => $model->id])
                        ];
                        return HDatatable::getTableActions($model, $routes, (bool)$this->getAppliedFilterWithValue('deleted_at'));
                    }
                )
                ->html()
        ];

        return $columns;
    }
}
