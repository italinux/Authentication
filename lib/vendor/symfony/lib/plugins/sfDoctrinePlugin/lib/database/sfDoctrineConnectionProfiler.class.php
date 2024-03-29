<?php

/**
 * Connection profiler.
 * 
 * @package    sfPropelPlugin
 * @subpackage database
 * @author     Kris Wallsmith <kris.wallsmith@symfony-project.com>
 * @version    SVN: $Id: sfPropelConnectionProfiler.class.php 20157 2010-07-13 17:00:12Z Kris.Wallsmith $
 */
class sfPropelConnectionProfiler extends Propel_Connection_Profiler
{
  protected
    $dispatcher = null,
    $options    = array();

  /**
   * Constructor.
   * 
   * Available options:
   * 
   *  * logging:              Whether to notify query logging events (defaults to false)
   *  * slow_query_threshold: How many seconds a query must take to be considered slow (defaults to 1)
   * 
   * @param sfEventDispatcher $dispatcher
   * @param array             $options
   */
  public function __construct(sfEventDispatcher $dispatcher, $options = array())
  {
    $this->dispatcher = $dispatcher;
    $this->options = array_merge(array(
      'logging'              => false,
      'slow_query_threshold' => 1,
    ), $options);
  }

  /**
   * Returns an option value.
   * 
   * @param  string $name
   * 
   * @return mixed
   */
  public function getOption($name)
  {
    return isset($this->options[$name]) ? $this->options[$name] : null;
  }

  /**
   * Sets an option value.
   * 
   * @param string $name
   * @param mixed  $value
   */
  public function setOption($name, $value)
  {
    $this->options[$name] = $value;
  }

  /**
   * Logs time and a connection query on behalf of the connection.
   * 
   * @param Propel_Event $event
   */
  public function preQuery(Propel_Event $event)
  {
    if ($this->options['logging'])
    {
      $this->dispatcher->notify(new sfEvent($event->getInvoker(), 'application.log', array(sprintf('query : %s - (%s)', $event->getQuery(), join(', ', self::fixParams($event->getParams()))))));
    }

    sfTimerManager::getTimer('Database (Propel)');

    $args = func_get_args();
    $this->__call(__FUNCTION__, $args);
  }

  /**
   * Logs to the timer.
   * 
   * @param Propel_Event $event
   */
  public function postQuery(Propel_Event $event)
  {
    sfTimerManager::getTimer('Database (Propel)')->addTime();

    $args = func_get_args();
    $this->__call(__FUNCTION__, $args);

    if ($event->getElapsedSecs() > $this->options['slow_query_threshold'])
    {
      $event->slowQuery = true;
    }
  }

  /**
   * Logs a connection exec on behalf of the connection.
   * 
   * @param Propel_Event $event
   */
  public function preExec(Propel_Event $event)
  {
    if ($this->options['logging'])
    {
      $this->dispatcher->notify(new sfEvent($event->getInvoker(), 'application.log', array(sprintf('exec : %s - (%s)', $event->getQuery(), join(', ', self::fixParams($event->getParams()))))));
    }

    sfTimerManager::getTimer('Database (Propel)');

    $args = func_get_args();
    $this->__call(__FUNCTION__, $args);
  }

  /**
   * Logs to the timer.
   * 
   * @param Propel_Event $event
   */
  public function postExec(Propel_Event $event)
  {
    sfTimerManager::getTimer('Database (Propel)')->addTime();

    $args = func_get_args();
    $this->__call(__FUNCTION__, $args);

    if ($event->getElapsedSecs() > $this->options['slow_query_threshold'])
    {
      $event->slowQuery = true;
    }
  }

  /**
   * Logs a statement execute on behalf of the statement.
   * 
   * @param Propel_Event $event
   */
  public function preStmtExecute(Propel_Event $event)
  {
    if ($this->options['logging'])
    {
      $this->dispatcher->notify(new sfEvent($event->getInvoker(), 'application.log', array(sprintf('execute : %s - (%s)', $event->getQuery(), join(', ', self::fixParams($event->getParams()))))));
    }

    sfTimerManager::getTimer('Database (Propel)');

    $args = func_get_args();
    $this->__call(__FUNCTION__, $args);
  }

  /**
   * Logs to the timer.
   * 
   * @param Propel_Event $event
   */
  public function postStmtExecute(Propel_Event $event)
  {
    sfTimerManager::getTimer('Database (Propel)')->addTime();

    $args = func_get_args();
    $this->__call(__FUNCTION__, $args);

    if ($event->getElapsedSecs() > $this->options['slow_query_threshold'])
    {
      $event->slowQuery = true;
    }
  }

  /**
   * Returns events having to do with query execution.
   *
   * @return array
   */
  public function getQueryExecutionEvents()
  {
    $events = array();
    foreach ($this as $event)
    {
      if (in_array($event->getCode(), array(Propel_Event::CONN_QUERY, Propel_Event::CONN_EXEC, Propel_Event::STMT_EXECUTE)))
      {
        $events[] = $event;
      }
    }

    return $events;
  }

  /**
   * Fixes query parameters for logging.
   * 
   * @param  array $params
   * 
   * @return array
   */
  static public function fixParams($params)
  {
    foreach ($params as $key => $param)
    {
      if (strlen($param) >= 255)
      {
        $params[$key] = '['.number_format(strlen($param) / 1024, 2).'Kb]';
      }
    }

    return $params;
  }
}
