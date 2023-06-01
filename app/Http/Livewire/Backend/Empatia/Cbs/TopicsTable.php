<?php

namespace App\Http\Livewire\Backend\Empatia\Cbs;

use App\Helpers\HDatatable;
use App\Http\Controllers\Backend\Empatia\Cbs\TopicsController;
use App\Models\Empatia\Cbs\Cb;
use App\Models\Empatia\Cbs\Topic;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;


class TopicsTable extends DataTableComponent
{

    public $cbId;
    public $cbType; //Used to personalize index table
    public $cb;
    // Index Filters
    public $deletionStatusFilter = false;


    //Columns to be shown according to cbType
    private $topicColumns = [
        'template' => [
            'title',
            'created_by',
            'created_at'
        ],
    ];

    public function mount(){
        $this->cb = Cb::findOrFail($this->cbId);
    }
    /**
     * Laravel Livewire Tables query builder
     *
     * @return Builder
     */
    public function builder(): Builder
    {
        if($this->cbId){
            $query = Topic::whereCbId($this->cbId);
        }else{
            $query = Topic::query();

        }
        return $query;

    }

    /**
     * Laravel Livewire Tables configuration
     *
     * @return void
     */
    public function configure() : void {
        HDatatable::applyTableConfigureDefaults($this);

        $this->setDefaultSort('created_at', 'desc');
        $this->setAdditionalSelects(['id']);

        HDatatable::applyTableConfigureTable($this);

        HDatatable::applyTableConfigureHeader($this, [
            'locale' => [
                'class' => 'col-auto col-md-1',
            ],
            'name' => [
                'class' => 'col-auto col-md-2'
            ],
            'default' => [
                'class' => 'col-md-2',
            ],
            'backend' => [
                'class' => 'col-md-2',
            ],
            'frontend' => [
                'class' => 'col-md-2',
            ],
            'action' => [
                'class' => 'col-auto',
            ]
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

        $statusOptions = ['' => __('backend.generic.status')];
        $this->type = $this->type ?? 'default';
        $statusConfig = (array)data_get($this->cb, 'data.configurations.topic.status', []);
        foreach ($statusConfig as $key => $state) {
            if (!empty(data_lang_get($state, 'title')))
                $statusOptions[$key] = data_lang_get($state, 'title');
        }

        array_push($filters,
            SelectFilter::make(__('backend.generic.status'), 'status')
                ->options(
                    $statusOptions
                )
                ->filter(function(Builder $builder, string $value) {
                    return $builder->whereState($value);
                }));

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
                ->hideIf(self::showColumn('id'))
                ->searchable()
                ->sortable(),
            Column::make(__('backend::generic.title'), 'title')
                ->hideIf(self::showColumn('title'))
                ->format(function ($value, $model, $column) {
                    $action = action([TopicsController::class, 'show'], ['type' => $this->cbType, 'cbId' => $this->cbId, 'id' => $model->id]);
                    return '<a href="' . $action . '">' . getFieldLang($model, 'title') ?? '--' . '</a>';
                })
                ->html()
                ->sortable(function (Builder $query, $direction) {
                    return $query->orderBy('title->' . getLang(), $direction);
                })
                ->searchable(function (Builder $builder, string $term) {
                    return $builder->orWhere('title->' . getLang(), 'like', '%' . $term . '%');
                }),
            Column::make(__('backend::generic.status'), 'status')
                ->hideIf(self::showColumn('status'))
                ->format(function ($value, $model, $column) {
                    $topic = Topic::withTrashed()->findOrFail($model->id);
                    return $topic->cb->stateLabel($topic->state, getLang());
                }),
            Column::make(__('backend::generic.created-at'), 'created_at')
                ->hideIf(self::showColumn('created_at'))
                ->searchable()
                ->sortable()
                ->format(function ($value) {
                    return $value->format('Y-m-d');
                }),
            Column::make('Action', 'action')
                ->label(
                    function ($model) {
                        $routes = [
                            'show' => route('cbs.backend.topics.show', ['type' => $this->cbType, 'cbId' => $this->cbId, 'id' => $model->id]),
                            'edit' => route('cbs.backend.topics.edit', ['type' => $this->cbType, 'cbId' => $this->cbId, 'id' => $model->id]),
                            'delete' => route('cbs.backend.topics.delete', ['type' => $this->cbType, 'cbId' => $this->cbId, 'id' => $model->id]),
                            'restore' => route('cbs.backend.topics.restore', ['type' => $this->cbType, 'cbId' => $this->cbId, 'id' => $model->id])
                        ];
                        return HDatatable::getTableActions($model, $routes, (bool)$this->getAppliedFilterWithValue('deleted_at'));
                    }
                )
                ->html()
        ];
    }

    public function showColumn($columnType)
    {
        foreach ($this->topicColumns as $key => $column) {
            if ($key == $this->cbType) {
                foreach ($column as $colType) {
                    if ($colType == $columnType) {
                        return false;
                    }
                }
                return true;
            }
        }
        return false;
    }

}
