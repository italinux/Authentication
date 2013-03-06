<?php

include(dirname(__FILE__).'/../bootstrap/unit.php');
initializeDatabase();

$configuration = ProjectConfiguration::getApplicationConfiguration( 'fe', 'unittest', true);
$database_manager = new sfDatabaseManager($configuration);

$t = new lime_test(4, new lime_output_color());

$t->is(Propel::getTable('Priority')->retrieveHighestPosition(), -1, '->retrieveHighestPosition() returns the rigth value');

Propel::loadData(sfConfig::get('sf_test_dir').'/fixtures/fixtures.yml');

$sql = "SELECT p.id AS p__id, p.name AS p__name, p.position AS p__position FROM priority p ORDER BY p.position";
$query = Propel::getTable('Priority')->getPrioritiesOrderByPositionQuery();
$t->ok($query instanceof Propel_Query, '->getPrioritiesOrderByPositionQuery() returns the right object');
$t->is($query->getSqlQuery(), $sql, '->getPrioritiesOrderByPositionQuery() makes the right query');

$t->is(Propel::getTable('Priority')->retrieveHighestPosition(), 1, '->retrieveHighestPosition() returns the rigth value');