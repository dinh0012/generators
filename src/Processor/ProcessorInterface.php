<?php

namespace Dinh0012\Generators\Processor;

use Dinh0012\Generators\Config;
use Dinh0012\Generators\Model\EloquentModel;

/**
 * Interface ProcessorInterface
 * @package Dinh0012\Generators\Processor
 */
interface ProcessorInterface
{
    /**
     * @param EloquentModel $model
     * @param Config $config
     */
    public function process(EloquentModel $model, Config $config);

    /**
     * @return int
     */
    public function getPriority();
}
