<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A symfony database driver for Propel.
 *
 * @package    symfony
 * @subpackage propel
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfPropelDatabase.class.php 28902 2010-03-30 20:57:27Z Jonathan.Wage $
 */
class sfPropelDatabase extends sfDatabase
{
  /**
   * Instance of the Propel_Connection for this instance of sfPropelDatabase.
   * Connection can be accessed by the getPropelConnection() accessor method.
   *
   * @var Propel_Connection $_propelConnection
   */
  protected $_propelConnection = null;

  /**
   * @var sfPropelConnectionProfiler
   **/
  protected $profiler = null;

  /**
   * Initialize a sfPropelDatabase connection with the given parameters.
   *
   * <code>
   * $parameters = array(
   *    'name'       => 'propel',
   *    'dsn'        => 'sqlite:////path/to/sqlite/db');
   *
   * $p = new sfPropelDatabase($parameters);
   * </code>
   *
   * @param array $parameters  Array of parameters used to initialize the database connection
   * @return void
   */
  public function initialize($parameters = array())
  {
    parent::initialize($parameters);

    if (null !== $this->_propelConnection)
    {
      return;
    }

    $dsn = $this->getParameter('dsn');
    $name = $this->getParameter('name');

    // Make sure we pass non-PEAR style DSNs as an array
    if ( !strpos($dsn, '://'))
    {
      $dsn = array($dsn, $this->getParameter('username'), $this->getParameter('password'));
    }

    // Make the Propel connection for $dsn and $name
    $configuration = sfProjectConfiguration::getActive();
    $dispatcher = $configuration->getEventDispatcher();
    $manager = Propel_Manager::getInstance();

    $this->_propelConnection = $manager->openConnection($dsn, $name);

    $attributes = $this->getParameter('attributes', array());
    foreach ($attributes as $name => $value)
    {
      if (is_string($name))
      {
        $stringName = $name;
        $name = constant('Propel_Core::ATTR_'.strtoupper($name));
      }

      if (is_string($value))
      {
        $valueConstantName = 'Propel_Core::'.strtoupper($stringName).'_'.strtoupper($value);
        $value = defined($valueConstantName) ? constant($valueConstantName) : $value;
      }

      $this->_propelConnection->setAttribute($name, $value);
    }

    $encoding = $this->getParameter('encoding', 'UTF8');
    $eventListener = new sfPropelConnectionListener($this->_propelConnection, $encoding);
    $this->_propelConnection->addListener($eventListener);

    // Load Query Profiler
    if ($this->getParameter('profiler', sfConfig::get('sf_debug')))
    {
      $this->profiler = new sfPropelConnectionProfiler($dispatcher, array(
        'logging' => $this->getParameter('logging', sfConfig::get('sf_logging_enabled')),
      ));
      $this->_propelConnection->addListener($this->profiler, 'symfony_profiler');
    }

    // Invoke the configuration methods for the connection if they exist (deprecated in favor of the "propel.configure_connection" event)
    $method = sprintf('configurePropelConnection%s', ucwords($this->_propelConnection->getName()));

    if (method_exists($configuration, 'configurePropelConnection') && ! method_exists($configuration, $method))
    {
      $configuration->configurePropelConnection($this->_propelConnection);
    }

    if (method_exists($configuration, $method))
    {
      $configuration->$method($this->_propelConnection);
    }

    $dispatcher->notify(new sfEvent($manager, 'propel.configure_connection', array('connection' => $this->_propelConnection, 'database' => $this)));
  }

  /**
   * Get the Propel_Connection instance.
   *
   * @return Propel_Connection $conn
   */
  public function getPropelConnection()
  {
    return $this->_propelConnection;
  }

  /**
   * Returns the connection profiler.
   * 
   * @return sfPropelConnectionProfiler|null
   */
  public function getProfiler()
  {
    return $this->profiler;
  }

  /**
   * Initializes the connection and sets it to object.
   *
   * @return void
   */
  public function connect()
  {
    $this->connection = $this->_propelConnection->getDbh();
  }

  /**
   * Execute the shutdown procedure.
   *
   * @return void
   */
  public function shutdown()
  {
    if ($this->connection !== null)
    {
      $this->connection = null;
    }
    if ($this->_propelConnection !== null) 
    { 
      $this->_propelConnection->getManager()->closeConnection($this->_propelConnection); 
    }
  }
}