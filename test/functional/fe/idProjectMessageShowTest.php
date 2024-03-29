<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new idPropelTestFunctional(new sfBrowser());
$browser->initializeDatabase();


$browser->

  get('/')->
  click('Login', array('signin' => array('username' => 'puser', 'password' => 'puser')))->
  followRedirect()->

  click('Projects')->
  click('Il mio secondo progetto')->
  click('Discussions')->
  click('Primo messaggio')->

  with('request')->begin()->
    isParameter('module', 'idMessage')->
    isParameter('action', 'show')->
  end()->

  with('response')->begin()->
    checkElement('.title:contains("Primo messaggio")')->
    checkElement('div:contains("Body primo messaggio")')->

    checkElement('form input[type="submit"][value="Leave a comment"]')->
    checkElement('form input[name="fd_comment[title]"]')->
    checkElement('form textarea[name="fd_comment[body]"]')->
    checkElement('form input[name="fd_comment[model]"][type="hidden"]')->
    checkElement('form input[name="fd_comment[model_field]"][type="hidden"]')->
    checkElement('form input[name="fd_comment[user_id]"][type="hidden"]')->

    checkElement('h4:contains("pippo")')->
    checkElement('p:contains("pippo pippo poivnonjoifwe ijewjpfjpw ....")')->
    checkElement('div:contains("by Puser PUserone (puser)")')->
    checkElement('div:contains("by User Userone (user)")')->

    checkElement('.pagenation a[href~="idProject/2/idMessage/show/1?page=2"]', 3)->
  end()
;
