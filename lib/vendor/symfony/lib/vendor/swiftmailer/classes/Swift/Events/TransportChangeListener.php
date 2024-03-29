<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2010 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//@require 'Swift/Events/EventListener.php';
//@require 'Swift/Events/TransportChangeEvent.php';

/**
 * Listens for changes within the Transport system.
 * 
 * @package Swift
 * @subpackage Events
 * 
 * @author Chris Corbyn
 */
interface Swift_Events_TransportChangeListener extends Swift_Events_EventListener
{
  
  /**
   * Invoked just before a Transport is started.
   * 
   * @param Swift_Events_TransportChangeEvent $evt
   */
  public function beforeTransportStarted(Swift_Events_TransportChangeEvent $evt);
  
  /**
   * Invoked immediately after the Transport is started.
   * 
   * @param Swift_Events_TransportChangeEvent $evt
   */
  public function transportStarted(Swift_Events_TransportChangeEvent $evt);
  
  /**
   * Invoked just before a Transport is stopped.
   * 
   * @param Swift_Events_TransportChangeEvent $evt
   */
  public function beforeTransportStopped(Swift_Events_TransportChangeEvent $evt);
  
  /**
   * Invoked immediately after the Transport is stopped.
   * 
   * @param Swift_Events_TransportChangeEvent $evt
   */
  public function transportStopped(Swift_Events_TransportChangeEvent $evt);
  
}
