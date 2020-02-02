<?php

namespace Dinh0012\Generators\Commands;

use Illuminate\Config\Repository as AppConfig;
use Illuminate\Console\Command;
use Dinh0012\Generators\Config;
use Dinh0012\Generators\Generator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use DB;
use Str;

/**
 * Class Model
 * @package Dinh0012\Generators\Commands
 */
class Model extends Command
{
    /**
     * @var string
     */
    protected $name = 'generate:model';

    /**
     * @var Generator
     */
    protected $generator;

    /**
     * @var AppConfig
     */

    protected $appConfig;

    /**
     * @var \Doctrine\DBAL\Schema\AbstractSchemaManager
     */
    private $databaseConnection;

    /**
     * @var Config
     */
    private $config;

    /**
     * GenerateModelCommand constructor.
     * @param Generator $generator
     * @param AppConfig $appConfig
     */
    public function __construct(Generator $generator, AppConfig $appConfig)
    {
        parent::__construct();

        $this->generator = $generator;
        $this->appConfig = $appConfig;

    }

    /**
     * Executes the command
     */
    public function fire()
    {
        $this->config = $this->createConfig();
        $config = $this->config;
        $connection = $config->get('connection');
        $this->databaseConnection = $connection ? DB::connection($connection)->getDoctrineSchemaManager() :
            DB::connection()->getDoctrineSchemaManager();

       $this->generateModel();

    }

    private function generateModel()
    {
        $config = $this->config;
        $tableInput = $config->get('table');
        $singular = $config->get('singular', true);
        $tables = [];
        if ($tableInput === '*' || !$tableInput || $tableInput == 'all') {
            $tables = $this->getAllTables();
        } else {
            $tables = explode(',', $tableInput);
        }
        foreach ($tables as $table) {
            $config->set('table', $table);
            $className = str_replace('"', '', $table);
            $className = ucfirst(Str::studly($className));
            if ($singular) {
                $className = Str::singular($className);
            }

            $config->set('class_name', $className);
            $model = $this->generator->generateModel($config);
            $this->output->writeln(sprintf('Model %s generated', $model->getName()->getName()));
        }
    }

    private function getAllTables()
    {
        $tables = collect($this->databaseConnection->listTableNames())->flatten();

        $tables = $tables->map(function ($value, $key) {
            return collect($value)->flatten()[0];
        })->reject(function ($value, $key) {
            return $value == 'migrations';
        });

        return $tables;
    }

    /**
     * Add support for Laravel 5.5
     */
    public function handle()
    {
        $this->fire();
    }

    /**
     * @return Config
     */
    protected function createConfig()
    {
        $config = [];

        foreach ($this->getArguments() as $argument) {
            $config[$argument[0]] = $this->argument($argument[0]);
        }
        foreach ($this->getOptions() as $option) {
            $value = $this->option($option[0]);
            if ($option[2] == InputOption::VALUE_NONE && $value === false) {
                $value = null;
            }
            $config[$option[0]] = $value;
        }

        $config['db_types'] = $this->appConfig->get('eloquent_model_generator.db_types');

        return new Config($config, $this->appConfig->get('eloquent_model_generator.model_defaults'));
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['table', 'tn', InputOption::VALUE_OPTIONAL, 'Name of the table to use', null],
            ['output-path', 'op', InputOption::VALUE_OPTIONAL, 'Directory to store generated model', null],
            ['namespace', 'ns', InputOption::VALUE_OPTIONAL, 'Namespace of the model', null],
            ['base-class-name', 'bc', InputOption::VALUE_OPTIONAL, 'Model parent class', null],
            ['no-timestamps', 'ts', InputOption::VALUE_NONE, 'Set timestamps property to false', null],
            ['date-format', 'df', InputOption::VALUE_OPTIONAL, 'dateFormat property', null],
            ['connection', 'cn', InputOption::VALUE_OPTIONAL, 'Connection property', null],
            ['backup', 'b', InputOption::VALUE_NONE, 'Backup existing model', null]
        ];
    }
}
