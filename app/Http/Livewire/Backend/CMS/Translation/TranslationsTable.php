<?php

namespace App\Http\Livewire\Backend\CMS\Translation;

use App\Exports\TranslationsExport;
use App\Helpers\HCache;
use App\Helpers\HDatatable;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Backend\CMS\Translation;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Carbon\Carbon;

class TranslationsTable extends DataTableComponent
{
    private $prefix = "backend.cms.translations.";
    
    // refreshDatatable listener (used in indexes.js) is needed here, because the $listeners array property has others listeners
    protected $listeners = ['updateTranslation', 'exportTranslations', 'refreshDatatable' => '$refresh'];
    
    public static array $fieldsToExport = ['locale','namespace', 'group', 'item', 'text', 'created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by', 'deleted_at'];
    
    
    public function updateTranslation($id, $value)
    {
        try {
            HCache::flushTranslationId($id);
            Translation::findOrFail($id)->update(['text' => $value]);
            flash()->addSuccess(__('backend.generic.update.ok'));
        } catch (\Exception $e) {
            logError($e->getMessage());
            flash()->addError(__('backend.generic.update.error'));
            return redirect()->back()->withInput();
        }
    }
    
    /**
     * Exports all the translation in DB
     * @return 
     */
    public function exportTranslations()
    {
        try {
            $appliedFilters = $this->getAppliedFiltersWithValues();

            if(isset($appliedFilters['deleted_at']) && (bool)$appliedFilters['deleted_at']){
                $translations = Translation::onlyTrashed()->select(self::$fieldsToExport)->get();
            }else{
                $translations = Translation::query()->select(self::$fieldsToExport)->get();
            }
            
            foreach ($appliedFilters ?? [] as $key => $value){
                if ($key == 'deleted_at')
                    continue;
                $translations = $translations->where($key, $value);
            }
            
            return Excel::download(new TranslationsExport($translations), 'translations_' . config('app.name') . '_' . Carbon::now()->timestamp . '.xlsx');
            
        } catch (\Exception|\Throwable $e) {
            logError($e->getMessage() . ' at line ' . $e->getLine());
            return abort(500);
        }
    }
    
    /**
     * Laravel Livewire Tables query builder
     *
     * @return Builder
     */
    public function builder() : Builder {
        return Translation::query();
    }

    /**
     * Laravel Livewire Tables columns builder
     *
     * @return array
     */
    public function configure() : void {
        HDatatable::applyTableConfigureDefaults($this);

        $this->setDefaultSort('namespace');
        $this->setAdditionalSelects(['id']);

        HDatatable::applyTableConfigureTable($this);

        HDatatable::applyTableConfigureHeader($this, [
            'namespace' => [
                'class' => 'col-md-1',
            ],
            'group' => [
                'class' => 'col-md-1',
            ],
            'item' => [
                'class' => 'col-3 col-md-2',
            ],
            'locale' => [
                'class' => 'col-2 col-md-1',
            ],
            'text' => [
                'class' => 'col-4 col-md-3',
            ],
            'actions' => [
                'class' => 'col-2 col-md-1 text-center',
            ],
        ]);
//
        HDatatable::applyTableConfigureColumn($this, [
            'actions' => fn($row) => [
                'class' => 'text-center',
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
//        $translationQuery = Translation::query();

        // Translations specific filters
        // FIXME: Filters will be disabled due to the big loading time (It's a package issue)
//        array_push($filters,
//            SelectFilter::make(__($this->prefix. 'generic.namespace'), 'namespace')
//            ->options(array_merge(['' => __('backend.datatable.filters.all')], $translationQuery
//                ->orderBy('namespace', 'asc')
//                ->get('namespace')
//                ->keyBy('namespace')
//                ->map(fn($trans) => $trans->namespace)
//                ->toArray()))
//            ->filter(function(Builder $builder, string $value) {
//                return $builder->where('namespace', $value);
//            }),
//            SelectFilter::make(__($this->prefix. 'generic.group'), 'group')
//                ->options(array_merge(['' => __('backend.datatable.filters.all')],$translationQuery
//                    ->orderBy('group', 'asc')
//                    ->get('group')
//                    ->keyBy('group')
//                    ->map(fn($trans) => $trans->group)
//                    ->toArray()))
//                ->filter(function(Builder $builder, string $value) {
//                    return $builder->where('group', $value);
//                }),
//            SelectFilter::make(__($this->prefix. 'generic.locale'), 'locale')
//                ->options(array_merge(['' => __('backend.datatable.filters.all')], Language::query()
//                    ->select('locale','name')
//                    ->orderBy('name', 'asc')
//                    ->get()
//                    ->keyBy('locale')
//                    ->map(fn($lang) => $lang->name)
//                    ->toArray()))
//                ->filter(function(Builder $builder, string $value) {
//                    return $builder->where('locale', $value);
//                })
//        );

        return $filters;
    }

    /**
     * Laravel Livewire Tables columns builder
     *
     * @return array
     */
    public function columns() : array {
        $columns = [
            Column::make(__($this->prefix.'generic.namespace'), 'namespace')
                ->sortable()
                ->searchable()
                ->collapseOnTablet(),
            Column::make(__($this->prefix.'generic.group'), 'group')
                ->sortable()
                ->searchable()
                ->collapseOnTablet(),
            Column::make(__($this->prefix.'generic.item'), 'item')
                ->sortable()
                ->searchable(),
            Column::make(__($this->prefix.'generic.locale'), 'locale')
                ->sortable()
                ->searchable(),
//                ->collapseOnTablet(),
            Column::make(__($this->prefix.'generic.text'), 'text')
                ->format(fn($value, $model, Column $column) => '<input type="text" class="form-control input-light-placeholder" placeholder="not translated" value= "' . $value . '" wire:change.lazy="updateTranslation(' . $model->id . ', $event.target.value)"' . '>')
                ->html()
                ->sortable()
                ->searchable(),
            Column::make('Action', 'action')
                ->label(
                    function ($model) {
                        $routes = [
                            'delete' => route('cms.translations.delete', ['id' => $model->id]),
                            'restore' => route('cms.translations.restore', ['id' => $model->id]),
                        ];
                        
                        return HDatatable::getTableActions($model, $routes, (bool)$this->getAppliedFilterWithValue('deleted_at'), ['delete']);
                    }
                )
                ->html()
        ];
        return $columns;
    }
    
}
