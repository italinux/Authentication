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
 * Drops database for current model.
 *
 * @package    symfony
 * @subpackage propel
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfPropelDropDbTask.class.php 24341 2010-11-24 15:01:58Z Kris.Wallsmith $
 */
class sfPropelDropDbTask extends sfPropelBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('database', sfCommandArgument::OPTIONAL | sfCommandArgument::IS_ARRAY, 'A specific database'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', true),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('no-confirmation', null, sfCommandOption::PARAMETER_NONE, 'Whether to force dropping of the database')
    ));

    $this->namespace = 'propel';
    $this->name = 'drop-db';
    $this->briefDescription = 'Drops database for current model';

    $this->detailedDescription = <<<EOF
The [propel:drop-db|INFO] task drops one or more databases based on
configuration in [config/databases.yml|COMMENT]:

  [./symfony propel:drop-db|INFO]

You will be prompted for confirmation before any databases are dropped unless
you provide the [--no-confirmation|COMMENT] option:

  [./symfony propel:drop-db --no-confirmation|INFO]

You can specify what databases to drop by providing their names:

  [./symfony propel:drop-db slave1 slave2|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);
    $databases = $this->getPropelDatabases($databaseManager, count($arguments['database']) ? $arguments['database'] : null);

    $environment = $this->configuration instanceof sfApplicationConfiguration ? $this->configuration->getEnvironment() : 'all';

    if (
      !$options['no-confirmation']
      &&
      !$this->askConfirmation(array_merge(
        array(sprintf('This command will remove all data in the following "%s" connection(s):', $environment), ''),
        array_map(create_function('$v', 'return \' - \'.$v;'), array_keys($databases)),
        array('', 'Are you sure you want to proceed? (y/N)')
      ), 'QUESTION_LARGE', false)
    )
    {
      $this->logSection('propel', 'task aborted');

      return 1;
    }

    foreach ($databases as $name => $database)
    {
      $this->logSection('propel', sprintf('Dropping "%s" database', $name));
      try
      {
        $database->getPropelConnection()->dropDatabase();
      }
      catch (Exception $e)
      {
        $this->logSection('propel', $e->getMessage(), null, 'ERROR');
      }
    }
  }
}
