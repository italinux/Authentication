<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/sfPropelBaseTask.class.php');

/**
 * Generate migrations from database
 *
 * @package    symfony
 * @subpackage propel
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfPropelGenerateMigrationsDbTask.class.php 23922 2010-11-14 14:58:38Z fabien $
 */
class sfPropelGenerateMigrationsDbTask extends sfPropelBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', true),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
    ));

    $this->namespace = 'propel';
    $this->name = 'generate-migrations-db';
    $this->briefDescription = 'Generate migration classes from existing database connections';

    $this->detailedDescription = <<<EOF
The [propel:generate-migrations-db|INFO] task generates migration classes from
existing database connections:

  [./symfony propel:generate-migrations-db|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);
    $config = $this->getCliConfig();

    $this->logSection('propel', 'generating migration classes from database');

    if (!is_dir($config['migrations_path']))
    {
      $this->getFilesystem()->mkdirs($config['migrations_path']);
    }

    $this->callPropelCli('generate-migrations-db', array(
      'yaml_schema_path' => $this->prepareSchemaFile($config['yaml_schema_path']),
    ));
  }
}