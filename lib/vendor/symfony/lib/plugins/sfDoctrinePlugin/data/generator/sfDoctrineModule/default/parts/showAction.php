  public function executeShow(sfWebRequest $request)
  {
<?php if (isset($this->params['with_propel_route']) && $this->params['with_propel_route']): ?>
    $this-><?php echo $this->getSingularName() ?> = $this->getRoute()->getObject();
<?php else: ?>
    $this-><?php echo $this->getSingularName() ?> = Propel::getTable('<?php echo $this->getModelClass() ?>')->find(array(<?php echo $this->getRetrieveByPkParamsForAction(65) ?>));
    $this->forward404Unless($this-><?php echo $this->getSingularName() ?>);
<?php endif; ?>
  }
