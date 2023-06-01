<?php

namespace App\Providers;

use App\Helpers\HBackend;
use App\Http\Controllers\Backend\CMS\ContentsController;
use App\Http\Controllers\Backend\CMS\LanguagesController;
use App\Http\Controllers\Backend\CMS\TranslationsController;
use App\Http\Controllers\Backend\ConfigurationsController;
use App\Http\Controllers\Backend\Empatia\Cbs\CbsController;
use App\Http\Controllers\Backend\Empatia\Cbs\TopicsController;
use App\Http\Controllers\Backend\Notifications\EmailsController;
use App\Http\Controllers\Backend\Notifications\SmsController;
use App\Http\Controllers\Backend\Notifications\TemplatesController;
use App\Http\Controllers\Backend\Empatia\LoginLevelsController;
use App\Http\Livewire\Backend\CMS\Content\ContentsTable;
use App\Http\Livewire\Backend\CMS\File\FileInput;
use App\Http\Livewire\Backend\CMS\File\FilePreview;
use App\Http\Livewire\Backend\CMS\File\FileUpload;
use App\Http\Livewire\Backend\CMS\Languages\LanguagesTable;
use App\Http\Livewire\Backend\CMS\Menu\MenuForm;
use App\Http\Livewire\Backend\CMS\Menu\MenuList;
use App\Http\Livewire\Backend\CMS\Translation\TranslationsImport;
use App\Http\Livewire\Backend\CMS\Translation\TranslationsTable;
use App\Http\Livewire\Backend\ConfigurationsTable;
use App\Http\Livewire\Backend\Empatia\Cbs\CbsTable;
use App\Http\Livewire\Backend\Empatia\Cbs\TopicsTable;
use App\Http\Livewire\Backend\JsonTable;
use App\Http\Livewire\Backend\Notifications\EmailsTable;
use App\Http\Livewire\Backend\Notifications\SmsTable;
use App\Http\Livewire\Backend\Notifications\TemplatesTable;
use App\Http\Livewire\Frontend\Users\Users;
use App\Http\Middleware\PermissionMiddleware;
use App\Http\Middleware\RoleOrPermissionMiddleware;
use App\Rules\Coordinates;
use App\Rules\Nif;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Vizir\KeycloakWebGuard\Middleware\KeycloakCan;

class AppServiceProvider extends ServiceProvider
{
    protected $customValidationRules = [
        Nif::class,
        Coordinates::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Custom Translator override
        $this->app->extend(\Illuminate\Translation\Translator::class, function ($translator) {
            return new \App\Http\Controllers\Backend\CMS\CustomTranslator($translator->getLoader(), $translator->getLocale());
        });

        // Add Middleware "keycloak-web-can"
        $this->app['router']->aliasMiddleware('role', KeycloakCan::class);
        $this->app['router']->aliasMiddleware('permission', PermissionMiddleware::class);
        $this->app['router']->aliasMiddleware('role_or_permission', RoleOrPermissionMiddleware::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**   Request Macros   **/
        Request::macro('environment', function () {
            return HBackend::getEnvironment($this);
        });
        Request::macro('isBackend', function () {
            return HBackend::isBackend($this);
        });
        Request::macro('isFrontend', function () {
            return HBackend::isFrontend($this);
        });

        /*****************************
         * Livewire Table Components
         * */
        Livewire::component('contents-table', ContentsTable::class);
        Livewire::component('configurations-table', ConfigurationsTable::class);
        Livewire::component('translations-table', TranslationsTable::class);
        Livewire::component('languages-table', LanguagesTable::class);
        Livewire::component('json-table', JsonTable::class);
        Livewire::component('emails-table', EmailsTable::class);
        Livewire::component('sms-table', SmsTable::class);
        Livewire::component('templates-table', TemplatesTable::class);
        Livewire::component('cbs-table', CbsTable::class);
        Livewire::component('topics-table', TopicsTable::class);

        /*****************************
         * Livewire Components
         * */
        Livewire::component('file-input', FileInput::class);
        Livewire::component('file-upload', FileUpload::class);
        Livewire::component('file-preview', FilePreview::class);
        Livewire::component('translations-import', TranslationsImport::class);
        Livewire::component('menu-list', MenuList::class);
        Livewire::component('menu-form', MenuForm::class);


        /**   Empatia Livewire Components   **/
        Livewire::component('user-profile', Users::class);

        /**   Migration Macros   **/
        /*  Timestamps  */
        Blueprint::macro('blamestamps', function () {
            $this->timestamp('created_at')->nullable();
            $this->timestamp('updated_at')->nullable();
            $this->softDeletes();
            $this->integer('created_by')->unsigned()->nullable();
            $this->integer('updated_by')->unsigned()->nullable();
            $this->integer('deleted_by')->unsigned()->nullable();
        });
        Blueprint::macro('dropBlamestamps', function () {
            $this->dropColumn('created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by', 'deleted_at');
        });

        /*  Versions  */
        Blueprint::macro('versionable', function () {
            $this->integer('version')->unsigned()->default(1)->nullable();
            $this->json('versions')->nullable();
        });
        Blueprint::macro('dropVersionable', function () {
            $this->dropColumn('version', 'versions');
        });

        /**   Validation Rules   **/
        /*  add custom rule names to validator (to be used by string)  */
        foreach ($this->customValidationRules as $rule) {
            $this->app['validator']->extend(Str::snake(class_basename($rule)), function () use ($rule) {
                return (new $rule())->passes(...func_get_args());
            });
        }

    }
}
