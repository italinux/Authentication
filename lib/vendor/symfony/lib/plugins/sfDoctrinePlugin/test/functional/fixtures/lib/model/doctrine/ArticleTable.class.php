<?php
/**
 * This class has been auto-generated by the Propel ORM Framework
 */
class ArticleTable extends Propel_Table
{
  public function retrieveArticle1(Propel_Query $query)
  {
    return $query->execute();
  }

  public function retrieveArticle2(array $parameters)
  {
    $query = $this->createQuery('a');
    return $query->execute();
  }

  public function retrieveArticle3(array $parameters)
  {
    $query = $this->createQuery('a');
    return $query->execute();
  }

  public function retrieveArticle4(array $parameters)
  {
    $query = $this->createQuery('a');
    return $query->fetchOne();
  }

  public function routeTest9(array $parameters)
  {
    return Propel_Query::create()
      ->from('Article a')
      ->where('a.id = ?', $parameters['id'])
      ->limit(1)
      ->execute();
  }

  public function routeTest10(Propel_Query $q)
  {
    $q->orWhere($q->getRootAlias() . '.is_on_homepage = ?', 0);
    return $q->fetchOne();
  }

  public function testAdminGenTableMethod(Propel_Query $q)
  {
    return $q;
  }

  public function getNewQuery()
  {
    return $this->createQuery()->select('title, body');
  }

  public function addOnHomepage(Propel_Query $q = null)
  {
    if (is_null($q))
    {
      $q = $this->createQuery('a');
    }
    $alias = $q->getRootAlias();
    return $q->addWhere($alias.'.is_on_homepage = 1');
  }

  public function filterSuppliedQuery($query)
  {
    $query->select('title, body');
  }

  public function filterSuppliedQueryAndReturn($query)
  {
    return $query->select('title, body');
  }
}
