<?php

namespace Dinh0012\Generators;

/*
 *
 * @author Dinhnv <dinh020304@gmail.com>
 */

use Illuminate\Support\ServiceProvider;

class GeneratorsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot()
    {
        //
    }

    public function register()
    {
        $this->registerModelGenerator();
    }

    private function registerModelGenerator()
    {
        $this->app->singleton('command.dinh0012.generate', function ($app) {
            return $app['Dinh0012\Generators\Commands\Model'];
        });

        $this->commands('command.dinh0012.generate');
    }
}
