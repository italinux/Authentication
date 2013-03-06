<?php
/**
 * This file is part of the fe package.
 * (c) 2010 Matteo Montanari <matteo@italinux.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * idPropelTestFunctional.class.php
 *
 * @package    fe
 * @subpackage idProjectManagementPlugin Test
 */

/**
 * idPropelTestFunctional
 * 
 * Extends the standard sfTestFunctional class to add propel fixtures loading methods
 *
 * @package    fe
 * @subpackage idProjectManagementPlugin Test
 * @author     Matteo Montanari <matteo@italinux.com>
 */

class idPropelTestFunctional extends sfTestFunctional
{
  /**
   * Creates the commands for loading the fixture into the database for the test enviroment, and then it calls executeShellCommand for the created command.
   *
   * @access private
   */
  public function initializeDatabase()
  {
    $this->info('Initializing database');

    $command = "./symfony propel:drop-db --env=test --no-confirmation; ";
    $command .= "./symfony propel:build-db --env=test; ";
    $command .= "./symfony propel:build-sql --env=test; ";
    $command .= "./symfony propel:insert-sql --env=test; ";
    $this->executeShellCommand($command);

    $this->loadFixtures();

  }

  public function loadFixtures()
  {
    $this->info('Loading fixtures');
    $command .= "./symfony propel:data-load --env=test test/fixtures/fixtures.yml";
    $this->executeShellCommand($command);
  }

  public function loadEventFixtures()
  {
    $this->info('Loading events fixtures');
    $command = "./symfony propel:data-load --env=test test/fixtures/event_log_fixtures.yml";
    $this->executeShellCommand($command);
  }

  /**
   * Gets a command as string and call "exec" php function on it.
   * If the command return an error the method will throw an exception
   *
   * @param string $command
   */
  private function executeShellCommand($command)
  {
    $result = exec($command." 2>&1", $output, $error);
    if($error){
      throw new Exception('Error at [line '.__LINE__.'] of [file '.__FILE__.'] in loading fixtures : '.$command. ' ' .print_r($output, true).' '.print_r($error, true));
    }
  }


  public function showPage()
  {
    $this->with('response')->begin()->
      debug()->
    end();

    return $this;
  }

}

?>
