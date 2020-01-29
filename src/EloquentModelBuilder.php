<?php

namespace Dinh0012\Generators;

use Dinh0012\Generators\Exception\GeneratorException;
use Dinh0012\Generators\Model\EloquentModel;
use Dinh0012\Generators\Processor\ProcessorInterface;

/**
 * Class EloquentModelBuilder
 * @package Dinh0012\Generators
 */
class EloquentModelBuilder
{
    /**
     * @var ProcessorInterface[]
     */
    protected $processors;

    /**
     * EloquentModelBuilder constructor.
     * @param ProcessorInterface[]|\IteratorAggregate $processors
     */
    public function __construct($processors)
    {
        if ($processors instanceof \IteratorAggregate) {
            $this->processors = iterator_to_array($processors);
        } else {
            $this->processors = $processors;
        }
    }

    /**
     * @param Config $config
     * @return EloquentModel
     * @throws GeneratorException
     */
    public function createModel(Config $config)
    {
        $model = new EloquentModel();

        $this->prepareProcessors();

        foreach ($this->processors as $processor) {
            $processor->process($model, $config);
        }

        return $model;
    }

    /**
     * Sort processors by priority
     */
    protected function prepareProcessors()
    {
        usort($this->processors, function (ProcessorInterface $one, ProcessorInterface $two) {
            if ($one->getPriority() == $two->getPriority()) {
                return 0;
            }

            return $one->getPriority() < $two->getPriority() ? 1 : -1;
        });
    }
}
