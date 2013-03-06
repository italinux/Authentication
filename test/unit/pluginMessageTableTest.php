<?php
include(dirname(__FILE__).'/../bootstrap/unit.php');
initializeDatabase();

$configuration = ProjectConfiguration::getApplicationConfiguration( 'fe', 'unittest', true);
new sfDatabaseManager($configuration);
Propel::loadData(sfConfig::get('sf_test_dir').'/fixtures/fixtures.yml');


$message_query = Propel::getTable('Message')->getQueryForProjectMessages(2);
$results = $message_query->execute();

$t = new lime_test(4, new lime_output_color());

$t->ok($message_query instanceof Propel_query, 'retrieveQueryForProjectMessages returns a Propel_Query instance');
$t->is(count($results), 2, 'retrieveQueryForProjectessages returns the right Propel_Query instance');


$last_comment = Propel::getTable('Message')->getLastComment(1);
$t->is('pippo', $last_comment->title, 'getLastComment ok');
$t->is('pippo pippo poivnonjoifwe ijewjpfjpw ....', $last_comment->body, 'getLastComment ok');