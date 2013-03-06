<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'backend';
$fixtures = 'fixtures';
require_once(dirname(__FILE__).'/../bootstrap/functional.php');

$tests = array(
  '/propel/route/test1' => '/Article/',
  '/propel/route/test2' => '/Article/',
  '/propel/route/test3' => '/Propel_Collection/',
  '/propel/route/test4' => '/Propel_Collection/',
  '/propel/route/test5/1/some_fake_value' => '/Article/',
  '/propel/route/test6/english-title/some_fake_value' => '/Article/',
  '/propel/route/test7/some_fake_value' => '/Propel_Collection/',
  '/propel/route/test9/1/english-title/English+Title/test' => '/Article/',
  '/propel/route/test10/1/test' => '/Propel_Collection/',
);

$b = new sfTestBrowser();
foreach ($tests as $url => $check)
{
  $b->
    get($url)->
    with('response')->begin()->
      isStatusCode('200')->
      matches($check)->
    end()
  ;
}

$article = Propel::getTable('Article')->find(1);

$routes = array(
  'propel_route_test5' => array(
    'url' => '/index.php/propel/route/test5/1/test-english-title',
    'params' => $article
  ),
  'propel_route_test6' => array(
    'url' => '/index.php/propel/route/test6/english-title/test-english-title',
    'params' => $article
  ),
  'propel_route_test7' => array(
    'url' => '/index.php/propel/route/test7/w00t',
    'params' => array('testing_non_column' => 'w00t')
  ),
  'propel_route_test8' => array(
    'url' => '/index.php/propel/route/test8/1/english-title/English+Title/test',
    'params' => array(
      'id' => $article->id,
      'slug' => $article->slug,
      'title' => $article->title,
      'testing_non_column2' => 'test'
    )
  ),
);

foreach ($routes as $route => $check)
{
  $url = url_for2($route, $check['params']);
  $b->test()->is($url, $check['url'], 'Check "' . $route . '" generates correct url');
}