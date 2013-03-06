<?php
/**
 * This file is part of the fe package.
 * (c) 2010 Matteo Montanari <matteo@italinux.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * sfGuardUserActions actions.
 *
 * @package    fe
 * @subpackage idProjectManagementPlugin Modules
 */

/**
 * sfGuardUserActions actions.
 *
 * @package    fe
 * @subpackage idProjectManagementPlugin Modules
 * @author     Matteo Montanari <matteo@italinux.com>
 */
class sfGuardUserActions extends autoSfGuardUserActions
{
  /**
   * Executes show action
   *
   * @param sfWebRequest $request
   */
  public function executeShow(sfWebRequest $request)
  {
    $this->forward404Unless($this->user = Propel::getTable('sfGuardUser')->find(array($request->getParameter('id'))));
  }

  public function executeIndex(sfWebRequest $request)
  {
    if ($request->hasParameter('sort'))
    {
      $this->setSort(array($request->getParameter('sort'), $request->getParameter('sort_type')));
    }
    
    $this->setPage($request->getParameter('page', 1));
    $this->prefix_for_sf_guard_user_field = 's';

    $this->pager = $this->getPager();
    $this->sort = $this->getSort();

  }

  protected function buildQuery()
  {
    $tableMethod = $this->configuration->getTableMethod();
    if (is_null($this->filters))
    {
      $this->filters = $this->configuration->getFilterForm($this->getFilters());
    }

    $this->filters->setTableMethod($tableMethod);
    $query = $this->filters->buildQuery($this->getFilters());

    $query->from('sfGuardUser '.$this->prefix_for_sf_guard_user_field);
    $this->addSortQuery($query);
    $event = $this->dispatcher->filter(new sfEvent($this, 'admin.build_query'), $query);
    $query = $event->getReturnValue();

    return $query;
  }

  protected function getPager()
  {
    $pager = $this->configuration->getPager('sfGuardUser');
    $pager->setQuery($this->buildQuery());
    $pager->setPage($this->getPage());
    $pager->setMaxPerPage(!is_null(sfConfig::get('app_itemperpage_users')) ? sfConfig::get('app_itemperpage_users') : 5);
    $pager->init();

    return $pager;
  }
}
