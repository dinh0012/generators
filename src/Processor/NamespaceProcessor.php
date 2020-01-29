<?php

namespace Dinh0012\Generators\Processor;

use Krlove\CodeGenerator\Model\NamespaceModel;
use Dinh0012\Generators\Config;
use Dinh0012\Generators\Model\EloquentModel;

/**
 * Class NamespaceProcessor
 * @package Dinh0012\Generators\Processor
 */
class NamespaceProcessor implements ProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function process(EloquentModel $model, Config $config)
    {
        $model->setNamespace(new NamespaceModel($config->get('namespace')));
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return 6;
    }
}
