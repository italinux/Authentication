<?php
/*
 *  $Id: Compiler.php 7490 2010-03-29 19:53:27Z jwage $
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
 * Propel_Compiler
 * This class can be used for compiling the entire Propel framework into a single file
 *
 * @package     Propel
 * @subpackage  Compiler
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @license     http://www.opensource.org/licenses/lgpllicense.php LGPL
 * @link        www.phppropel.
 * @since       1.0
 * @version     $Revision: 7490 $
 */
class Propel_Compiler
{
    /**
     * method for making a single file of most used propel runtime components
     * including the compiled file instead of multiple files (in worst
     * cases dozens of files) can improve performance by an order of magnitude
     *
     * @throws Propel_Compiler_Exception      if something went wrong during the compile operation
     * @return $target Path the compiled file was written to
     */
    public static function compile($target = null, $includedDrivers = array())
    {
        if ( ! is_array($includedDrivers)) {
            $includedDrivers = array($includedDrivers);
        }
        
        $excludedDrivers = array();
        
        // If we have an array of specified drivers then lets determine which drivers we should exclude
        if ( ! empty($includedDrivers)) {
            $drivers = array('db2',
                             'mssql',
                             'mysql',
                             'oracle',
                             'pgsql',
                             'sqlite');
            
            $excludedDrivers = array_diff($drivers, $includedDrivers);
        }
        
        $path = Propel_Core::getPath();
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path . '/Propel'), RecursiveIteratorIterator::LEAVES_ONLY);

        foreach ($it as $file) {
            $e = explode('.', $file->getFileName());
            
            //@todo what is a versioning file? do we have these anymore? None 
            //exists in my version of propel from svn.
            // we don't want to require versioning files
            if (end($e) === 'php' && strpos($file->getFileName(), '.inc') === false) {
                require_once $file->getPathName();
            }
        }

        $classes = array_merge(get_declared_classes(), get_declared_interfaces());

        $ret     = array();

        foreach ($classes as $class) {
            $e = explode('_', $class);

            if ($e[0] !== 'Propel') {
                continue;
            }
            
            // Exclude drivers
            if ( ! empty($excludedDrivers)) {
                foreach ($excludedDrivers as $excludedDriver) {
                    $excludedDriver = ucfirst($excludedDriver);
                    
                    if (in_array($excludedDriver, $e)) {
                        continue(2);
                    }
                }
            }
            
            $refl  = new ReflectionClass($class);
            $file  = $refl->getFileName();
            
            $lines = file($file);

            $start = $refl->getStartLine() - 1;
            $end   = $refl->getEndLine();

            $ret = array_merge($ret, array_slice($lines, $start, ($end - $start)));
        }

        if ($target == null) {
            $target = $path . DIRECTORY_SEPARATOR . 'Propel.compiled.php';
        }

        // first write the 'compiled' data to a text file, so
        // that we can use php_strip_whitespace (which only works on files)
        $fp = @fopen($target, 'w');

        if ($fp === false) {
            throw new Propel_Compiler_Exception("Couldn't write compiled data. Failed to open $target");
        }
        
        fwrite($fp, "<?php ". implode('', $ret));
        fclose($fp);

        $stripped = php_strip_whitespace($target);
        $fp = @fopen($target, 'w');
        if ($fp === false) {
            throw new Propel_Compiler_Exception("Couldn't write compiled data. Failed to open $file");
        }
        
        fwrite($fp, $stripped);
        fclose($fp);

        return $target;
    }
}