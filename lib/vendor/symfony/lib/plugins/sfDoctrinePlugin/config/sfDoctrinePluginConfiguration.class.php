<?php

/*
 * This file is part of the symfony package.
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfPropelPluginConfiguration Class
 *
 * @package    symfony
 * @subpackage propel
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfPropelPluginConfiguration.class.php 29156 2010-04-14 22:22:41Z bschussek $
 */
class sfPropelPluginConfiguration extends sfPluginConfiguration
{
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    sfConfig::set('sf_orm', 'propel');

    if (!sfConfig::get('sf_admin_module_web_dir'))
    {
      sfConfig::set('sf_admin_module_web_dir', '/sfPropelPlugin');
    }

    if (sfConfig::get('sf_web_debug'))
    {
      require_once dirname(__FILE__).'/../lib/debug/sfWebDebugPanelPropel.class.php';

      $this->dispatcher->connect('debug.web.load_panels', array('sfWebDebugPanelPropel', 'listenToAddPanelEvent'));
    }

    require_once sfConfig::get('sf_propel_dir', realpath(dirname(__FILE__).'/../lib/vendor/propel')).'/Propel.php';
    spl_autoload_register(array('Propel', 'autoload'));

    $manager = Propel_Manager::getInstance();
    $manager->setAttribute(Propel::ATTR_EXPORT, Propel::EXPORT_ALL);
    $manager->setAttribute(Propel::ATTR_VALIDATE, Propel::VALIDATE_NONE);
    $manager->setAttribute(Propel::ATTR_RECURSIVE_MERGE_FIXTURES, true);
    $manager->setAttribute(Propel::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
    $manager->setAttribute(Propel::ATTR_AUTOLOAD_TABLE_CLASSES, true);

    // apply default attributes
    $manager->setDefaultAttributes();

    if (method_exists($this->configuration, 'configurePropel'))
    {
      $this->configuration->configurePropel($manager);
    }

    $this->dispatcher->notify(new sfEvent($manager, 'propel.configure'));

    // make sure the culture is intercepted
    $this->dispatcher->connect('user.change_culture', array('sfPropelRecord', 'listenToChangeCultureEvent'));
  }

  /**
   * Returns options for the Propel schema builder.
   *
   * @return array
   */
  public function getModelBuilderOptions()
  {
    $options = array(
      'generateBaseClasses'  => true,
      'generateTableClasses' => true,
      'packagesPrefix'       => 'Plugin',
      'suffix'               => '.class.php',
      'baseClassesDirectory' => 'base',
      'baseClassName'        => 'sfPropelRecord',
    );

    // for BC
    $options = array_merge($options, sfConfig::get('propel_model_builder_options', array()));

    // filter options through the dispatcher
    $options = $this->dispatcher->filter(new sfEvent($this, 'propel.filter_model_builder_options'), $options)->getReturnValue();

    return $options;
  }

  /**
   * Returns a configuration array for the Propel CLI.
   *
   * @return array
   */
  public function getCliConfig()
  {
    $config = array(
      'data_fixtures_path' => array_merge(array(sfConfig::get('sf_data_dir').'/fixtures'), $this->configuration->getPluginSubPaths('/data/fixtures')),
      'models_path'        => sfConfig::get('sf_lib_dir').'/model/propel',
      'migrations_path'    => sfConfig::get('sf_lib_dir').'/migration/propel',
      'sql_path'           => sfConfig::get('sf_data_dir').'/sql',
      'yaml_schema_path'   => sfConfig::get('sf_config_dir').'/propel',
    );

    // filter config through the dispatcher
    $config = $this->dispatcher->filter(new sfEvent($this, 'propel.filter_cli_config'), $config)->getReturnValue();

    return $config;
  }
}
