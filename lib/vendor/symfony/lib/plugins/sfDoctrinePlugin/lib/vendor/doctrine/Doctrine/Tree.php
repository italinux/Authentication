<?php
/*
 *  $Id: Tree.php 7490 2010-03-29 19:53:27Z jwage $
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
 * Propel_Tree
 *
 * @package     Propel
 * @subpackage  Tree
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.propel-project.org
 * @since       1.0
 * @version     $Revision: 7490 $
 * @author      Joe Simms <joe.simms@websites4.com>
 */
class Propel_Tree
{
    /**
     * @param object $table   reference to associated Propel_Table instance
     */
    protected $table;

    /**
     * @param array $options
     */
    protected $options = array();
    
    protected $_baseComponent;

    /**
     * constructor, creates tree with reference to table and any options
     *
     * @param object $table                     instance of Propel_Table
     * @param array $options                    options
     */
    public function __construct(Propel_Table $table, $options)
    {
        $this->table = $table;
        $this->options = $options;
        $this->_baseComponent = $table->getComponentName();
        $class = $this->_baseComponent;
        if ($table->getOption('inheritanceMap')) {
            $subclasses = $table->getOption('subclasses');
            while (in_array($class, $subclasses)) {
                $class = get_parent_class($class);
            }
            $this->_baseComponent = $class;
        }
        //echo $this->_baseComponent;
    }

    /**
     * Used to define table attributes required for the given implementation
     *
     * @throws Propel_Tree_Exception          if table attributes have not been defined
     */
    public function setTableDefinition()
    {
        throw new Propel_Tree_Exception('Table attributes have not been defined for this Tree implementation.');
    }

    /**
     * this method is used for setting up relations and attributes and should be used by specific implementations
     *
     */
    public function setUp()
    {
    }

    /**
     * Factory method to create a Tree.
     *
     * This is a factory method that returns a tree instance based upon 
     * chosen implementation.
     *
     * @param object $table                     instance of Propel_Table
     * @param string $impName                   implementation (NestedSet, AdjacencyList, MaterializedPath)
     * @param array $options                    options
     * @return Propel_Tree
     * @throws Propel_Exception               if class $implName does not extend Propel_Tree
     */
    public static function factory(Propel_Table $table, $implName, $options = array())
    {
        $class = 'Propel_Tree_' . $implName;
        if ( ! class_exists($class)) {
            throw new Propel_Exception('The chosen class must extend Propel_Tree');
        }
        return new $class($table, $options);
    }

    /**
     * gets tree attribute value
     *        
     */     
    public function getAttribute($name)
    {
      return isset($this->options[$name]) ? $this->options[$name] : null;
    }

    /**
     * sets tree attribute value
     *
     * @param mixed            
     */
    public function setAttribute($name, $value)
    {
      $this->options[$name] = $value;
    }

    /**
     * Returns the base tree component.
     */
    public function getBaseComponent()
    {
        return $this->_baseComponent;
    }
}
