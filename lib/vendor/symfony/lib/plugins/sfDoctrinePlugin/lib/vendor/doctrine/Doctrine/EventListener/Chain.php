<?php
/*
 *  $Id: Chain.php 7490 2010-03-29 19:53:27Z jwage $
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
 * Propel_EventListener_Chain
 * this class represents a chain of different listeners,
 * useful for having multiple listeners listening the events at the same time
 *
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @package     Propel
 * @subpackage  EventListener
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.propel-project.org
 * @since       1.0
 * @version     $Revision: 7490 $
 */
class Propel_EventListener_Chain extends Propel_Access implements Propel_EventListener_Interface
{
    /**
     * @var array $listeners        an array containing all listeners
     */
    protected $_listeners = array();

    /**
     * add
     * adds a listener to the chain of listeners
     *
     * @param object $listener
     * @param string $name
     * @return void
     */
    public function add($listener, $name = null)
    {
        if ( ! ($listener instanceof Propel_EventListener_Interface) &&
             ! ($listener instanceof Propel_Overloadable)) {
            
            throw new Propel_EventListener_Exception("Couldn't add eventlistener. EventListeners should implement either Propel_EventListener_Interface or Propel_Overloadable");
        }
        if ($name === null) {
            $this->_listeners[] = $listener;
        } else {
            $this->_listeners[$name] = $listener;
        }
    }

    /**
     * returns a Propel_EventListener on success
     * and null on failure
     *
     * @param mixed $key
     * @return mixed
     */
    public function get($key)
    {
        if ( ! isset($this->_listeners[$key])) {
            return null;
        }
        return $this->_listeners[$key];
    }

    /**
     * set
     *
     * @param mixed $key
     * @param Propel_EventListener $listener
     * @return void
     */
    public function set($key, $listener)
    {
        $this->_listeners[$key] = $listener;
    }

    /**
     * onLoad
     * an event invoked when Propel_Record is being loaded from database
     *
     * @param Propel_Record $record
     * @return void
     */
    public function onLoad(Propel_Record $record)
    {
        foreach ($this->_listeners as $listener) {
            $listener->onLoad($record);
        }
    }

    /**
     * onPreLoad
     * an event invoked when Propel_Record is being loaded
     * from database but not yet initialized
     *
     * @param Propel_Record $record
     * @return void
     */
    public function onPreLoad(Propel_Record $record)
    {
        foreach ($this->_listeners as $listener) {
            $listener->onPreLoad($record);
        }
    }

    /**
     * onSleep
     * an event invoked when Propel_Record is serialized
     *
     * @param Propel_Record $record
     * @return void
     */
    public function onSleep(Propel_Record $record)
    {
        foreach ($this->_listeners as $listener) {
            $listener->onSleep($record);
        }
    }

    /**
     * onWakeUp
     * an event invoked when Propel_Record is unserialized
     *
     * @param Propel_Record $record
     * @return void
     */
    public function onWakeUp(Propel_Record $record)
    {
        foreach ($this->_listeners as $listener) {
            $listener->onWakeUp($record);
        }
    }

    /**
     * postClose
     * an event invoked after Propel_Connection is closed
     *
     * @param Propel_Event $event
     * @return void
     */
    public function postClose(Propel_Event $event)
    {
        foreach ($this->_listeners as $listener) {
            $listener->postClose($event);
        }
    }

    /**
     * preClose
     * an event invoked before Propel_Connection is closed
     *
     * @param Propel_Event $event
     * @return void
     */
    public function preClose(Propel_Event $event)
    {
        foreach ($this->_listeners as $listener) {
            $listener->preClose($event);
        }
    }

    /**
     * onOpen
     * an event invoked after Propel_Connection is opened
     *
     * @param Propel_Connection $connection
     * @return void
     */
    public function onOpen(Propel_Connection $connection)
    {
        foreach ($this->_listeners as $listener) {
            $listener->onOpen($connection);
        }
    }

    /**
     * onTransactionCommit
     * an event invoked after a Propel_Connection transaction is committed
     *
     * @param Propel_Event $event
     * @return void
     */
    public function postTransactionCommit(Propel_Event $event)
    {
        foreach ($this->_listeners as $listener) {
            $listener->postTransactionCommit($event);
        }
    }

    /**
     * onPreTransactionCommit
     * an event invoked before a Propel_Connection transaction is committed
     *
     * @param Propel_Event $event
     * @return void
     */
    public function preTransactionCommit(Propel_Event $event)
    {
        foreach ($this->_listeners as $listener) {
            $listener->preTransactionCommit($event);
        }
    }

    /**
     * onTransactionRollback
     * an event invoked after a Propel_Connection transaction is being rolled back
     *
     * @param Propel_Event $event
     * @return void
     */
    public function postTransactionRollback(Propel_Event $event)
    {
        foreach ($this->_listeners as $listener) {
            $listener->postTransactionRollback($event);
        }
    }

    /**
     * onPreTransactionRollback
     * an event invoked before a Propel_Connection transaction is being rolled back
     *
     * @param Propel_Event $event
     * @return void
     */
    public function preTransactionRollback(Propel_Event $event)
    {
        foreach ($this->_listeners as $listener) {
            $listener->preTransactionRollback($event);
        }
    }

    /**
     * onTransactionBegin
     * an event invoked after a Propel_Connection transaction has been started
     *
     * @param Propel_Event $event
     * @return void
     */
    public function postTransactionBegin(Propel_Event $event)
    {
        foreach ($this->_listeners as $listener) {
            $listener->postTransactionBegin($event);
        }
    }

