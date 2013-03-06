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
 * Generate migration classes by producing a diff between your old and new schema.
 *
 * @package    symfony
 * @subpackage propel
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfPropelGenerateMigrationsDiffTask.class.php 28871 2010-03-29 17:28:03Z Jonathan.Wage $
 */
class sfPropelGenerateMigrationsDiffTask extends sfPropelBaseTask
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
    $this->name = 'generate-migrations-diff';
    $this->briefDescription = 'Generate migration classes by producing a diff between your old and new schema.';

    $this->detailedDescription = <<<EOF
The [propel:generate-migrations-diff|INFO] task generates migration classes by
producing a diff between your old and new schema.

  [./symfony propel:generate-migrations-diff|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);
    $config = $this->getCliConfig();

    $this->logSection('propel', 'generating migration diff');

    if (!is_dir($config['migrations_path']))
    {
      $this->getFilesystem()->mkdirs($config['migrations_path']);
    }

    spl_autoload_register(array('Propel_Core', 'modelsAutoload'));

    $this->callPropelCli('generate-migrations-diff', array(
      'yaml_schema_path' => $this->prepareSchemaFile($config['yaml_schema_path']),
    ));
  }
}
