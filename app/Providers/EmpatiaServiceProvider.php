<?php

namespace App\Providers;

use App\Helpers\Empatia\Cbs\TopicHelpers;
use App\Http\Controllers\Backend\Empatia\Cbs\Livewire\Cbs\TopicAnalyticsCharts;
use App\Http\Controllers\Backend\Empatia\Cbs\Livewire\Cbs\TopicAnalyticsTables;
use App\Http\Controllers\Backend\Empatia\Cbs\Livewire\Cbs\TopicProponents;
use App\Http\Livewire\Backend\Empatia\Cbs\OperationSchedulesTable;
use App\Http\Livewire\Backend\Empatia\Cbs\TechnicalAnalysisQuestionsTable;
use App\Http\Livewire\Backend\Empatia\Cbs\TopicStatus;
use App\Http\Livewire\Backend\Empatia\LoginLevelsTable;
use App\Http\Livewire\Backend\Statistics;
use App\Http\Livewire\Frontend\Empatia\Cbs\TopicsList;
use App\Listeners\Empatia\Frontend\TopicEventSubscriber;
use App\Listeners\Empatia\Frontend\UserEventSubscriber;
use App\Models\Empatia\Cbs\OperationSchedule;
use App\Models\Empatia\Cbs\TechnicalAnalysisQuestion;
use App\Policies\Backend\Empatia\Cbs\OperationSchedulesPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use App\Models\Empatia\LoginLevel;
use App\Policies\Backend\Empatia\Cbs\TechnicalAnalysisQuestionPolicy;
use App\Policies\Backend\Empatia\LoginLevelPolicy;


class EmpatiaServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        OperationSchedule::class => OperationSchedulesPolicy::class,
        LoginLevel::class => LoginLevelPolicy::class,
        TechnicalAnalysisQuestion::class => TechnicalAnalysisQuestionPolicy::class
    ];

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        if( config('one.framework_variant') != 'empatia' ){
            return;
        }

        $this->registerPolicies();

        /**   Backend   **/
//        // CBS Components
        Livewire::component('technical-analysis-table', TechnicalAnalysisQuestionsTable::class);
//        Livewire::component('cbs-table', CbsController::class);
//        Livewire::component('cb-parameters', CbParameters::class);
//        Livewire::component('cb-analytics', CbAnalytics::class);
//        Livewire::component('cb-versions-modal', CbVersionsModal::class);
//        Livewire::component(TopicsController::$component_name, TopicsController::class);
//        Livewire::component('topic-manager', TopicManager::class);
//        Livewire::component('topic-versions-modal', TopicVersionsModal::class);
        Livewire::component('topic-status', TopicStatus::class);
//        Livewire::component('topic-status-modal', TopicStatusModal::class);
        Livewire::component('topic-proponents', TopicProponents::class);
//        Livewire::component('topic-proponents', TopicProponents::class);
//        Livewire::component('topic-analytics-tables', TopicAnalyticsTables::class);
//        Livewire::component('topic-analytics-charts', TopicAnalyticsCharts::class);
//        Livewire::component('events', Events::class);
//        Livewire::component('analytics', Analytics::class);
//        Livewire::component('configurations', Configurations::class);
//        Livewire::component('topics-table', TopicsController::class);
//        Livewire::component('cb-phases', CbPhase::class);
          Livewire::component('statistics', Statistics::class);
        Livewire::component('operation-schedules-table', OperationSchedulesTable::class);
//
        // Login Levels Components
        Livewire::component('login-levels-table', LoginLevelsTable::class);
//
//        // Operation Schedules Components
//        Livewire::component('operation-schedules-table', OperationSchedulesController::class);

        /**   Frontend   **/
        Livewire::component('fe-topics-list', TopicsList::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);

        \App::bind('topic-helpers',function() {
            return new TopicHelpers;
        });
    }

    /**
     * Register event listeners.
     *
     * @return void
     */
    public static function registerListeners()
    {
        Event::subscribe( TopicEventSubscriber::class );
        Event::subscribe( UserEventSubscriber::class );
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
