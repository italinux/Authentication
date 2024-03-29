<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new idPropelTestFunctional(new sfBrowser());
$browser->initializeDatabase();


$browser->
  get('/')->
  click('Login', array('signin' => array('username' => 'admin', 'password' => 'admin')))->
  
  followRedirect()->
  click('Projects')->
  
  click('Il mio primo progetto')->

  with('response')->begin()->
    checkElement('.project-navigation ul li a[href*="en/idProject/1/staff_list"]', 'Staff')->
  end()->


  click('Staff')->

  with('response')->begin()->
    checkElement('.title', '/Staff/')->
    checkElement('.title a', '/Edit/')->
  end()->

  click('Save', array('project' => array('users_list' => array('4'))))->

  with('form')->begin()->
    hasErrors(false)->
  end()->

  with('request')->begin()->
    isParameter('module', 'idProject')->
    isParameter('action', 'updateStaffList')->
    isParameter('id', '1')->
  end()->

  with('response')->begin()->
    checkElement('body:contains("NOuser N.")')->
    checkElement('body:contains("example4@example.com")')->
    checkElement('body:contains("developer")')->
  end();