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
 * sfPropelCli
 *
 * @package    symfony
 * @subpackage propel
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfPropelCli.class.php 23810 2010-11-12 11:07:44Z Kris.Wallsmith $
 */
class sfPropelCli extends Propel_Cli
{
  protected $symfonyDispatcher,
            $symfonyFormatter;

  /**
   * Set the symfony dispatcher of the cli instance
   *
   * @param object $dispatcher
   * @return void
   */
  public function setSymfonyDispatcher($dispatcher)
  {
    $this->symfonyDispatcher = $dispatcher;
  }

  /**
   * Set the symfony formatter to use for the cli
   *
   * @param object $formatter
   * @return void
   */
  public function setSymfonyFormatter($formatter)
  {
    $this->symfonyFormatter = $formatter;
  }

  /**
   * Notify the dispatcher of a message. We silent the messages from the Propel cli.
   *
   * @param string $notification
   * @param string $style
   * @return false
   */
  public function notify($notification = null, $style = 'HEADER')
  {
    $this->symfonyDispatcher->notify(new sfEvent($this, 'command.log', array($this->symfonyFormatter->formatSection('propel', $notification))));
  }

  /**
   * Notify symfony of an exception thrown by the Propel cli
   *
   * @param Propel_Exception $exception
   * @return void
   * @throws sfException
   */
  public function notifyException(Exception $exception)
  {
    throw $exception;
  }
}