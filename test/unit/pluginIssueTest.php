<?php
include(dirname(__FILE__).'/../bootstrap/unit.php');
initializeDatabase();

$configuration = ProjectConfiguration::getApplicationConfiguration( 'fe', 'unittest', true);
new sfDatabaseManager($configuration);
Propel::loadData(sfConfig::get('sf_test_dir').'/fixtures/fixtures.yml');


$issue = Propel::getTable('Issue')->createQuery()->fetchOne();

$t = new lime_test(15, new lime_output_color());

$t->like("".$issue, '/#.* new issue/', '__toString() returns the right value');
$t->is($issue->hasComments(), true, 'hasComments() returns the right value');

$issues = Propel::getTable('Issue')->countByProject(5);
$t->is($issues['issues'], 15, 'countByProject() returns the right number of issues');

$issues = Propel::getTable('Issue')->countByProject();
$t->is($issues, null, 'countByProject() returns null');

$issues = Propel::getTable('Issue')->countByProjectWithEstimatedTime(5);
$t->is($issues['issues'], 10, 'countByProjectWithEstimatedTime() returns the right number of issues');

$issues = Propel::getTable('Issue')->countByProjectWithEstimatedTime();
$t->is($issues, null, 'countByProjectWithEstimatedTime() returns the right number of issues');

$issues = Propel::getTable('Issue')->countByTrackerOfProjectWithEstimatedTime(5);
$t->is($issues[''], 1, 'countByTrackerOfProjectWithEstimatedTime() returns the right number of issues');
$t->is($issues['Task'], 6, 'countByTrackerOfProjectWithEstimatedTime() returns the right number of issues');
$t->is($issues['user story'], 3, 'countByTrackerOfProjectWithEstimatedTime() returns the right number of issues');

$issues = Propel::getTable('Issue')->countByTrackerOfProjectWithEstimatedTime();
$t->is($issues, null, 'countByTrackerOfProjectWithEstimatedTime() returns the right number of issues');

$issues = Propel::getTable('Issue')->countByTrackerOfProjectWithoutEstimatedTime(5);
$t->is($issues['Bug'], 5, 'countByTrackerOfProjectWithoutEstimatedTime() returns the right number of issues');

$issues = Propel::getTable('Issue')->countByTrackerOfProjectWithoutEstimatedTime();
$t->is($issues, null, 'countByTrackerOfProjectWithoutEstimatedTime() returns the right number of issues');

$issues = Propel::getTable('Issue')->retrieveEstimatedTimeForProject(5);
$t->is($issues['estimated_time'], 136, 'retrieveEstimatedTimeForProject() returns the right number of issues');

$issues = Propel::getTable('Issue')->retrieveEstimatedTimeForProjectMilestone(1, 1);
$t->is($issues['estimated_time'], 101, 'retrieveEstimatedTimeForProjectMilestone() returns the right number of estimated hours');


$issue = Propel::getTable('Issue')->find(69);
$t->is($issue->getTotalLogTime(), '2.5', 'getTotalLogTime() ok');
