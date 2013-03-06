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
 * Inserts SQL for current model.
 *
 * @package    symfony
 * @subpackage propel
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfPropelInsertSqlTask.class.php 27942 2010-02-12 14:05:53Z Kris.Wallsmith $
 */
class sfPropelInsertSqlTask extends sfPropelBaseTask
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
    $this->name = 'insert-sql';
    $this->briefDescription = 'Inserts SQL for current model';

    $this->detailedDescription = <<<EOF
The [propel:insert-sql|INFO] task creates database tables:

  [./symfony propel:insert-sql|INFO]

The task connects to the database and creates tables for all the
[lib/model/propel/*.class.php|COMMENT] files.
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->logSection('propel', 'creating tables');

    $databaseManager = new sfDatabaseManager($this->configuration);
    $config = $this->getCliConfig();

    Propel_Core::loadModels($config['models_path'], Propel_Core::MODEL_LOADING_CONSERVATIVE);
    Propel_Core::createTablesFromArray(Propel_Core::getLoadedModels());

    $this->logSection('propel', 'created tables successfully');
  }
}
