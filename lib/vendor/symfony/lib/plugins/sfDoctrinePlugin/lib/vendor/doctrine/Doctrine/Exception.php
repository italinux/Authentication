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
 * Propel_Exception
 *
 * @package     Propel
 * @subpackage  Exception
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.propel-project.org
 * @since       1.0
 * @version     $Revision: 7490 $
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 */
class Propel_Exception extends Exception
{ 
    /**
     * @var array $_errorMessages       an array of error messages
     */
    protected static $_errorMessages = array(
                Propel_Core::ERR                    => 'unknown error',
                Propel_Core::ERR_ALREADY_EXISTS     => 'already exists',
                Propel_Core::ERR_CANNOT_CREATE      => 'can not create',
                Propel_Core::ERR_CANNOT_ALTER       => 'can not alter',
                Propel_Core::ERR_CANNOT_REPLACE     => 'can not replace',
                Propel_Core::ERR_CANNOT_DELETE      => 'can not delete',
                Propel_Core::ERR_CANNOT_DROP        => 'can not drop',
                Propel_Core::ERR_CONSTRAINT         => 'constraint violation',
                Propel_Core::ERR_CONSTRAINT_NOT_NULL=> 'null value violates not-null constraint',
                Propel_Core::ERR_DIVZERO            => 'division by zero',
                Propel_Core::ERR_INVALID            => 'invalid',
                Propel_Core::ERR_INVALID_DATE       => 'invalid date or time',
                Propel_Core::ERR_INVALID_NUMBER     => 'invalid number',
                Propel_Core::ERR_MISMATCH           => 'mismatch',
                Propel_Core::ERR_NODBSELECTED       => 'no database selected',
                Propel_Core::ERR_NOSUCHFIELD        => 'no such field',
                Propel_Core::ERR_NOSUCHTABLE        => 'no such table',
                Propel_Core::ERR_NOT_CAPABLE        => 'Propel backend not capable',
                Propel_Core::ERR_NOT_FOUND          => 'not found',
                Propel_Core::ERR_NOT_LOCKED         => 'not locked',
                Propel_Core::ERR_SYNTAX             => 'syntax error',
                Propel_Core::ERR_UNSUPPORTED        => 'not supported',
                Propel_Core::ERR_VALUE_COUNT_ON_ROW => 'value count on row',
                Propel_Core::ERR_INVALID_DSN        => 'invalid DSN',
                Propel_Core::ERR_CONNECT_FAILED     => 'connect failed',
                Propel_Core::ERR_NEED_MORE_DATA     => 'insufficient data supplied',
                Propel_Core::ERR_EXTENSION_NOT_FOUND=> 'extension not found',
                Propel_Core::ERR_NOSUCHDB           => 'no such database',
                Propel_Core::ERR_ACCESS_VIOLATION   => 'insufficient permissions',
                Propel_Core::ERR_LOADMODULE         => 'error while including on demand module',
                Propel_Core::ERR_TRUNCATED          => 'truncated',
                Propel_Core::ERR_DEADLOCK           => 'deadlock detected',
            );

    /**
     * Return a textual error message for a Propel error code
     *
     * @param   int|array   integer error code,
     *                           null to get the current error code-message map,
     *                           or an array with a new error code-message map
     *
     * @return  string  error message
     */
    public function errorMessage($value = null)
    {
        if (is_null($value)) {
            return self::$_errorMessages;
        }

        return isset(self::$_errorMessages[$value]) ?
           self::$_errorMessages[$value] : self::$_errorMessages[Propel_Core::ERR];
    }

}
