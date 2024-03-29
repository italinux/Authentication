<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2010 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//@require 'Swift/Events/EventListener.php';
//@require 'Swift/Events/CommandEvent.php';

/**
 * Listens for Transports to send commands to the server.
 * @package Swift
 * @subpackage Events
 * @author Chris Corbyn
 */
interface Swift_Events_CommandListener extends Swift_Events_EventListener
{
  
  /**
   * Invoked immediately following a command being sent.
   * @param Swift_Events_ResponseEvent $evt
   */
  public function commandSent(Swift_Events_CommandEvent $evt);
  
}
