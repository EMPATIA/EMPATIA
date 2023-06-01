<?php

namespace App\Http\Livewire\Frontend\Empatia\Cbs;

use App\Helpers\HBackend;
use App\Helpers\HFrontend;
use App\Models\Empatia\Cbs\Cb;
use App\Objects\Empatia\CbParameter;
use Illuminate\Support\Collection;
use Livewire\Component;

class TopicsList extends Component
{
    const DEFAULT_VIEW = 'frontend.livewire.cbs.topics-list';

    public ?Cb $cb;
    public ?Collection $topics;

    /*  View Settings  */
    public string $projectPath;
    public string $view;
    public string $filtersView;
    public string $topicsView;

    private mixed $filterParameters;

    public array $preselectedFilters = [];
    public array $filters = [];
    public string $sorting;
    public string $topicState = '';
    public string $search;

    public array $sortingOptions = [
        'winner' => 'state'
    ];


    public function mount()
    {
        $this->cb = !empty($this->cb) ? $this->cb : null;
        $this->topics = $this->cb?->topicsByWinningStatus() ?? null;

        $this->initViewSettings();
        $this->initFilters();

        $this->applyFilters();
        $this->applySortings();
    }

    public function render()
    {
        return view('livewire.frontend.empatia.cbs.topics-list');
    }

    public function updatedFilters($value, $name) : void
    {
        $this->applyFilters();
        $this->applySortings();
    }

    public function updatedSearch($value, $name) : void
    {
        $this->applyFilters();
        $this->applySortings();
    }

    public function updatedTopicState($value, $name) : void
    {
        $this->applyFilters();
        $this->applySortings();
    }

    public function updatedSorting($value, $name) : void
    {
        $this->applyFilters();
        $this->applySortings();
    }

    public function resetFilters() : void
    {
        $this->loadFilterParameters();

        foreach ($this->filterParameters as $parameter) {
            $code = data_get($parameter, 'code');
            if( empty($code) ){
                continue;
            }
            $this->filters[$code] = $this->preselectedFilters[$code] ?? [];
        }
        $this->topicState = $this->cb?->hasEnded() ? 'approved' : '';
        $this->search = '';

        $this->resetList();
    }

    public function resetList() : void
    {
        $this->topics = $this->cb->topicsByWinningStatus();
    }

    public function applyFilters() : void
    {
        if( empty($this->cb) ) return;

        $this->loadFilterParameters();
        $this->resetList();

        foreach ($this->filterParameters as $parameter) {
            if( empty($this->filters[$parameter->code]) ) continue;

            if( in_array($parameter->type ?? '', ['select', 'checkbox']) ){
                $this->topics = $this->topics?->filter(function($item) use($parameter){
                    if( $parameter->isFilterMultiple() ){
                        $condition = $parameter->isFilterCumulative();
                        foreach ($this->filters[$parameter->code] as $option) {
                            $parameterCondition = in_array($option, (array)$item->parameter($parameter->code) ?? []);
                            $condition = $parameter->isFilterCumulative() ? ($condition && $parameterCondition) : ($condition || $parameterCondition);
                        }
                        return $condition;
                    } else {
                        return in_array($this->filters[$parameter->code], (array)$item->parameter($parameter->code) ?? []);
                    }
                });
            } else {
                $this->topics = $this->topics?->where("parameters.$parameter->code", '=', $this->filters[$parameter->code]);
            }
        }

        if( !empty($this->search) ){
            $this->topics = $this->topics?->filter(function($item){
                return stripos(data_lang_get($item, 'title', '', true),$this->search) !== false;
            });
        };

        if( !empty($this->topicState) ){
            $this->topics = $this->topics?->where('state', $this->topicState);
        };

        if( !empty($this->search) ){
            $this->topics = $this->topics?->filter(function($item){
                return stripos(data_lang_get($item, 'title', '', true),$this->search) !== false;
            });
        };

    }

    public function applySortings() : void
    {
        if( empty($this->cb) ) return;

        $this->loadFilterParameters();

        $sortField = data_get($this->sortingOptions ?? [], $this->sorting ?? '');

        if( !empty($sortField) ){
            $this->topics = $this->topics?->sortBy($sortField);
        };

    }

    /**   INITIALIZATION FUNCTIONS   **/

    private function initViewSettings(): void
    {
        $this->projectPath  = $this->projectPath ?? HFrontend::getProjectPath(true);
        $this->view         = $this->view ?? self::DEFAULT_VIEW;
        $this->filtersView  = $this->filtersView ?? "frontend.$this->projectPath.cbs.{$this->cb->type}.cb.partials.topics-grid-filters";
        $this->topicsView   = $this->topicsView ?? "frontend.$this->projectPath.cbs.{$this->cb->type}.cb.partials.topics-grid";
    }

    private function initFilters(): void
    {
        if( empty($this->cb) ) return;

        $this->loadFilterParameters();

        // save preselected filters
        if( !empty($this->filters) ){
            $this->preselectedFilters = $this->filters;
        }

        $this->resetFilters();
    }

    private function loadFilterParameters() : void
    {
        if( empty($this->cb) ) return;

        // get parameters from cb
        $this->filterParameters = $this->cb->getParameters([
            'flags.use_as_filter' => true,
        ]);
    }
}
