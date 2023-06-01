<?php

namespace App\Http\Livewire\Backend\CMS\Languages;

use App\Helpers\HDatatable;
use App\Http\Controllers\Backend\CMS\LanguagesController;
use App\Models\Backend\CMS\Language;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class LanguagesTable extends DataTableComponent
{

    /**
     * Laravel Livewire Tables query builder
     *
     * @return Builder
     */
    public function builder() : Builder {
        return Language::query();
    }

    /**
     * Laravel Livewire Tables configuration
     *
     * @return void
     */
    public function configure() : void {
        HDatatable::applyTableConfigureDefaults($this);

        $this->setDefaultSort('name');
        $this->setAdditionalSelects(['id']);

        HDatatable::applyTableConfigureTable($this);

        HDatatable::applyTableConfigureHeader($this, [
        ]);

        HDatatable::applyTableConfigureColumn($this, []);

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
            Column::make(__('backend.generic.name'), 'name')
                ->format(fn($value, $row, Column $column) => "<a href='".route('cms.languages.show', ['id' => $row->id])."'>".$value."</a>")
                ->html()
                ->sortable()
                ->searchable(),
            Column::make(__('backend.generic.locale'), 'locale')
                ->sortable()
                ->searchable(),
            BooleanColumn::make('Default')
                ->collapseOnTablet(),
            BooleanColumn::make('Backend')
                ->collapseOnTablet(),
            BooleanColumn::make('Frontend')
                ->collapseOnTablet(),
            Column::make('Action', 'action')
                ->label(
                    function ($model) {
                        $routes = [
                            'show' => route('cms.languages.show', ['id' => $model->id]),
                            'edit' => route('cms.languages.edit', ['id' => $model->id]),
                            'delete' => route('cms.languages.delete', ['id' => $model->id]),
                            'restore' => route('cms.languages.restore', ['id' => $model->id])
                        ];
                        return HDatatable::getTableActions($model, $routes, (bool)$this->getAppliedFilterWithValue('deleted_at'));
                    }
                )
                ->html()
        ];

        return $columns;
    }
}
