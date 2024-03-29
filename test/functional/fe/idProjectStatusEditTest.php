<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new idPropelTestFunctional(new sfBrowser());
$browser->initializeDatabase();

$browser->

get('/')->
click('Login', array('signin' => array('username' => 'admin', 'password' => 'admin')))->
  followRedirect()->

  click('Settings')->
  click('Statuses')->

  click('Edit')->

  with('request')->begin()->
    isParameter('module', 'idStatus')->
    isParameter('action', 'edit')->
  end()->

  with('response')->begin()->
    checkElement('select#status_status_type option[value="new"][selected="selected"]', 'new')->
    checkElement('select#status_status_type option[value="invalid"]', 'invalid')->
    checkElement('select#status_status_type option[value="closed"]', 'closed')->
    checkElement('select#status_status_type option', 4)->
  end()->

  click('Save', array(
                      'status' => array(
                                            'name' => 'closed',
                                            'status_type' => 'invalid'
                                       ),
                      'status_type' => 'closed'
                     )
       )->

  followRedirect()->

  with('request')->begin()->
    isParameter('module', 'idStatus')->
    isParameter('action', 'index')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('table.table td:contains("invalid")')->

    checkElement('table.table td a[href="/index.php/en/idStatus/edit/1"]')->
    checkElement('table.table td:contains("closed")')->
end();
