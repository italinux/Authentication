<?php

include(dirname(__FILE__).'/../bootstrap/unit.php');
initializeDatabase();

$configuration = ProjectConfiguration::getApplicationConfiguration( 'fe', 'unittest', true);
$database_manager = new sfDatabaseManager($configuration);
Propel::loadData(sfConfig::get('sf_test_dir').'/fixtures/fixtures.yml');

$t = new lime_test(57, new lime_output_color());

$issue = Propel::getTable('Issue')->getIssueById(12);
$t->ok($issue instanceof Issue, '->getIssueById() returns the right object');
$t->ok($issue->getId() == 12, '->getIssueById() returns an object of with the right id');


$t->ok(Propel::getTable('Issue')->getQueryForMilstoneIssues(2, 2) instanceof Propel_Query, '->getQueryForMilstoneIssues(2, 2) returns a propel query object');
$t->is(Propel::getTable('Issue')->getQueryForMilstoneIssues(), null, '->getQueryForMilstoneIssues() returns null');

$t->ok(Propel::getTable('Issue')->getQueryForProjectIssues(2) instanceof Propel_Query, '->getQueryForProjectIssues(2) returns a propel query object');
$t->is(Propel::getTable('Issue')->getQueryForMilstoneIssues(), null, '->getQueryForProjectIssues() returns null');

$t->ok(Propel::getTable('Issue')->getQueryForUserIssues(2) instanceof Propel_Query, '->getQueryForUserIssues(2) returns a propel query object');
$t->is(Propel::getTable('Issue')->getQueryForUserIssues(), null, '->getQueryForUserIssues() returns null');

$logtimes = Propel::getTable('Issue')->retrieveLogTimeForProject(5);
$t->is($logtimes['project_log_times'], '15', '->retrieveLogTimeForProject(5) returns a propel query object');
$logtimes = Propel::getTable('Issue')->retrieveLogTimeForProject();
$t->is(Propel::getTable('Issue')->retrieveLogTimeForProject(), null, '->retrieveLogTimeForProject() returns null');

$results = Propel::getTable('Issue')->getIssueForProjectOrderedByStatusType(2);

$t->is(count($results), 6, 'getIssueForProjectOrderedByStatusType return the right numebr of results');
$t->is($results[0]->getStatus()->getStatusType(), 'invalid', 'getIssueForProjectOrderedByStatusType return the right numebr of results');
$t->is($results[1]->getStatus()->getStatusType(), 'new', 'getIssueForProjectOrderedByStatusType return the right numebr of results');

$result = Propel::getTable('Issue')->getSpentTimeOnIssuesClosedAndInvalidForProject(3);
$t->is($result['project_log_times'], null, 'getSpentTimeOnIssuesClosedAndInvalidForProject ok');
$result = Propel::getTable('Issue')->getOpenIssuesEstimatedTimeForProject(3);
$t->is($result['estimated_time'], 95, 'getOpenIssuesEstimatedTimeForProject(3) ok');

$result = Propel::getTable('Issue')->getSpentTimeOnIssuesClosedAndInvalidForProject(5);
$t->is($result['project_log_times'], null, 'getSpentTimeOnIssuesClosedAndInvalidForProject ok');
$result = Propel::getTable('Issue')->getOpenIssuesEstimatedTimeForProject(5);
$t->is($result['estimated_time'], 136, 'getOpenIssuesEstimatedTimeForProject(5) ok');

$result = Propel::getTable('Issue')->getSpentTimeOnIssuesClosedAndInvalidForProject(1);
$t->is($result['project_log_times'], 203, 'getSpentTimeOnIssuesClosedAndInvalidForProject ok');
$result = Propel::getTable('Issue')->getOpenIssuesEstimatedTimeForProject(1);
$t->is($result['estimated_time'], 126, 'getOpenIssuesEstimatedTimeForProject(1) ok');

$results = Propel::getTable('Issue')->getClosedIssueForProject(2);
$t->is(count($results), 0, 'getClosedIssueForProject ok');

$results = Propel::getTable('Issue')->getNewIssueForProject(2);
$t->is(count($results), 5, 'getNewIssueForProject ok');

$results = Propel::getTable('Issue')->getInvalidIssueForProject(2);
$t->is(count($results), 1, 'getInvalidIssueForProject ok');

$results = Propel::getTable('Issue')->getQueryForAssignedIssueForProject(1)->count();
$t->is($results, 1, 'getQueryForAssignedIssueForProject ok');

/*estimated and logged time for milstones issues*/

$result = Propel::getTable('Issue')->retrieveLogTimeForProjectMilestone(1, 1);
$t->is($result['milestone_log_times'], 32, 'retrieveLogTimeForProjectMilestone(1,1) ok');
$result = Propel::getTable('Issue')->retrieveEstimatedTimeForProjectMilestone(1, 1);
$t->is($result['estimated_time'], 101, 'retrieveEstimatedTimeForProjectMilestone(1,1) ok');


