<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new idPropelTestFunctional(new sfBrowser());
$browser->initializeDatabase();


$browser->

  get('/')->
  click('Login', array('signin' => array('username' => 'puser', 'password' => 'puser')))->
  followRedirect()->

  click('Il mio terzo progetto')->

  click('Issues')->

  click('#1')->

  with('request')->begin()->
    isParameter('module', 'idIssue')->
    isParameter('action', 'show')->
    isParameter('issue_id', '1')->
  end()->

  with('response')->begin()->
    isStatusCode('200')->
    checkElement('input[name="issue[estimated_time]"]')->
    checkElement('input[type="submit"][value="Set"]')->
  end()->

  click('Set', array('issue' => array('estimated_time' => '0.5')))->

  //with('response')->begin()->
  //  debug()->
  //end()->

  followRedirect()->

  with('request')->begin()->
    isParameter('module', 'idIssue')->
    isParameter('action', 'show')->
    isParameter('issue_id', '1')->
  end()->

  with('response')->begin()->
    isStatusCode('200')->
    checkElement('input[id="issue_estimated_time"][value="0.5"]')->
  end()->

  click('Set', array('issue' => array('estimated_time' => '1.3', 'id' => 1, 'project_id' => 3)))->
  followRedirect()->

  with('request')->begin()->
    isParameter('module', 'idIssue')->
    isParameter('action', 'show')->
    isParameter('issue_id', '1')->
  end()->

  with('response')->begin()->
    isStatusCode('200')->
    checkElement('input[id="issue_estimated_time"][value="1.3"]')->
  end()->

  click('Set', array('issue' => array('estimated_time' => '13', 'id' => 1, 'project_id' => 3)))->
  followRedirect()->

  with('request')->begin()->
    isParameter('module', 'idIssue')->
    isParameter('action', 'show')->
    isParameter('issue_id', '1')->
  end()->

  with('response')->begin()->
    isStatusCode('200')->
    checkElement('input[id="issue_estimated_time"][value="13"]')->
  end()->

  click('Set', array('issue' => array('estimated_time' => '-3', 'id' => 1, 'project_id' => 3)))->
  followRedirect()->

  with('request')->begin()->
    isParameter('module', 'idIssue')->
    isParameter('action', 'show')->
    isParameter('issue_id', '1')->
  end()->

  with('response')->begin()->
    checkElement('body:contains("You cannot set a negative estimated time")')->
  end();
