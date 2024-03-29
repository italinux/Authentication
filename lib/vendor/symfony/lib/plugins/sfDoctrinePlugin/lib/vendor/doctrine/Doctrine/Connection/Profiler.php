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
 * Propel_Connection_Profiler
 *
 * @package     Propel
 * @subpackage  Connection
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.propel-project.org
 * @since       1.0
 * @version     $Revision$
 */
class Propel_Connection_Profiler implements Propel_Overloadable, IteratorAggregate, Countable
{
    /**
     * @param array $listeners      an array containing all availible listeners
     */
    private $listeners  = array('query',
                                'prepare',
                                'commit',
                                'rollback',
                                'connect',
                                'begintransaction',
                                'exec',
                                'execute');

    /**
     * @param array $events         an array containing all listened events
     */
    private $events     = array();

    /**
     * @param array $eventSequences         an array containing sequences of all listened events as keys
     */
    private $eventSequences = array();

    /**
     * constructor
     */
    public function __construct() {

    }

    /**
     * setFilterQueryType
     *
     * @param integer $filter
     * @return boolean
     */
    public function setFilterQueryType() {
                                             
    }                                         
    /**
     * method overloader
     * this method is used for invoking different listeners, for the full
     * list of availible listeners, see Propel_EventListener
     *
     * @param string $m     the name of the method
     * @param array $a      method arguments
     * @see Propel_EventListener
     * @return boolean
     */
    public function __call($m, $a)
    {
        // first argument should be an instance of Propel_Event
        if ( ! ($a[0] instanceof Propel_Event)) {
            throw new Propel_Connection_Profiler_Exception("Couldn't listen event. Event should be an instance of Propel_Event.");
        }


        if (substr($m, 0, 3) === 'pre') {
            // pre-event listener found
            $a[0]->start();

            $eventSequence = $a[0]->getSequence();
            if ( ! isset($this->eventSequences[$eventSequence])) {
                $this->events[] = $a[0];
                $this->eventSequences[$eventSequence] = true;
            }
        } else {
            // after-event listener found
            $a[0]->end();
        }
    }

    /**
     * get
     *
     * @param mixed $key
     * @return Propel_Event
     */
    public function get($key) 
    {
        if (isset($this->events[$key])) {
            return $this->events[$key];
        }
        return null;
    }

    /**
     * getAll
     * returns all profiled events as an array
     *
     * @return array        all events in an array
     */
    public function getAll() 
    {
        return $this->events;
    }

    /**
     * getIterator
     * returns an iterator that iterates through the logged events
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->events);
    }

    /**
     * count
     * 
     * @return integer
     */
    public function count() 
    {
        return count($this->events);
    }

    /**
     * pop the last event from the event stack
     *
     * @return Propel_Event
     */
    public function pop() 
    {
        $event = array_pop($this->events);
        if ($event !== null)
        {
            unset($this->eventSequences[$event->getSequence()]);
        }
        return $event;
    }

    /**
     * Get the Propel_Event object for the last query that was run, regardless if it has
     * ended or not. If the event has not ended, it's end time will be Null.
     *
     * @return Propel_Event
     */
    public function lastEvent()
    {
        if (empty($this->events)) {
            return false;
        }

        end($this->events);
        return current($this->events);
    }
}