$result = Propel::getTable('Issue')->retrieveLogTimeForProjectMilestone(1, 2);
$t->is($result['milestone_log_times'], 205.5, 'retrieveLogTimeForProjectMilestone(1,2) ok');
$result = Propel::getTable('Issue')->retrieveEstimatedTimeForProjectMilestone(1, 2);
$t->is($result['estimated_time'], 185, 'retrieveEstimatedTimeForProjectMilestone(1,2) ok');

$result = Propel::getTable('Issue')->retrieveLogTimeForProjectMilestone(3, 3);
$t->is($result['milestone_log_times'], null, 'retrieveLogTimeForProjectMilestone(3,3) ok');
$result = Propel::getTable('Issue')->retrieveEstimatedTimeForProjectMilestone(3, 3);
$t->is($result['estimated_time'], 95, 'retrieveEstimatedTimeForProjectMilestone(3,3) ok');

$result = Propel::getTable('Issue')->retrieveLogTimeForProjectMilestone(2, 4);
$t->is($result['milestone_log_times'], null, 'retrieveLogTimeForProjectMilestone(2,4) ok');
$result = Propel::getTable('Issue')->retrieveEstimatedTimeForProjectMilestone(2, 4);
$t->is($result['estimated_time'], 0, 'retrieveEstimatedTimeForProjectMilestone(2,4) ok');

$result = Propel::getTable('Issue')->retrieveLogTimeForProjectMilestone(2, 5);
$t->is($result['milestone_log_times'], null, 'retrieveLogTimeForProjectMilestone(2,5) ok');
$result = Propel::getTable('Issue')->retrieveEstimatedTimeForProjectMilestone(2, 5);
$t->is($result['estimated_time'], 0, 'retrieveEstimatedTimeForProjectMilestone(2,5) ok');

/* late and upcoming issue for user*/

$late = Propel::getTable('Issue')->getLateIssuesForUserByUserId(2);
$t->is(count($late), 2, '->getLateIssuesForUserByUserId(2) retireves the right number of issues');
$t->is($late[0]->id, 91, 'retrieved right issue');
$t->is($late[1]->id, 92, 'retrieved right issue');
$upcoming = Propel::getTable('Issue')->getUpcomingIssuesForUserByUserId(2);
$t->is(count($upcoming), 11, '->getUpcomingIssuesForUserByUserId(2) retireves the right number of issues');
$t->is($upcoming[4]->id, 6, 'retrieved right issue');
$t->is($upcoming[10]->id, 12, 'retrieved right issue');

$late = Propel::getTable('Issue')->getLateIssuesForUserByUserId(3);
$t->is(count($late), 0, '->getLateIssuesForUserByUserId(3) retireves the right number of issues');
$upcoming = Propel::getTable('Issue')->getUpcomingIssuesForUserByUserId(3);
$t->is(count($upcoming), 3, '->getUpcomingIssuesForUserByUserId(4) retireves the right number of issues');
$t->is($upcoming[0]->id, 1, 'retrieved right issue');
$t->is($upcoming[1]->id, 2, 'retrieved right issue');
$t->is($upcoming[2]->id, 70, 'retrieved right issue');


/*log time grouped by date*/

$dates_logtime = Propel::getTable('Issue')->retrieveLogTimeForProjectGoupByCreatedAt(1);
$t->is(count($dates_logtime), 6, '->retrieveLogTimeForProjectGoupByCreatedAt() returns the right number of dates');

$t->is($dates_logtime[0]['date'], date('Y-m-d', strtotime('-5 days')), '->retrieveLogTimeForProjectGoupByCreatedAt() returns the right date');
$t->is($dates_logtime[0]['logged_time'], 16, '->retrieveLogTimeForProjectGoupByCreatedAt() returns the right logged time');

$t->is($dates_logtime[1]['date'], date('Y-m-d', strtotime('-4 days')), '->retrieveLogTimeForProjectGoupByCreatedAt() returns the right date');
$t->is($dates_logtime[1]['logged_time'], 16, '->retrieveLogTimeForProjectGoupByCreatedAt() returns the right logged time');

$t->is($dates_logtime[2]['date'], date('Y-m-d', strtotime('-3 days')), '->retrieveLogTimeForProjectGoupByCreatedAt() returns the right date');
$t->is($dates_logtime[2]['logged_time'], 41, '->retrieveLogTimeForProjectGoupByCreatedAt() returns the right logged time');

$t->is($dates_logtime[3]['date'], date('Y-m-d', strtotime('-2 days')), '->retrieveLogTimeForProjectGoupByCreatedAt() returns the right date');
$t->is($dates_logtime[3]['logged_time'], 82, '->retrieveLogTimeForProjectGoupByCreatedAt() returns the right logged time');

$t->is($dates_logtime[4]['date'], date('Y-m-d', strtotime('-1 days')), '->retrieveLogTimeForProjectGoupByCreatedAt() returns the right date');
$t->is($dates_logtime[4]['logged_time'], 81.3, '->retrieveLogTimeForProjectGoupByCreatedAt() returns the right logged time');

$t->is($dates_logtime[5]['date'], date('Y-m-d'), '->retrieveLogTimeForProjectGoupByCreatedAt() returns the right date');
$t->is($dates_logtime[5]['logged_time'], 1.2, '->retrieveLogTimeForProjectGoupByCreatedAt() returns the right logged time');

