<?php

namespace Dinh0012\Generators\Processor;

use Illuminate\Database\DatabaseManager;
use Dinh0012\Generators\Config;
use Dinh0012\Generators\Exception\GeneratorException;
use Dinh0012\Generators\Model\EloquentModel;

/**
 * Class ExistenceCheckerProcessor
 * @package Dinh0012\Generators\Processor
 */
class ExistenceCheckerProcessor implements ProcessorInterface
{
    /**
     * @var DatabaseManager
     */
    protected $databaseManager;

    /**
     * ExistenceCheckerProcessor constructor.
     * @param DatabaseManager $databaseManager
     */
    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
    }

    /**
     * @inheritdoc
     */
    public function process(EloquentModel $model, Config $config)
    {
        $schemaManager = $this->databaseManager->connection($config->get('connection'))->getDoctrineSchemaManager();
        $prefix = $this->databaseManager->connection($config->get('connection'))->getTablePrefix();

        if (!$schemaManager->tablesExist($prefix . $model->getTableName())) {
            throw new GeneratorException(sprintf('Table %s does not exist', $prefix . $model->getTableName()));
        }
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return 8;
    }
}
