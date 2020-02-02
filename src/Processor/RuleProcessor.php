<?php

namespace Dinh0012\Generators\Processor;

use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Relations\BelongsTo as EloquentBelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany as EloquentBelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany as EloquentHasMany;
use Illuminate\Database\Eloquent\Relations\HasOne as EloquentHasOne;
use Illuminate\Support\Str;
use Dinh0012\CodeGenerator\Model\DocBlockModel;
use Dinh0012\CodeGenerator\Model\MethodModel;
use Dinh0012\CodeGenerator\Model\VirtualPropertyModel;
use Dinh0012\Generators\Config;
use Dinh0012\Generators\Exception\GeneratorException;
use Dinh0012\Generators\Helper\EmgHelper;
use Dinh0012\Generators\Model\BelongsTo;
use Dinh0012\Generators\Model\BelongsToMany;
use Dinh0012\Generators\Model\EloquentModel;
use Dinh0012\Generators\Model\HasMany;
use Dinh0012\Generators\Model\HasOne;
use Dinh0012\Generators\Model\Relation;

/**
 * Class RelationProcessor
 * @package Dinh0012\Generators\Processor
 */
class RuleProcessor implements ProcessorInterface
{
    /**
     * @var DatabaseManager
     */
    protected $databaseManager;

    /**
     * @var EmgHelper
     */
    protected $helper;

    /**
     * FieldProcessor constructor.
     * @param DatabaseManager $databaseManager
     * @param EmgHelper $helper
     */
    public function __construct(DatabaseManager $databaseManager, EmgHelper $helper)
    {
        $this->databaseManager = $databaseManager;
        $this->helper = $helper;
    }

    /**
     * @inheritdoc
     */
    public function process(EloquentModel $model, Config $config)
    {
        $schemaManager = $this->databaseManager->connection($config->get('connection'))->getDoctrineSchemaManager();
        $prefix = $this->databaseManager->connection($config->get('connection'))->getTablePrefix();

        $columns = $schemaManager->listTableColumns($prefix . $model->getTableName());
        $columnRules = [];
        $type = Type::getTypeRegistry()->getMap();
        foreach ($columns as $columm) {
            $name = $columm->getName();
            $rule = array_search($columm->getType(), $type);
            $length = $columm->getLength();
            $isRequired = $columm->getNotnull();
            if ($columm->getAutoincrement() ||
                (in_array($rule, ['text', 'string']) && !$isRequired && !$length)) {
                continue;
            }

            $rule .= $length ? '|max:' . $length : '';
            $rule .= $isRequired ? '|required' : '' ;
            $columnRules[$name] = $rule;
        }

        $body = "return [\n\t\t\t";
        foreach ($columnRules as $columnRule => $rule) {
            $body .= sprintf('\'%s\'', addslashes($columnRule)) .
                ' => ' . sprintf('\'%s\'', addslashes($rule)) . ",\n\t\t\t";
        }
        $body .= "\n\t\t];";
        $method = new MethodModel('rules');
        $docBlock = '@return array';
        $method->setAccess('protected')
            ->setBody($body)
            ->setDocBlock(new DocBlockModel($docBlock));
        $model->addMethod($method);

    }
    public function getPriority()
    {
        return 5;
    }
}
