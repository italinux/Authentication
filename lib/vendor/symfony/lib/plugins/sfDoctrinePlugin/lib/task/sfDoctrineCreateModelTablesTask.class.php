<?php

/*
 * This file is part of the symfony package.
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/sfPropelBaseTask.class.php');

/**
 * Create tables for specified list of models
 *
 * @package    symfony
 * @subpackage propel
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfPropelCreateModelTablesTask.class.php 23922 2010-11-14 14:58:38Z fabien $
 */
class sfPropelCreateModelTables extends sfPropelBaseTask
{
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('models', sfCommandArgument::IS_ARRAY, 'The list of models', array()),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', 'frontend'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
    ));

    $this->namespace = 'propel';
    $this->name = 'create-model-tables';
    $this->briefDescription = 'Drop and recreate tables for specified models.';

    $this->detailedDescription = <<<EOF
The [propel:create-model-tables|INFO] Drop and recreate tables for specified models:

  [./symfony propel:create-model-tables User|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);

    $buildModel = new sfPropelBuildModelTask($this->dispatcher, $this->formatter);
    $buildModel->setCommandApplication($this->commandApplication);
    $buildModel->setConfiguration($this->configuration);
    $ret = $buildModel->run();

    $connections = array();
    $models = $arguments['models'];
    foreach ($models as $key => $model)
    {
      $model = trim($model);
      $conn = Propel_Core::getTable($model)->getConnection();
      $connections[$conn->getName()][] = $model;
    }

    foreach ($connections as $name => $models)
    {
      $this->logSection('propel', 'dropping model tables for connection "'.$name.'"');

      $conn = Propel_Manager::getInstance()->getConnection($name);
      $models = $conn->unitOfWork->buildFlushTree($models);
      $models = array_reverse($models);

      foreach ($models as $model)
      {
        $tableName = Propel_Core::getTable($model)->getOption('tableName');

        $this->logSection('propel', 'dropping table "'.$tableName.'"');

        try {
          $conn->export->dropTable($tableName);
        }
        catch (Exception $e)
        {
          $this->logSection('propel', 'dropping table failed: '.$e->getMessage());
        }
      }

      $this->logSection('propel', 'recreating tables for models');

      Propel_Core::createTablesFromArray($models);
    }
  }
}