<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new idPropelTestFunctional(new sfBrowser());
$browser->initializeDatabase();

  $browser->

  get('/')->
  click('Login', array('signin' => array('username' => 'puser', 'password' => 'puser')))->

  followRedirect()->
  
  click('puser')->

  with('request')->begin()->
    isParameter('module', 'idProfile')->
    isParameter('action', 'edit')->
  end()->

  with('response')->begin()->
    checkElement('label:contains("Is active")', false)->
    checkElement('label:contains("Is super admin")', false)->
    checkElement('label:contains("Groups")', false)->
    checkElement('label:contains("Permissions")', false)->
  end()->

  click('Save', array('sf_guard_user' => array('password' => 'mario2',
                                               'password_again' => 'mario2',
                                               'username' => 'mariotto',
                                               'first_name' => 'mariotto',
                                               'last_name' => 'mariotti',
                                               'email_address' => 'mariotti@examople.com'
  )))->

  with('form')->begin()->
    hasErrors(false)->
  end()->

  followRedirect()->

  with('request')->begin()->
    isParameter('module', 'idProfile')->
    isParameter('action', 'index')->
  end()->
  with('response')->begin()->
    checkElement('body:contains("mariotto")')->
  end();
