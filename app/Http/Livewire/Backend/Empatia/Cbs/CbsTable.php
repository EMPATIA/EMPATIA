<?php

namespace App\Http\Livewire\Backend\Empatia\Cbs;

use App\Helpers\Empatia\Cbs\HCb;
use App\Helpers\HDatatable;
use App\Http\Controllers\Backend\Empatia\Cbs\CbsController;
use App\Models\Empatia\Cbs\Cb;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;


class CbsTable extends DataTableComponent
{
    private $prefix = "backend.empatia.cbs.";
    public $cbType;



    public function mount($type = 'all')
    {
        if ($type != 'all')
            $this->cbType = HCb::validateType($type);
    }

    /**
     * Laravel Livewire Tables query builder
     *
     * @return Builder
     */
    public function builder() : Builder {
        $query = Cb::query()
            ->when($this->cbType && $this->cbType != 'all', function ($q) {
                return $q->whereType($this->cbType);
            });
        return $query;
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
            'subject' => [
                'class' => 'col-2',
            ],
            'from_name' => [
                'class' => 'col-3 col-md-2',
            ],
            'from_email' => [
                'class' => 'col-3 col-md-2',
            ],
            'user_email' => [
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
        $filters = HDatatable::addFilterDates($filters);

        return $filters;
    }

    /**
     * Laravel Livewire Tables columns builder
     *
     * @return array
     */
    public function columns(): array
    {
        return [
            Column::make(__('backend::generic.id'), 'id')
                ->searchable()
                ->sortable()
                ->hideIf( !auth()->user()->hasAnyRole(['admin','laravel-admin']) ),
            Column::make(__('backend::generic.title'), 'title')
                ->format(function ($value, $model, $column) {
                    $action = action([CbsController::class, 'show'], ['type' => $this->cbType ?? 'all', 'id' => $model->id]);
                    return '<a href="' . $action . '">' . getFieldLang($model, 'title') ?? '-' . '</a>';
                })
                ->sortable(function (Builder $query, string $direction) {
                    return $query->orderBy('title->' . getLang(), $direction);
                })
                ->searchable(function (Builder $builder, string $term) {
                    return $builder->orWhere('title->' . getLang(), 'like', '%' . $term . '%');
                })
                ->html(),
            Column::make(__('backend::generic.start_date'), 'start_date')
                ->sortable()
                ->searchable(),
            Column::make(__('backend::generic.end_date'), 'end_date')
                ->sortable()
                ->searchable(),
            Column::make(__('backend::generic.code'), 'code')
                ->sortable()
                ->searchable()
                ->hideIf( !auth()->user()->hasAnyRole(['admin','laravel-admin']) ),
            Column::make(__('backend::generic.type'), 'type')
                ->sortable()
                ->searchable()
                ->hideIf( !auth()->user()->hasAnyRole(['admin','laravel-admin']) ),
            Column::make('Action', 'action')
                ->label(
                    function ($model) {
                        $routes = [
                            'show' => route('cbs.show', ['type' => $model->type, 'id' => $model->id]),
                            'edit' => route('cbs.edit', ['type' => $model->type, 'id' => $model->id]),
                            'delete' => route('cbs.delete', ['type' => $model->type, 'id' => $model->id]),
                            'restore' => route('cbs.restore', ['type' => $model->type, 'id' => $model->id])
                        ];
                        return HDatatable::getTableActions($model, $routes, (bool)$this->getAppliedFilterWithValue('deleted_at'));
                    }
                )
                ->html()
        ];
    }

}
