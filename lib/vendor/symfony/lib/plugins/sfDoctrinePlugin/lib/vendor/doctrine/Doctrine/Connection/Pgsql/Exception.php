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
 * Propel_Connection_Pgsql_Exception
 *
 * @package     Propel
 * @subpackage  Connection
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.propel-project.org
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @author      Paul Cooper <pgc@ucecom.com> (PEAR MDB2 Pgsql driver)
 * @author      Lukas Smith <smith@pooteeweet.org> (PEAR MDB2 library)
 * @since       1.0
 * @version     $Revision: 7490 $
 */
class Propel_Connection_Pgsql_Exception extends Propel_Connection_Exception
{
    /**
     * @var array $errorRegexps         an array that is used for determining portable
     *                                  error code from a native database error message
     */
    protected static $errorRegexps = array(
                                    '/parser: parse error at or near/i'
                                        => Propel_Core::ERR_SYNTAX,
                                    '/syntax error at/'
                                        => Propel_Core::ERR_SYNTAX,
                                    '/column reference .* is ambiguous/i'
                                        => Propel_Core::ERR_SYNTAX,
                                    '/column .* (of relation .*)?does not exist/i'
                                        => Propel_Core::ERR_NOSUCHFIELD,
                                    '/attribute .* not found|relation .* does not have attribute/i'
                                        => Propel_Core::ERR_NOSUCHFIELD,
                                    '/column .* specified in USING clause does not exist in (left|right) table/i'
                                        => Propel_Core::ERR_NOSUCHFIELD,
                                    '/(relation|sequence|table).*does not exist|class .* not found/i'
                                        => Propel_Core::ERR_NOSUCHTABLE,
                                    '/index .* does not exist/'
                                        => Propel_Core::ERR_NOT_FOUND,
                                    '/relation .* already exists/i'
                                        => Propel_Core::ERR_ALREADY_EXISTS,
                                    '/(divide|division) by zero$/i'
                                        => Propel_Core::ERR_DIVZERO,
                                    '/pg_atoi: error in .*: can\'t parse /i'
                                        => Propel_Core::ERR_INVALID_NUMBER,
                                    '/invalid input syntax for( type)? (integer|numeric)/i'
                                        => Propel_Core::ERR_INVALID_NUMBER,
                                    '/value .* is out of range for type \w*int/i'
                                        => Propel_Core::ERR_INVALID_NUMBER,
                                    '/integer out of range/i'
                                        => Propel_Core::ERR_INVALID_NUMBER,
                                    '/value too long for type character/i'
                                        => Propel_Core::ERR_INVALID,
                                    '/permission denied/'
                                        => Propel_Core::ERR_ACCESS_VIOLATION,
                                    '/violates [\w ]+ constraint/'
                                        => Propel_Core::ERR_CONSTRAINT,
                                    '/referential integrity violation/'
                                        => Propel_Core::ERR_CONSTRAINT,
                                    '/violates not-null constraint/'
                                        => Propel_Core::ERR_CONSTRAINT_NOT_NULL,
                                    '/more expressions than target columns/i'
                                        => Propel_Core::ERR_VALUE_COUNT_ON_ROW,
                                );

    /**
     * This method checks if native error code/message can be
     * converted into a portable code and then adds this
     * portable error code to $portableCode field
     *
     * the portable error code is added at the end of array
     *
     * @param array $errorInfo      error info array
     * @since 1.0
     * @see Propel_Core::ERR_* constants
     * @see Propel_Connection::$portableCode
     * @return boolean              whether or not the error info processing was successfull
     *                              (the process is successfull if portable error code was found)
     */
    public function processErrorInfo(array $errorInfo)
    {
        foreach (self::$errorRegexps as $regexp => $code) {
            if (preg_match($regexp, $errorInfo[2])) {
                $this->portableCode = $code;
                return true;
            }
        }
        return false;
    }
}