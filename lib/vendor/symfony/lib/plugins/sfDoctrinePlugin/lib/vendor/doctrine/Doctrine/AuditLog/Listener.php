<?php
/*
 *  $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.propel-project.org>.
 */

/**
 * Propel_AuditLog_Listener
 *
 * @package     Propel
 * @subpackage  AuditLog
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.propel-project.org
 * @since       1.0
 * @version     $Revision$
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 */
class Propel_AuditLog_Listener extends Propel_Record_Listener
{
    /**
     * Instance of Propel_Auditlog
     *
     * @var Propel_AuditLog
     */
    protected $_auditLog;

    /**
     * Instantiate AuditLog listener and set the Propel_AuditLog instance to the class
     *
     * @param   Propel_AuditLog $auditLog 
     * @return  void
     */
    public function __construct(Propel_AuditLog $auditLog) 
    {
        $this->_auditLog = $auditLog;
    }

    /**
     * Pre insert event hook for incrementing version number
     *
     * @param   Propel_Event $event
     * @return  void
     */
    public function preInsert(Propel_Event $event)
    {
        $version = $this->_auditLog->getOption('version');
        $name = $version['alias'] === null ? $version['name'] : $version['alias'];

        $record = $event->getInvoker();
        $record->set($name, $this->_getInitialVersion($record));
    }

    /**
     * Post insert event hook which creates the new version record
     * This will only insert a version record if the auditLog is enabled
     *
     * @param   Propel_Event $event 
     * @return  void
     */
    public function postInsert(Propel_Event $event) 
    {
        if ($this->_auditLog->getOption('auditLog')) {
            $class = $this->_auditLog->getOption('className');

            $record  = $event->getInvoker();
            $version = new $class();
            $version->merge($record->toArray(), false);
            $version->save();
        }
    }

    /**
     * Pre delete event hook deletes all related versions
     * This will only delete version records if the auditLog is enabled
     *
     * @param   Propel_Event $event
     * @return  void
     */
    public function preDelete(Propel_Event $event)
    {
        if ($this->_auditLog->getOption('auditLog')) {
	        $className = $this->_auditLog->getOption('className');
            $version = $this->_auditLog->getOption('version');
            $name = $version['alias'] === null ? $version['name'] : $version['alias'];
	        $event->getInvoker()->set($name, null);

            if ($this->_auditLog->getOption('deleteVersions')) {
    	        $q = Propel_Core::getTable($className)
    	            ->createQuery('obj')
    	            ->delete();
    	        foreach ((array) $this->_auditLog->getOption('table')->getIdentifier() as $id) {
    	            $conditions[] = 'obj.' . $id . ' = ?';
    	            $values[] = $event->getInvoker()->get($id);
    	        }

    	        $rows = $q->where(implode(' AND ', $conditions))
    					  ->execute($values);
    		}
        }
    }

    /**
     * Pre update event hook for inserting new version record
     * This will only insert a version record if the auditLog is enabled
     *
     * @param  Propel_Event $event
     * @return void
     */
    public function preUpdate(Propel_Event $event)
    {
        if ($this->_auditLog->getOption('auditLog')) {
            $class  = $this->_auditLog->getOption('className');
            $record = $event->getInvoker();

            $version = $this->_auditLog->getOption('version');
            $name = $version['alias'] === null ? $version['name'] : $version['alias'];

            $record->set($name, $this->_getNextVersion($record));

            $version = new $class();
            $version->merge($record->toArray(), false);
            $version->save();
        }
    }

    /**
     * Get the initial version number for the audit log
     *
     * @param Propel_Record $record
     * @return integer $initialVersion
     */
    protected function _getInitialVersion(Propel_Record $record)
    {
        return 1;
    }

    /**
     * Get the next version number for the audit log
     *
     * @param Propel_Record $record 
     * @return integer $nextVersion
     */
    protected function _getNextVersion(Propel_Record $record)
    {
      if ($this->_auditLog->getOption('auditLog')) {
          return ($this->_auditLog->getMaxVersion($record) + 1);
      }
    }
}