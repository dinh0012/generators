<?php

namespace Dinh0012\Generators\Provider;

use Illuminate\Support\ServiceProvider;
use Dinh0012\Generators\Commands\Model;
use Dinh0012\Generators\EloquentModelBuilder;
use Dinh0012\Generators\Processor\CustomPrimaryKeyProcessor;
use Dinh0012\Generators\Processor\CustomPropertyProcessor;
use Dinh0012\Generators\Processor\ExistenceCheckerProcessor;
use Dinh0012\Generators\Processor\FieldProcessor;
use Dinh0012\Generators\Processor\NamespaceProcessor;
use Dinh0012\Generators\Processor\RelationProcessor;
use Dinh0012\Generators\Processor\TableNameProcessor;

/**
 * Class GeneratorServiceProvider
 * @package Dinh0012\Generators\Provider
 */
class GeneratorServiceProvider extends ServiceProvider
{
    const PROCESSOR_TAG = 'eloquent_model_generator.processor';

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->commands([
            Model::class,
        ]);

        $this->app->tag([
            ExistenceCheckerProcessor::class,
            FieldProcessor::class,
            NamespaceProcessor::class,
            RelationProcessor::class,
            CustomPropertyProcessor::class,
            TableNameProcessor::class,
            CustomPrimaryKeyProcessor::class,
        ], self::PROCESSOR_TAG);

        $this->app->bind(EloquentModelBuilder::class, function ($app) {
            return new EloquentModelBuilder($app->tagged(self::PROCESSOR_TAG));
        });
    }
}
