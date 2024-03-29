<?php

include(dirname(__FILE__).'/../bootstrap/unit.php');
include(dirname(__FILE__).'/../../plugins/idProjectManagementPlugin/lib/generator/LogMessageGenerator.php');
initializeDatabase();
$configuration = ProjectConfiguration::getApplicationConfiguration( 'fe', 'unittest', true);
new sfDatabaseManager($configuration);
sfContext::createInstance($configuration);

$t = new lime_test(12, new lime_output_color());

class Project
{
  public $name = 'progetto';
}

$link = LogMessageGenerator::getLinkForObject(new Project);
$t->like($link, '/<a href=".*en\/idProject\/show\/">progetto<\/a>/', 'getLinkForObject ok for project class');

class Milestone
{
  public $title = 'milestone';
  public $project_id = 33;
  public $id = 33;
}

$link = LogMessageGenerator::getLinkForObject(new Milestone);
$t->like($link, '/<a href=".*en\/idProject\/33\/idMilestone\/show\/33">milestone<\/a>/', 'getLinkForObject ok for milestone class');

class Issue
{
  public $title = 'issue';
  public $project_id = 33;
  public $id = 33;
}

$link = LogMessageGenerator::getLinkForObject(new Issue);
$t->like($link, '/<a href=".*en\/idProject\/33\/idIssue\/show\/33">#33 issue<\/a>/', 'getLinkForObject ok for issue class');

class LogTime
{
  public $issue;
  public $log_time = '12,3';
  public function __construct()
  {
     $this->issue = new Issue();
  }
}

$link = LogMessageGenerator::getLinkForObject(new LogTime);
$t->like($link, '/time \(12,3 hours\) for <a href=".*en\/idProject\/33\/idIssue\/show\/33">#33 issue<\/a>/', 'getLinkForObject ok for logtime class');

class Message
{
  public $title = 'message';
  public $project_id = 33;
  public $id = 33;
}

$link = LogMessageGenerator::getLinkForObject(new Message);
$t->like($link, '/<a href=".*en\/idProject\/33\/idMessage\/show\/33">message<\/a>/', 'getLinkForObject ok for message class');

class MyClass123456789 {}

$link = LogMessageGenerator::getLinkForObject(new MyClass123456789);
$t->like($link, '/<a href=/', 'getLinkForObject ok for generic class class');
$t->like($link, '/MyClass123456789<\/a>/', 'getLinkForObject ok for generic class class');

class MyClassToString
{
  public function __toString()
  {
    return 'my_class';
  }
}

$link = LogMessageGenerator::getLinkForObject(new MyClassToString);
$t->like($link, '/<a href=.*my_class<\/a>/', 'getLinkForObject ok for generic class with to string method');

class EventMock extends Propel_Event
{
  public function getInvoker()
  {
    return new Issue;
  }
}

class userMock
{
  function getGuardUser()
  {
    return $this;
  }

  function getProfile()
  {
    return $this;
  }

  function getFirstName()
  {
    return "Fibus";
  }

  function getLastName()
  {
    return "Lastname";
  }
}

LogMessageGenerator::generateMessageAndStoreFromPropelEvent(new userMock, 'create', new EventMock);
$logs = Propel::getTable('EventLog')->findAll(Propel::HYDRATE_ARRAY);

$t->is($logs[0]['namespace'], 'issue', 'generateAndStore() ok');
$t->is($logs[0]['action'], 'create', 'generateAndStore() ok');
$t->is($logs[0]['id'], '1', 'generateAndStore() ok');
$t->is($logs[0]['project_id'], '33', 'generateAndStore() ok');
