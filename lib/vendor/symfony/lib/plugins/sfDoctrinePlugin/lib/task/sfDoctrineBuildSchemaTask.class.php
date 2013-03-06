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
 * Creates a schema.yml from an existing database.
 *
 * @package    symfony
 * @subpackage propel
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfPropelBuildSchemaTask.class.php 23922 2010-11-14 14:58:38Z fabien $
 */
class sfPropelBuildSchemaTask extends sfPropelBaseTask
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
    $this->name = 'build-schema';
    $this->briefDescription = 'Creates a schema from an existing database';

    $this->detailedDescription = <<<EOF
The [propel:build-schema|INFO] task introspects a database to create a schema:

  [./symfony propel:build-schema|INFO]

The task creates a yml file in [config/propel|COMMENT]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->logSection('propel', 'generating yaml schema from database');

    $databaseManager = new sfDatabaseManager($this->configuration);
    $this->callPropelCli('generate-yaml-db');
  }
}