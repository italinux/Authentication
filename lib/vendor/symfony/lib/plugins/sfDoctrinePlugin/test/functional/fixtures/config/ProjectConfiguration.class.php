<?php

require_once dirname(__FILE__).'/../../../../../../autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    $this->enableAllPluginsExcept(array('sfPropelPlugin'));
  }

  public function initializePropel()
  {
    chdir(sfConfig::get('sf_root_dir'));

    $task = new sfPropelBuildTask($this->dispatcher, new sfFormatter());
    $task->setConfiguration($this);
    $task->run(array(), array(
      'no-confirmation' => true,
      'db'              => true,
      'model'           => true,
      'forms'           => true,
      'filters'         => true,
    ));
  }

  public function loadFixtures($fixtures)
  {
    $path = sfConfig::get('sf_data_dir') . '/' . $fixtures;
    if ( ! file_exists($path)) {
      throw new sfException('Invalid data fixtures file');
    }
    chdir(sfConfig::get('sf_root_dir'));
    $task = new sfPropelDataLoadTask($this->dispatcher, new sfFormatter());
    $task->setConfiguration($this);
    $task->run(array($path));
  }

  public function configurePropel(Propel_Manager $manager)
  {
    $manager->setAttribute(Propel::ATTR_VALIDATE, true);

    $options = array('baseClassName' => 'myPropelRecord');
    sfConfig::set('propel_model_builder_options', $options);
  }

  public function configurePropelConnection(Propel_Connection $connection)
  {
  }

  public function configurePropelConnectionPropel2(Propel_Connection $connection)
  {
    $connection->setAttribute(Propel::ATTR_VALIDATE, false);
  }
}
