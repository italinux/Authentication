<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new idPropelTestFunctional(new sfBrowser());
$browser->initializeDatabase();
$browser->loadEventFixtures();

$browser->
  get('/')->

  click('Login', array('signin' => array('username' => 'customer', 'password' => 'customer')))->

  followRedirect()->

  with('request')->begin()->
    isParameter('module', 'idDashboard')->
    isParameter('action', 'index')->
  end()->

  with('response')->begin()->
    checkElement('#content .title', 'Recent Activity')->
    checkElement('#content .menu')->
    checkElement('#content ul.action ul li:contains("'.date('F d',  strtotime('+1 minute')).'")')->

    checkElement('.contentWrapper .dashboard .recent .dashboard-row:contains("message 22")', false)->
    checkElement('.contentWrapper .dashboard .recent .dashboard-row:contains("message 42")', false)->

    checkElement('#content ul.action ul li.span-15', '/message 19/', array('position' => 0))->
    checkElement('#content ul.action ul li.span-15', '/message 18/', array('position' => 1))->
    checkElement('#content ul.action ul li.span-15', '/message 17/', array('position' => 2))->
    checkElement('#content ul.action ul li.span-15', '/message 16/', array('position' => 3))->
    checkElement('#content ul.action ul li.span-15', '/message 15/', array('position' => 4))->
    checkElement('#content ul.action ul li.span-15', '/message 14/', array('position' => 5))->
    checkElement('#content ul.action ul li.span-15', '/message 13/', array('position' => 6))->
    checkElement('#content ul.action ul li.span-15', '/message 12/', array('position' => 7))->
    checkElement('#content ul.action ul li.span-15', '/message 11/', array('position' => 8))->
    checkElement('#content ul.action ul li.span-15', '/message 10/', array('position' => 9))->

    checkElement('#sidebar')->
    checkElement('#sidebar h3 a', 5)->
    checkElement('#sidebar .box', 5)->
    checkElement('#sidebar .box .percent', '43.48%', array('position' => 0))->

    checkElement('#sidebar .box .progress div[class="progress-green"][style*="43.48%"]', true)->
    checkElement('#sidebar .box .progress div[class="progress-grey"][style*="4.35%"]', true)->
  end()
;