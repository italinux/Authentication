<?php
/*
 *  $Id: PostOrderIterator.php 7490 2010-03-29 19:53:27Z jwage $
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
 * Propel_Node_MaterializedPath_PostOrderIterator
 *
 * @package     Propel
 * @subpackage  Node
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.propel-project.org
 * @since       1.0
 * @version     $Revision: 7490 $
 * @author      Joe Simms <joe.simms@websites4.com>
 */
class Propel_Node_MaterializedPath_PostOrderIterator implements Iterator
{
    private $topNode = null;

    private $curNode = null;

    public function __construct($node, $opts)
    {
        throw new Propel_Exception('Not yet implemented');
    }

    public function rewind()
    {
        throw new Propel_Exception('Not yet implemented');
    }

    public function valid()
    {
        throw new Propel_Exception('Not yet implemented');
    }

    public function current()
    {
        throw new Propel_Exception('Not yet implemented');
    }

    public function key()
    {
        throw new Propel_Exception('Not yet implemented');
    }

    public function next()
    {
        throw new Propel_Exception('Not yet implemented');
    }
}