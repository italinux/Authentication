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
 * Propel_Pager_Range
 *
 * @author      Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @package     Propel
 * @subpackage  Pager
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version     $Revision$
 * @link        www.propel-project.org
 * @since       0.9
 */
abstract class Propel_Pager_Range
{
    /**
     * @var array $_options     Custom Propel_Pager_Range implementation options
     */
    protected $_options;

    /**
     * @var Propel_Pager $pager     Propel_Pager object related to the pager range
     */
    private $pager;


    /**
     * __construct
     *
     * @param array $options     Custom subclass implementation options.
     *                           Default is a blank array
     * @param Propel_Pager $pager     Optional Propel_Pager object to be associated
     * @return void
     */
    final public function __construct($options = array(), $pager = null)
    {
        $this->_setOptions($options);

        if ($pager !== null) {
            $this->setPager($pager);
        }
    }

    /**
     * getPager
     *
     * Returns the Propel_Pager object related to the pager range
     *
     * @return Propel_Pager        Propel_Pager object related to the pager range
     */
    public function getPager()
    {
        return $this->pager;
    }

    /**
     * setPager
     *
     * Defines the Propel_Pager object related to the pager range and
     * automatically (re-)initialize Propel_Pager_Range
     *
     * @param $pager       Propel_Pager object related to the pager range
     * @return void
     */
    public function setPager($pager)
    {
        $this->pager = $pager;

        // Lazy-load initialization. It only should be called when all
        // needed information data is ready (this can only happens when we have
        // options stored and a Propel_Pager assocated)
        $this->_initialize();
    }

    /**
     * getOptions
     *
     * Returns the custom Propel_Pager_Range implementation options
     *
     * @return array        Custom Propel_Pager_Range implementation options
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * getOption
     *
     * Returns the custom Propel_Pager_Range implementation offset option
     *
     * @return array        Custom Propel_Pager_Range implementation options
     */
    public function getOption($option)
    {
        if (isset($this->_options[$option])) {
            return $this->_options[$option];
        }

        throw new Propel_Pager_Exception(
            'Cannot access unexistent option \'' . $option . '\' in Propel_Pager_Range class'
        );
    }

    /**
     * _setOptions
     *
     * Defines the subclass implementation options
     *
     * @param $options       Custom Propel_Pager_Range implementation options
     * @return void
     */
    protected function _setOptions($options)
    {
        $this->_options = $options;
    }

    /**
     * isInRange
     *
     * Check if a given page is in the range
     *
     * @param $page       Page to be checked
     * @return boolean
     */
    public function isInRange($page)
    {
        return (array_search($page, $this->rangeAroundPage()) !== false);
    }



    /**
     * _initialize
     *
     * Initialize Propel_Page_Range subclass which does custom class definitions
     *
     * @return void
     */
    abstract protected function _initialize();


    /**
     * rangeAroundPage
     *
     * Calculate and returns an array representing the range around the current page
     *
     * @return array
     */
    abstract public function rangeAroundPage();
}