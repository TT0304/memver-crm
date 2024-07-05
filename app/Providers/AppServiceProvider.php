<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $loader = AliasLoader::getInstance();

        $loader->alias('Bouncer', \App\Facades\Core\Bouncer::class);
        // $loader->alias('Menu', \App\Facades\Menu::class);

        $this->app->singleton('bouncer', function () {
            return new \App\Core\Bouncer();
        });

        // $this->app->singleton('menu', function () {
        //     return new \App\Menu();
        // });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Router $router): void
    {
        Schema::defaultStringLength(191);
        Relation::morphMap([
            'leads'         => 'App\DataStructure\Lead\Models\Lead',
            'products'      => 'App\DataStructure\Product\Models\Product',
            'persons'       => 'App\DataStructure\Contact\Models\Person',
            'organizations' => 'App\DataStructure\Contact\Models\Organization',
            'quotes'        => 'App\DataStructure\Quote\Models\Quote',
            'member'        => 'App\DataStructure\Lead\Models\Member',
            'user'          => 'App\DataStructure\User\Models\User',
        ]);
        $router->aliasMiddleware('web', \App\Http\Middleware\Bouncer::class);
        $this->app->register(EventServiceProvider::class);
    }
}
