<?php

$app = 'frontend';
$fixtures = 'fixtures/fixtures.yml';
include dirname(__FILE__).'/../../bootstrap/functional.php';

$t = new lime_test(1);

// ->clean()
$t->diag('->clean()');

$query = Propel_Core::getTable('Author')->createQuery();
$validator = new sfValidatorPropelChoice(array('model' => 'Author', 'query' => $query));

$author = Propel_Core::getTable('Author')->createQuery()->limit(1)->fetchOne();
$validator->clean($author->id);

$t->is(trim($query->getDql()), 'FROM Author', '->clean() does not change the supplied query object');
