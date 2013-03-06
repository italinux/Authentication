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
 * Propel_Record_Listener
 *
 * @package     Propel
 * @subpackage  Record
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.propel-project.org
 * @since       1.0
 * @version     $Revision$
 */
interface Propel_Record_Listener_Interface
{
    public function setOption($name, $value = null);

    public function getOptions();

    public function getOption($name);

    public function preSerialize(Propel_Event $event);

    public function postSerialize(Propel_Event $event);

    public function preUnserialize(Propel_Event $event);

    public function postUnserialize(Propel_Event $event);

    public function preSave(Propel_Event $event);

    public function postSave(Propel_Event $event);

    public function preDelete(Propel_Event $event);

    public function postDelete(Propel_Event $event);

    public function preUpdate(Propel_Event $event);

    public function postUpdate(Propel_Event $event);

    public function preInsert(Propel_Event $event);

    public function postInsert(Propel_Event $event);
    
    public function preHydrate(Propel_Event $event);
    
    public function postHydrate(Propel_Event $event);
}