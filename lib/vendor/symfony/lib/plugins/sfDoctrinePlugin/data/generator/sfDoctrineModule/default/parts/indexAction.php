  public function executeIndex(sfWebRequest $request)
  {
<?php if (isset($this->params['with_propel_route']) && $this->params['with_propel_route']): ?>
    $this-><?php echo $this->getPluralName() ?> = $this->getRoute()->getObjects();
<?php else: ?>
    $this-><?php echo $this->getPluralName() ?> = Propel::getTable('<?php echo $this->getModelClass() ?>')
      ->createQuery('a')
      ->execute();
<?php endif; ?>
  }
