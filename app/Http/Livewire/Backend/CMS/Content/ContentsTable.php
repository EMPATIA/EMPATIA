<?php

namespace App\Http\Livewire\Backend\CMS\Content;

use App\Helpers\HDatatable;
use App\Http\Controllers\Backend\CMS\ContentsController;
use App\Models\Backend\CMS\Content;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class ContentsTable extends DataTableComponent
{
    public $type;
    private $prefix = 'backend.cms.contents.';
    /**
     * Mount livewire attributes with automatic store in class attribute
     *
     * @param  mixed $type
     * @return void
     */
    public function mount($type) {
    }

    /**
     * Laravel Livewire Tables query builder
     *
     * @return Builder
     */
    public function builder() : Builder {
        if($this->type != 'all')
            return $query = Content::whereType($this->type);
        else
            return Content::query();
    }

    /**
     * Laravel Livewire Tables columns builder
     *
     * @return array
     */
    public function configure() : void {
        HDatatable::applyTableConfigureDefaults($this);

        $this->setDefaultSort('title->'.getLang());
        $this->setAdditionalSelects(['type']);

        HDatatable::applyTableConfigureTable($this);

        HDatatable::applyTableConfigureHeader($this, [
            'slug' => [
                'class' => 'col-2 d-none d-lg-table-cell',
            ],
            'status' => [
                'class' => 'col-1',
            ],
        ]);

        HDatatable::applyTableConfigureColumn($this, [
            'title' => fn($row) => [
                'class' => 'text-truncate',
                'data-bs-toggle' => 'tooltip',
                'title' => getFieldLang($row, 'title'),
            ],
            'slug' => fn($row) => [
                'class' => 'text-truncate d-none d-lg-table-cell',
                'data-bs-toggle' => 'tooltip',
                'title' => getFieldLang($row, 'slug'),
            ],
            'status' => fn($row) => [
                'data-bs-toggle' => 'tooltip',
                'title' => __($this->prefix."status.".$row->status),
            ],
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
        $filters =  HDatatable::addFilterDates($filters);

        array_push($filters,
            SelectFilter::make(__('backend.generic.status'), 'status')
                ->options([
                    '' => __('backend.datatable.filters.all'),
                    'published' => __($this->prefix.'table-column.status-published'),
                    'unpublished' => __($this->prefix.'table-column.status-unpublished'),
                ])
                ->filter(function(Builder $builder, string $value) {
                    return $builder->whereStatus($value);
                }));

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
                ->searchable()
                ->sortable()
                ->hideIf( !auth()->user()->hasAnyRole(['admin','laravel-admin']) ),
            Column::make(__('backend.generic.title'), 'title')
                ->format(fn($value, $row, Column $column) => "<a href='".route('cms.content.show', ['type' => $row->type, 'id' => $row->id])."'>".getFieldLang($row, "title")."</a>")
                ->html()
                ->sortable()
                ->searchable(),
            Column::make(__('backend.generic.slug'), 'slug')
                ->format(fn($value, $row, Column $column) => "<a href='".route('cms.content.show', ['type' => $row->type, 'id' => $row->id])."'>".getFieldLang($row, "slug")."</a>")
                ->html()
                ->sortable()
                ->searchable(),
            BooleanColumn::make(__('backend.generic.status'), 'status')
                ->setCallback(fn(string $value, $row) => $value == 'published'),
            Column::make('Action', 'action')
                ->label(
                    function ($model) {
                        $routes = [
                            'show' => route('cms.content.show', ['type' => $model->type, 'id' => $model->id]),
                            'edit' => route('cms.content.show', ['type' => $model->type, 'id' => $model->id]),
                            'delete' => route('cms.content.delete', ['type' => $model->type, 'id' => $model->id]),
                            'restore' => route('cms.content.restore', ['type' => $model->type, 'id' => $model->id])
                        ];
                        return HDatatable::getTableActions($model, $routes, (bool)$this->getAppliedFilterWithValue('deleted_at'));
                    }
                )
                ->html()
        ];

        return $columns;
    }
}