    /**
     * onTransactionBegin
     * an event invoked before a Propel_Connection transaction is being started
     *
     * @param Propel_Event $event
     * @return void
     */
    public function preTransactionBegin(Propel_Event $event)
    {
        foreach ($this->_listeners as $listener) {
            $listener->preTransactionBegin($event);
        }
    }

    /**
     * postSavepointCommit
     * an event invoked after a Propel_Connection transaction with savepoint
     * is committed
     *
     * @param Propel_Event $event
     * @return void
     */
    public function postSavepointCommit(Propel_Event $event)
    {
        foreach ($this->_listeners as $listener) {
            $listener->postSavepointCommit($event);
        }
    }

    /**
     * preSavepointCommit
     * an event invoked before a Propel_Connection transaction with savepoint
     * is committed
     *
     * @param Propel_Event $event
     * @return void
     */
    public function preSavepointCommit(Propel_Event $event)
    {
        foreach ($this->_listeners as $listener) {
            $listener->preSavepointCommit($event);
        }
    }

    /**
     * postSavepointRollback
     * an event invoked after a Propel_Connection transaction with savepoint
     * is being rolled back
     *
     * @param Propel_Event $event
     * @return void
     */
    public function postSavepointRollback(Propel_Event $event)
    {
        foreach ($this->_listeners as $listener) {
            $listener->postSavepointRollback($event);
        }
    }

    /**
     * preSavepointRollback
     * an event invoked before a Propel_Connection transaction with savepoint
     * is being rolled back
     *
     * @param Propel_Event $event
     * @return void
     */
    public function preSavepointRollback(Propel_Event $event)
    {
        foreach ($this->_listeners as $listener) {
            $listener->preSavepointRollback($event);
        }
    }

    /**
     * postSavepointCreate
     * an event invoked after a Propel_Connection transaction with savepoint
     * has been started
     *
     * @param Propel_Event $event
     * @return void
     */
    public function postSavepointCreate(Propel_Event $event)
    {
        foreach ($this->_listeners as $listener) {
            $listener->postSavepointCreate($event);
        }
    }

    /**
     * preSavepointCreate
     * an event invoked before a Propel_Connection transaction with savepoint
     * is being started
     *
     * @param Propel_Event $event
     * @return void
     */
    public function preSavepointCreate(Propel_Event $event)
    {
        foreach ($this->_listeners as $listener) {
            $listener->preSavepointCreate($event);
        }
    }
    // @end

    /**
     * onCollectionDelete
     * an event invoked after a Propel_Collection is being deleted
     *
     * @param Propel_Collection $collection
     * @return void
     */
    public function onCollectionDelete(Propel_Collection $collection)
    {
        foreach ($this->_listeners as $listener) {
            $listener->onCollectionDelete($collection);
        }
    }

    /**
     * onCollectionDelete
     * an event invoked after a Propel_Collection is being deleted
     *
     * @param Propel_Collection $collection
     * @return void
     */
    public function onPreCollectionDelete(Propel_Collection $collection)
    {
        foreach ($this->_listeners as $listener) {
            $listener->onPreCollectionDelete($collection);
        }
    }

    public function postConnect(Propel_Event $event)
    {
        foreach ($this->_listeners as $listener) {
            $listener->postConnect($event);
        }
    }

    public function preConnect(Propel_Event $event)
    {
        foreach ($this->_listeners as $listener) {
            $listener->preConnect($event);
        }
    }

    public function preQuery(Propel_Event $event)
    { 
        foreach ($this->_listeners as $listener) {
            $listener->preQuery($event);
        }
    }

    public function postQuery(Propel_Event $event)
    {
        foreach ($this->_listeners as $listener) {
            $listener->postQuery($event);
        }
    }

    public function prePrepare(Propel_Event $event)
    { 
        foreach ($this->_listeners as $listener) {
            $listener->prePrepare($event);
        }
    }

    public function postPrepare(Propel_Event $event)
    {
        foreach ($this->_listeners as $listener) {
            $listener->postPrepare($event);
        }
    }

    public function preExec(Propel_Event $event)
    {
        foreach ($this->_listeners as $listener) {
            $listener->preExec($event);
        }
    }

    public function postExec(Propel_Event $event)
    {
        foreach ($this->_listeners as $listener) {
            $listener->postExec($event);
        }
    }

    public function preError(Propel_Event $event)
    { 
        foreach ($this->_listeners as $listener) {
            $listener->preError($event);
        }
    }

    public function postError(Propel_Event $event)
    {
        foreach ($this->_listeners as $listener) {
            $listener->postError($event);
        }
    }

    public function preFetch(Propel_Event $event)
    { 
        foreach ($this->_listeners as $listener) {
            $listener->preFetch($event);
        }
    }

    public function postFetch(Propel_Event $event)
    {
        foreach ($this->_listeners as $listener) {
            $listener->postFetch($event);
        }
    }

    public function preFetchAll(Propel_Event $event)
    { 
        foreach ($this->_listeners as $listener) {
            $listener->preFetchAll($event);
        }
    }

    public function postFetchAll(Propel_Event $event)
    {
        foreach ($this->_listeners as $listener) {
            $listener->postFetchAll($event);
        }
    }

    public function preStmtExecute(Propel_Event $event)
    {
        foreach ($this->_listeners as $listener) {
            $listener->preStmtExecute($event);
        }
    }

    public function postStmtExecute(Propel_Event $event)
    {
        foreach ($this->_listeners as $listener) {
            $listener->postStmtExecute($event);
        }
    }
}