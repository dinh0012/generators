<?php

namespace Dinh0012\Generators\Model;

use Dinh0012\CodeGenerator\Model\ClassModel;

/**
 * Class EloquentModel
 * @package Dinh0012\Generators\Model
 */
class EloquentModel extends ClassModel
{
    /**
     * @var string
     */
    protected $tableName;

    /**
     * @param string $tableName
     *
     * @return $this
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }
}
