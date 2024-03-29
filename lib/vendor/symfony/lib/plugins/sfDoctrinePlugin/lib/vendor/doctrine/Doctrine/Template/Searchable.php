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
 * Propel_Template_Searchable
 *
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @package     Propel
 * @subpackage  Template
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version     $Revision$
 * @link        www.propel-project.org
 * @since       1.0
 */
class Propel_Template_Searchable extends Propel_Template
{
    /**
     * __construct
     *
     * @param array $options 
     * @return void
     */
    public function __construct(array $options = array())
    {
	      parent::__construct($options);
        $this->_plugin = new Propel_Search($this->_options); 
    }

    /**
     * Setup the Searchable template behavior
     *
     * @return void
     */
    public function setUp()
    {
        $this->_plugin->initialize($this->_table);

        $this->addListener(new Propel_Search_Listener($this->_plugin));
    }

    /**
     * Make the batchUpdateIndex() function available to the template so Propel_Record child classes
     * with the behavior enabled can all the function
     *
     * @param integer $limit 
     * @param integer $offset 
     * @return void
     */
    public function batchUpdateIndex($limit = null, $offset = null, $encoding = null)
    {
        $this->_plugin->batchUpdateIndex($limit, $offset, $encoding);
    }

    /**
     * Proxy method so the batch updating can be called from table classes
     *
     * @param integer $limit 
     * @param integer $offset 
     * @return void
     */
    public function batchUpdateIndexTableProxy($limit = null, $offset = null, $encoding = null)
    {
        $this->batchUpdateIndex($limit, $offset, $encoding);
    }

    /**
     * Searchable keyword search proxy for Propel_Table
     * 
     * @param string $string Keyword string to search for
     * @param Propel_Query $query Query object to alter. Adds where condition to limit the results using the search index
     * @return array    ids and relevancy
     */
    public function searchTableProxy($string, $query = null)
    {
        return $this->_plugin->search($string, $query);
    }
}
