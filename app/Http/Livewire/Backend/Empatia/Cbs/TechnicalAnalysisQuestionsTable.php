<?php

namespace App\Http\Livewire\Backend\Empatia\Cbs;

use App\Helpers\HDatatable;
use App\Http\Controllers\Backend\Empatia\Cbs\TechnicalAnalysisController;
use App\Models\Empatia\Cbs\Cb;
use App\Models\Empatia\Cbs\TechnicalAnalysisQuestion;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;


class TechnicalAnalysisQuestionsTable extends DataTableComponent
{
    public Cb $cb;

    /**
     * Laravel Livewire Tables query builder
     *
     * @return Builder
     */
    public function builder() : Builder {
        TechnicalAnalysisQuestion::linkModel($this->cb);
        return TechnicalAnalysisQuestion::query();
    }


    /**
     * Laravel Livewire Tables columns builder
     *
     * @return array
     */
    public function configure() : void {
        $this->setDefaultSort('code');
        HDatatable::applyTableConfigureDefaults($this);
        HDatatable::applyTableConfigureTable($this);
        HDatatable::applyTableConfigureHeader($this, []);
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

        $filters[] = SelectFilter::make(__('backend.generic.type'), 'type')
            // TODO: the line bellow throws an error
//            ->options(['' => __('backend.datatable.filters.all')] + $this->cb?->technicalAnalysisQuestionTypes(true))
            ->filter(function(Builder $builder, string $value) {
                return $builder->where("type", $value);
            });

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
            Column::make(__('backend.generic.code'), 'code')
                ->sortable()
                ->searchable(),
            Column::make(__('backend.generic.type'), 'type')
                ->sortable()
                ->searchable(),
            Column::make(__('backend.generic.value'), 'value')
                ->format(function ($value, $model) {
                    $action = action([TechnicalAnalysisController::class, 'show'], ['type' => $this->cb->type ?? 'all', 'cbId' => $this->cb->id, 'code' => $model->code]);
                    return '<a href="' . $action . '">' . getFieldLang($model, 'value') ?? '-' . '</a>';
                })
                ->sortable(function (Builder $query, string $direction) {
                    return $query->orderBy('value->' . getLang(), $direction);
                })
                ->searchable(function (Builder $builder, string $term) {
                    return $builder->orWhere('value->' . getLang(), 'like', '%' . $term . '%');
                })
                ->html(),
            BooleanColumn::make(__('backend.generic.enabled'), 'enabled'),

            Column::make('Action', 'action')
                ->label(
                    function ($model) {
                        $routes = [
                            'show' => route('cbs.technical-analysis-questions.show', ['type' => $this->cb->type ?? 'all', 'cbId' => $this->cb->id, 'code' => $model->code]),
                            'edit' => route('cbs.technical-analysis-questions.edit', ['type' => $this->cb->type ?? 'all', 'cbId' => $this->cb->id, 'code' => $model->code]),
                            'delete' => route('cbs.technical-analysis-questions.delete', ['type' => $this->cb->type ?? 'all', 'cbId' => $this->cb->id, 'code' => $model->code]),
                            'restore' => route('cbs.technical-analysis-questions.restore', ['type' => $this->cb->type ?? 'all', 'cbId' => $this->cb->id, 'code' => $model->code])
                        ];
                        return HDatatable::getTableActions($model, $routes, (bool)$this->getAppliedFilterWithValue('deleted_at'));
                    }
                )
                ->html()
        ];
    }

}
