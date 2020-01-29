<?php

namespace Dinh0012\Generators\Processor;

use Dinh0012\CodeGenerator\Model\DocBlockModel;
use Dinh0012\CodeGenerator\Model\PropertyModel;
use Dinh0012\Generators\Config;
use Dinh0012\Generators\Model\EloquentModel;

/**
 * Class CustomPropertyProcessor
 * @package Dinh0012\Generators\Processor
 */
class CustomPropertyProcessor implements ProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function process(EloquentModel $model, Config $config)
    {
        if ($config->get('no_timestamps') === true) {
            $pNoTimestamps = new PropertyModel('timestamps', 'public', false);
            $pNoTimestamps->setDocBlock(
                new DocBlockModel('Indicates if the model should be timestamped.', '', '@var bool')
            );
            $model->addProperty($pNoTimestamps);
        }

        if ($config->has('date_format')) {
            $pDateFormat = new PropertyModel('dateFormat', 'protected', $config->get('date_format'));
            $pDateFormat->setDocBlock(
                new DocBlockModel('The storage format of the model\'s date columns.', '', '@var string')
            );
            $model->addProperty($pDateFormat);
        }

        if ($config->has('connection')) {
            $pConnection = new PropertyModel('connection', 'protected', $config->get('connection'));
            $pConnection->setDocBlock(
                new DocBlockModel('The connection name for the model.', '', '@var string')
            );
            $model->addProperty($pConnection);
        }
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return 5;
    }
}
