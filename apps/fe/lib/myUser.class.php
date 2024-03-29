<?php

/*
 * This file is part of the fe package.
 * (c) 2010 Matteo Montanari <matteo@italinux.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


/**
 * myUser.class.php
 *
 * @package    fe
 */

/**
 * myUser class that extends sfGuardSecurityUser
 *
 * @package    fe
 * @author Matteo Montanari <matteo@italinux.com>
 */

class myUser extends sfGuardSecurityUser
{
  /**
   * Retunrns all the projects stored into the aopplication
   *
   * @access private
   * @return boolean
   */
  private function getAdminProjects()
  {
    $q = Propel_Query::create()
      ->from('Project')
      ->orderBy('created_at ASC');
    return $q->execute();
  }

  /**
   * Returns true if the actual user is a memeber of a project
   *
   * @access private
   * @param int $project_id
   * @return boolean
   */
  private function isMemberOfProject($project_id)
  {
    $projects = is_null($this->getGuardUser()->getProjects()) ? array() : $this->getGuardUser()->getProjects();
    foreach ( $projects as $project)
    {
      if ($project->getId() == $project_id)
      {
        return true;
      }
    }

    return false;
  }

  /**
   * Returns true iof the actual user is an admin
   *
   * @return <type>
   */
  public function isAdmin()
  {
    return ($this->isSuperAdmin() || $this->hasPermission('admin')) ? true : false;
  }

  public function isProjectManager()
  {
    return $this->hasCredential('project manager');
  }

  public function isCustomer()
  {
    return $this->hasCredential('customer');
  }

  public function isDeveloper()
  {
    return $this->hasCredential('user');
  }

  /**
   * TODO
   *
   * @param integer $project_id
   * @return boolean
   */
  public function canEditProject($project_id)
  {
    return $this->isAdmin();
  }

  /**
   * Retunrs the projects where the actual user is set as a member
   *
   * @param Propel_Query $query
   * @return array
   */
  public function getMyProjects($query = null)
  {
    if (!is_null($query))
    {
      return $query->execute();
    }
    
    if ($this->isAdmin())
    {
      return $this->getAdminProjects();
    }

    $sf_guard_user = $this->getGuardUser();
    return $sf_guard_user->Projects;
  }

  /**
   * Create the query for retriving the projects of a user.
   *
   * @return Propel_Query
   */
  public function getQueryForMyProjects()
  {
    $q = Propel_Query::create()
      ->from('Project p')
      ->orderBy('created_at ASC');

    if (!$this->isAdmin())
    {
      $q->addWhere('p.id IN (SELECT pu.project_id FROM ProjectUser pu WHERE pu.user_id = ?)', $this->getGuardUser()->id);
    }
    
    return $q;
  }

  /**
   * Returns true if the actual user is a member of the given project
   *
   * @param int $project_id
   * @return boolean
   */
  public function isMyProject($project_id)
  {
    return $this->isAdmin() ? true : $this->isMemberOfProject($project_id);
  }

  /**
   * Returns true if the actual user is a member of the given project
   *
   * @param int $project_id
   * @return boolean
   */
  public function isMyProjectByIssue($issue)
  {
    if (! ($issue instanceof Issue))
    {
      return false;
    }
    
    return $this->isAdmin() ? true : $this->isMemberOfProject($issue->getProject()->getId());
  }

  public function getMyProjectsIds()
  {
    $projects = ($this->isAdmin()) ? $this->getAdminProjects() : $this->getMyProjects();

    $ids = array();
    foreach ($projects as $project)
    {
      $ids[] = $project->id;
    }
    return $ids;
  }

  /**
   * Returns projects ids and names where the user have assigned issues
   *
   * @return array
   */
  public function getProjectsIdsAndNamesWhereIhaveAssignedIssues()
  {
    return Propel::getTable('Project')
            ->getQueryToRetrieveProjectWhereUserHaveAssignedIssues($this->getGuardUser()->getId())
            ->select('p.name as name, p.id as id')
            ->groupBy('p.name AND p.id')
            ->execute(array(), Propel::HYDRATE_ARRAY);
  }

  /**
   * Returns true if the id is the same as the user
   *
   * @param int $project_id
   * @return boolean
   */
  public function isMyProfile($id)
  {
    return $this->isAdmin() ? true : ($id == $this->getGuardUser()->getId());
  }

  public function retrieveNumberOfMyOpenIssueByProject($project_id)
  {
    $query = Propel::getTable('Issue')->getQueryForUserIssues($this->getGuardUser()->getId());
    return $query->
              addWhere('(s.status_type = ? OR s.status_type = ? )', array('new', 'assigned'))->
              addWhere('(i.project_id = ?)', array($project_id))->
              count();
  }

  public function retrieveMyClosedIssueByProject($project_id)
  {
    $query = Propel::getTable('Issue')->getQueryForUserIssues($this->getGuardUser()->getId());
    return $query->
              addWhere('(s.status_type = ? OR s.status_type = ? )', array('closed', 'invalid'))->
              addWhere('(i.project_id = ?)', array($project_id))->
              count();
  }

  public function retrieveMyLateIssues()
  {
    return Propel::getTable('Issue')->getLateIssuesForUserByUserId($this->getGuardUser()->getId());
  }

  public function retrieveMyUpcomingIssues($days = 7)
  {
    return Propel::getTable('Issue')->getUpcomingIssuesForUserByUserId($this->getGuardUser()->getId(), $days);
  }

  public function canSeeBudget()
  {
    return ($this->isAdmin() || !$this->isDeveloper());
  }

  public function canAddUsersToProject()
  {
    return ($this->isAdmin() || $this->hasPermission('CanAddUserToProject'));
  }

  public function retrieveMyIssuesForProject($project_id)
  {
    return Propel::getTable('Issue')->retrieveIssuesAssignedToUserByProject($this->getGuardUser()->id, $project_id);
  }

  public function countMyIssuesForProject($project_id)
  {
    return Propel::getTable('Issue')->countIssuesAssignedToUserByProject($this->getGuardUser()->id, $project_id);
  }

  public function getRoleByProject($project_id)
  {
    $this->getRoleByProject($project_id);
  }

  public function getMyTotalLogtimeForIssue($issue_id)
  {
    $logtime = Propel::getTable('LogTime')->getLogTimeForIssueByUser($issue_id, $this->getGuardUser()->getId());
    return $logtime[0]['logtimes'];
  }

}
