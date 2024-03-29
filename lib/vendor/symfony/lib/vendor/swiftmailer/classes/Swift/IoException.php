<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2010 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//@require 'Swift/SwiftException.php';

/**
 * I/O Exception class.
 * @package Swift
 * @author Chris Corbyn
 */
class Swift_IoException extends Swift_SwiftException
{
  
  /**
   * Create a new IoException with $message.
   * @param string $message
   */
  public function __construct($message)
  {
    parent::__construct($message);
  }
  
}
