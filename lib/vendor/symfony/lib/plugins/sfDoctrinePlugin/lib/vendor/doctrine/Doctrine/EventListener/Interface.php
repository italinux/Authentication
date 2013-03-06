<?php
/*
 *  $Id: Interface.php 7490 2010-03-29 19:53:27Z jwage $
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
 * Propel_EventListener     all event listeners extend this base class
 *                            the empty methods allow child classes to only implement the methods they need to implement
 *
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @package     Propel
 * @subpackage  EventListener
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.propel-project.org
 * @since       1.0
 * @version     $Revision: 7490 $
 */
interface Propel_EventListener_Interface
{
    public function preTransactionCommit(Propel_Event $event);
    public function postTransactionCommit(Propel_Event $event);

    public function preTransactionRollback(Propel_Event $event);
    public function postTransactionRollback(Propel_Event $event);

    public function preTransactionBegin(Propel_Event $event);
    public function postTransactionBegin(Propel_Event $event);

    public function postConnect(Propel_Event $event);
    public function preConnect(Propel_Event $event);

    public function preQuery(Propel_Event $event);
    public function postQuery(Propel_Event $event);

    public function prePrepare(Propel_Event $event);
    public function postPrepare(Propel_Event $event);

    public function preExec(Propel_Event $event);
    public function postExec(Propel_Event $event);

    public function preError(Propel_Event $event);
    public function postError(Propel_Event $event);

    public function preFetch(Propel_Event $event);
    public function postFetch(Propel_Event $event);

    public function preFetchAll(Propel_Event $event);
    public function postFetchAll(Propel_Event $event);

    public function preStmtExecute(Propel_Event $event);
    public function postStmtExecute(Propel_Event $event);
}
