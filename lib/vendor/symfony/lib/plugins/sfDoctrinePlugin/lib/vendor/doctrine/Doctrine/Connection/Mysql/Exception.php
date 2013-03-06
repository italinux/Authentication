<?php
/*
 *  $Id: Exception.php 7490 2010-03-29 19:53:27Z jwage $
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
 * Propel_Connection_Mysql_Exception
 *
 * @package     Propel
 * @subpackage  Connection
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @author      Lukas Smith <smith@pooteeweet.org> (PEAR MDB2 library)
 * @since       1.0
 * @version     $Revision: 7490 $
 * @link        www.propel-project.org
 */
class Propel_Connection_Mysql_Exception extends Propel_Connection_Exception
{
    /**
     * @var array $errorCodeMap         an array that is used for determining portable
     *                                  error code from a native database error code
     */
    protected static $errorCodeMap = array(
                                      1004 => Propel_Core::ERR_CANNOT_CREATE,
                                      1005 => Propel_Core::ERR_CANNOT_CREATE,
                                      1006 => Propel_Core::ERR_CANNOT_CREATE,
                                      1007 => Propel_Core::ERR_ALREADY_EXISTS,
                                      1008 => Propel_Core::ERR_CANNOT_DROP,
                                      1022 => Propel_Core::ERR_ALREADY_EXISTS,
                                      1044 => Propel_Core::ERR_ACCESS_VIOLATION,
                                      1046 => Propel_Core::ERR_NODBSELECTED,
                                      1048 => Propel_Core::ERR_CONSTRAINT,
                                      1049 => Propel_Core::ERR_NOSUCHDB,
                                      1050 => Propel_Core::ERR_ALREADY_EXISTS,
                                      1051 => Propel_Core::ERR_NOSUCHTABLE,
                                      1054 => Propel_Core::ERR_NOSUCHFIELD,
                                      1061 => Propel_Core::ERR_ALREADY_EXISTS,
                                      1062 => Propel_Core::ERR_ALREADY_EXISTS,
                                      1064 => Propel_Core::ERR_SYNTAX,
                                      1091 => Propel_Core::ERR_NOT_FOUND,
                                      1100 => Propel_Core::ERR_NOT_LOCKED,
                                      1136 => Propel_Core::ERR_VALUE_COUNT_ON_ROW,
                                      1142 => Propel_Core::ERR_ACCESS_VIOLATION,
                                      1146 => Propel_Core::ERR_NOSUCHTABLE,
                                      1216 => Propel_Core::ERR_CONSTRAINT,
                                      1217 => Propel_Core::ERR_CONSTRAINT,
                                      1451 => Propel_Core::ERR_CONSTRAINT,
                                      );

    /**
     * This method checks if native error code/message can be
     * converted into a portable code and then adds this
     * portable error code to $portableCode field
     *
     * @param array $errorInfo      error info array
     * @since 1.0
     * @return boolean              whether or not the error info processing was successfull
     *                              (the process is successfull if portable error code was found)
     */
    public function processErrorInfo(array $errorInfo)
    {
        $code = $errorInfo[1];
        if (isset(self::$errorCodeMap[$code])) {
            $this->portableCode = self::$errorCodeMap[$code];
            return true;
        }
        return false;
    }
}