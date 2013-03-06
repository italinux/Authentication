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
 * Create SQL for the current model.
 *
 * @package    symfony
 * @subpackage propel
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfPropelBuildSqlTask.class.php 23922 2010-11-14 14:58:38Z fabien $
 */
class sfPropelBuildSqlTask extends sfPropelBaseTask
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
    $this->name = 'build-sql';
    $this->briefDescription = 'Creates SQL for the current model';

    $this->detailedDescription = <<<EOF
The [propel:build-sql|INFO] task creates SQL statements for table creation:

  [./symfony propel:build-sql|INFO]

The generated SQL is optimized for the database configured in [config/databases.yml|COMMENT]:

  [propel.database = mysql|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->logSection('propel', 'generating sql for models');

    $path = sfConfig::get('sf_data_dir').'/sql';
    if (!is_dir($path)) {
      $this->getFilesystem()->mkdirs($path);
    }

    $databaseManager = new sfDatabaseManager($this->configuration);
    $this->callPropelCli('generate-sql');
  }
}