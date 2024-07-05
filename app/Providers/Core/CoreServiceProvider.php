<?php

namespace App\Providers\Core;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use App\Facades\Core\Core as CoreFacade;
use App\Modules\Core;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {
        include __DIR__ . '/../../Helpers/Core/helpers.php';

        // $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'core');

        // $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'core');
        $this->publishes([
            dirname(__DIR__) . '../../../config/concord.php' => config_path('concord.php'),
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();

        $this->registerFacades();
    }

    /**
     * Register Bouncer as a singleton.
     *
     * @return void
     */
    protected function registerFacades()
    {
        $loader = AliasLoader::getInstance();

        $loader->alias('core', CoreFacade::class);

        $this->app->singleton('core', function () {
            return app()->make(Core::class);
        });
    }

    /**
     * Register the console commands of this package
     *
     * @return void
     */
    protected function registerCommands(): void
    {
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        // $this->mergeConfigFrom(dirname(__DIR__) . '/Config/acl.php', 'acl');

        // $this->mergeConfigFrom(dirname(__DIR__) . '/Config/menu.php', 'menu.admin');

        // $this->mergeConfigFrom(dirname(__DIR__) . '/Config/core_config.php', 'core_config');

        // $this->mergeConfigFrom(dirname(__DIR__) . '/Config/dashboard_cards.php', 'dashboard_cards');

        $this->mergeConfigFrom(dirname(__DIR__) . '../../../config/attribute_lookups.php', 'attribute_lookups');

        $this->mergeConfigFrom(dirname(__DIR__) . '../../../config/attribute_entity_types.php', 'attribute_entity_types');
    }
}
