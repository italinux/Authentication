<?php
/*
 *  $Id: Node.php 7490 2010-03-29 19:53:27Z jwage $
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
 * Propel_Node
 *
 * @package     Propel
 * @subpackage  Node
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.propel-project.org
 * @since       1.0
 * @version     $Revision: 7490 $
 * @author      Joe Simms <joe.simms@websites4.com>
 */
class Propel_Node implements IteratorAggregate
{
    /**
     * @param object    $record   reference to associated Propel_Record instance
     */
    protected $record;

    /**
     * @param array     $options
     */
    protected $options;

    /**
     * @param string     $iteratorType  (Pre | Post | Level)
     */
    protected $iteratorType;

    /**
     * @param array     $iteratorOptions
     */
    protected $iteratorOptions;

    /**
     * The tree to which the node belongs.
     *
     * @var unknown_type
     */
    protected $_tree;

    /**
     * contructor, creates node with reference to record and any options
     *
     * @param object $record                    instance of Propel_Record
     * @param array $options                    options
     */
    public function __construct(Propel_Record $record, $options)
    {
        $this->record = $record;
        $this->options = $options;
        
        // Make sure that the tree object of the root class is used in the case
        // of column aggregation inheritance (single table inheritance).
        $class = $record->getTable()->getComponentName();
        $thisTable = $record->getTable();
        $table = $thisTable;
        if ($thisTable->getOption('inheritanceMap')) {
            // Move up the hierarchy until we find the "subclasses" option. This option
            // MUST be set on the root class of the user's hierarchy that uses STI.
            while ( ! $subclasses = $table->getOption('subclasses')) {
                $class = get_parent_class($class);
                $reflectionClass = new ReflectionClass($class);
                if ($reflectionClass->isAbstract()) {
                    continue;
                }
                if ($class == 'Propel_Record') {
                    throw new Propel_Node_Exception("No subclasses specified. You are "
                            . "using Single Table Inheritance with NestedSet but you have "
                            . "not specified the subclasses correctly. Make sure you use "
                            . "setSubclasses() in the root class of your hierarchy.");
                }
                $table = $table->getConnection()->getTable($class);
            }
        }
        if ($thisTable !== $table) {
            $this->_tree = $table->getTree();
        } else {
            $this->_tree = $thisTable->getTree();
        }
    }

    /**
     * Factory method for creating a Node.
     *
     * This is a factory method that returns node instance based upon chosen
     * implementation.
     *
     * @param object $record                    instance of Propel_Record
     * @param string $implName                  implementation (NestedSet, AdjacencyList, MaterializedPath)
     * @param array $options                    options
     * @return Propel_Node
     * @throws Propel_Node_Exception          if $implName is not a valid class
     */
    public static function factory(Propel_Record $record, $implName, $options = array())
    {
        $class = 'Propel_Node_' . $implName;

        if ( ! class_exists($class)) {
            throw new Propel_Node_Exception("The class $class must exist and extend Propel_Node");
        }

        return new $class($record, $options);
    }

    /**
     * setter for record attribute
     *
     * @param object $record                    instance of Propel_Record
     */
    public function setRecord(Propel_Record $record)
    {
        $this->record = $record;
    }

    /**
     * getter for record attribute
     *
     * @return Propel_Record
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * convenience function for getIterator
     *
     * @param string $type                      type of iterator (Pre | Post | Level)
     * @param array $options                    options
     */
    public function traverse($type = 'Pre', $options = array())
    {
        return $this->getIterator($type, $options);
    }

    /**
     * get iterator
     *
     * @param string $type                      type of iterator (Pre | Post | Level)
     * @param array $options                    options
     */
    public function getIterator($type = null, $options = null)
    {
        if ($type === null) {
            $type = (isset($this->iteratorType) ? $this->iteratorType : 'Pre');
        }

        if ($options === null) {
            $options = (isset($this->iteratorOptions) ? $this->iteratorOptions : array());
        }

        $implName = $this->record->getTable()->getOption('treeImpl');
        $iteratorClass = 'Propel_Node_' . $implName . '_' . ucfirst(strtolower($type)) . 'OrderIterator';

        return new $iteratorClass($this->record, $options);
    }

    /**
     * sets node's iterator type
     *
     * @param int
     */
    public function setIteratorType($type)
    {
        $this->iteratorType = $type;
    }

    /**
     * sets node's iterator options
     *
     * @param int
     */
    public function setIteratorOptions($options)
    {
        $this->iteratorOptions = $options;
    }
}
