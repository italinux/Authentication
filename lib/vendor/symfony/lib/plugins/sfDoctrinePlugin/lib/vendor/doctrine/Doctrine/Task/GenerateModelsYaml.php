<?php
/*
 *  $Id: GenerateModelsYaml.php 2761 2007-10-07 23:42:29Z zYne $
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
 * Propel_Task_GenerateModelsYaml
 *
 * @package     Propel
 * @subpackage  Task
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.propel-project.org
 * @since       1.0
 * @version     $Revision: 2761 $
 * @author      Jonathan H. Wage <jwage@mac.com>
 */
class Propel_Task_GenerateModelsYaml extends Propel_Task
{
    public $description          =   'Generates your Propel_Record definitions from a Yaml schema file',
           $requiredArguments    =   array('yaml_schema_path'   =>  'Specify the complete directory path to your yaml schema files.',
                                           'models_path'        =>  'Specify complete path to your Propel_Record definitions.'),
           $optionalArguments    =   array('generate_models_options'    =>  'Array of options for generating models');
    
    public function execute()
    {
        Propel_Core::generateModelsFromYaml($this->getArgument('yaml_schema_path'), $this->getArgument('models_path'), $this->getArgument('generate_models_options', array()));
        
        $this->notify('Generated models successfully from YAML schema');
    }
}
