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
 * Propel_Query_Filter_Chain
 *
 * @package     Propel
 * @subpackage  Query
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.propel-project.org
 * @since       1.0
 * @version     $Revision$
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 */
class Propel_Query_Filter_Chain
{
    /**
     * @var array $_filters         an array of Propel_Query_Filter objects
     */
    protected $_filters = array();

    /**
     * add
     *
     * @param Propel_Query_Filter $filter
     * @return void
     */
    public function add(Propel_Query_Filter $filter)
    {
        $this->_filters[] = $filter;
    }

    /**
     * returns a Propel_Query_Filter on success
     * and null on failure
     *
     * @param mixed $key
     * @return mixed
     */
    public function get($key)
    {
        if ( ! isset($this->_filters[$key])) {
            throw new Propel_Query_Exception('Unknown filter ' . $key);
        }
        return $this->_filters[$key];
    }

    /**
     * set
     *
     * @param mixed $key
     * @param Propel_Query_Filter $listener
     * @return void
     */
    public function set($key, Propel_Query_Filter $listener)
    {
        $this->_filters[$key] = $listener;
    }

    /**
     * preQuery
     *
     * Method for listening the preQuery method of Propel_Query and
     * hooking into the query building procedure, doing any custom / specialized
     * query building procedures that are neccessary.
     *
     * @return void
     */
    public function preQuery(Propel_Query $query)
    {
        foreach ($this->_filters as $filter) {
            $filter->preQuery($query);
        }
    }

    /**
     * postQuery
     *
     * Method for listening the postQuery method of Propel_Query and
     * to hook into the query building procedure, doing any custom / specialized
     * post query procedures (for example logging) that are neccessary.
     *
     * @return void
     */
    public function postQuery(Propel_Query $query)
    {
        foreach ($this->_filters as $filter) {
            $filter->postQuery($query);
        }
    }
}