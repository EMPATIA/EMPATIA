<?php

namespace App\Providers;

use App\Helpers\HBackend;
use App\Models\Backend\Notifications\Template;
use App\Models\Empatia\Cbs\TechnicalAnalysisQuestion;
use App\Policies\Backend\Empatia\Cbs\TechnicalAnalysisQuestionPolicy;
use App\Policies\Backend\Notifications\TemplatePolicy;
use App\Models\Empatia\LoginLevel;
use App\Policies\Backend\Empatia\LoginLevelPolicy;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Template::class => TemplatePolicy::class,
        LoginLevel::class => LoginLevelPolicy::class,
        TechnicalAnalysisQuestion::class => TechnicalAnalysisQuestionPolicy::class
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('permissions', function () {
            return json_decode(json_encode(HBackend::getConfigurationByCode('permissions')), true);
        });
    }

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::extend('keycloak-web', function ($app, $name, array $config) {
            $provider = Auth::createUserProvider($config['provider']);
            return new KeycloakWebGuard($provider, $app->request);
        });
    }
}
