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
 * Propel_Search_Indexer
 *
 * @package     Propel
 * @subpackage  Search
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version     $Revision$
 * @link        www.propel-project.org
 * @since       1.0
 */
class Propel_Search_Indexer
{
    public function indexDirectory($dir)
    {
        if ( ! file_exists($dir)) {
           throw new Propel_Search_Indexer_Exception('Unknown directory ' . $dir);
        }

        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::LEAVES_ONLY);

        $files = array();
        foreach ($it as $file) {
            $name = $file->getPathName();
            if (strpos($name, '.svn') === false) {
                $files[] = $name;
            }
        }

        $q = Propel_Core::getTable('Propel_File')
            ->createQuery('f')
            ->delete()
            ->where('f.url LIKE ?', array($dir . '%'))
            ->execute();

        // clear the index
        $q = Propel_Core::getTable('Propel_File_Index')
            ->createQuery('i')
            ->where('i.file_id = ?')
            ->execute();

        $coll = Propel_Collection::create('Propel_File');

        foreach ($files as $file) {
            $coll[]->url = $file;
        }
        
        $coll->save();
    }
}