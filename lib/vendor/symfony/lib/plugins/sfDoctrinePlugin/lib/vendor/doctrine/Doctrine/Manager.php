<?php
/*
 *  $Id: Manager.php 7490 2010-03-29 19:53:27Z jwage $
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
 *
 * Propel_Manager is the base component of all propel based projects.
 * It opens and keeps track of all connections (database connections).
 *
 * @package     Propel
 * @subpackage  Manager
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.propel-project.org
 * @since       1.0
 * @version     $Revision: 7490 $
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 */
class Propel_Manager extends Propel_Configurable implements Countable, IteratorAggregate
{
    /**
     * @var array $connections          an array containing all the opened connections
     */
    protected $_connections   = array();

    /**
     * @var array $bound                an array containing all components that have a bound connection
     */
    protected $_bound         = array();

    /**
     * @var integer $index              the incremented index
     */
    protected $_index         = 0;

    /**
     * @var integer $currIndex          the current connection index
     */
    protected $_currIndex     = 0;

    /**
     * @var Propel_Query_Registry     the query registry
     */
    protected $_queryRegistry;

    /**
     * @var array                       Array of registered validators
     */
    protected $_validators = array();

    /**
     * @var array                       Array of registered hydrators
     */
    protected $_hydrators = array(
        Propel_Core::HYDRATE_ARRAY            => 'Propel_Hydrator_ArrayDriver',
        Propel_Core::HYDRATE_RECORD           => 'Propel_Hydrator_RecordDriver',
        Propel_Core::HYDRATE_NONE             => 'Propel_Hydrator_NoneDriver',
        Propel_Core::HYDRATE_SCALAR           => 'Propel_Hydrator_ScalarDriver',
        Propel_Core::HYDRATE_SINGLE_SCALAR    => 'Propel_Hydrator_SingleScalarDriver',
        Propel_Core::HYDRATE_ON_DEMAND        => 'Propel_Hydrator_RecordDriver',
        Propel_Core::HYDRATE_ARRAY_HIERARCHY  => 'Propel_Hydrator_ArrayHierarchyDriver',
        Propel_Core::HYDRATE_RECORD_HIERARCHY => 'Propel_Hydrator_RecordHierarchyDriver',
    );

    protected $_connectionDrivers = array(
        'db2'      => 'Propel_Connection_Db2',
        'mysql'    => 'Propel_Connection_Mysql',
        'mysqli'   => 'Propel_Connection_Mysql',
        'sqlite'   => 'Propel_Connection_Sqlite',
        'pgsql'    => 'Propel_Connection_Pgsql',
        'oci'      => 'Propel_Connection_Oracle',
        'oci8'     => 'Propel_Connection_Oracle',
        'oracle'   => 'Propel_Connection_Oracle',
        'mssql'    => 'Propel_Connection_Mssql',
        'dblib'    => 'Propel_Connection_Mssql',
        'odbc'     => 'Propel_Connection_Mssql', 
        'mock'     => 'Propel_Connection_Mock'
    );

    protected $_extensions = array();

    /**
     * @var boolean                     Whether or not the validators from disk have been loaded
     */
    protected $_loadedValidatorsFromDisk = false;

    protected static $_instance;

    private $_initialized = false;

    /**
     * constructor
     *
     * this is private constructor (use getInstance to get an instance of this class)
     */
    private function __construct()
    {
        $null = new Propel_Null;
        Propel_Locator_Injectable::initNullObject($null);
        Propel_Record_Iterator::initNullObject($null);
    }

    /**
     * Sets default attributes values.
     *
     * This method sets default values for all null attributes of this 
     * instance. It is idempotent and can only be called one time. Subsequent 
     * calls does not alter the attribute values.
     *
     * @return boolean      true if inizialization was executed
     */
    public function setDefaultAttributes()
    {
        if ( ! $this->_initialized) {
            $this->_initialized = true;
            $attributes = array(
                        Propel_Core::ATTR_CACHE                        => null,
                        Propel_Core::ATTR_RESULT_CACHE                 => null,
                        Propel_Core::ATTR_QUERY_CACHE                  => null,
                        Propel_Core::ATTR_LOAD_REFERENCES              => true,
                        Propel_Core::ATTR_LISTENER                     => new Propel_EventListener(),
                        Propel_Core::ATTR_RECORD_LISTENER              => new Propel_Record_Listener(),
                        Propel_Core::ATTR_THROW_EXCEPTIONS             => true,
                        Propel_Core::ATTR_VALIDATE                     => Propel_Core::VALIDATE_NONE,
                        Propel_Core::ATTR_QUERY_LIMIT                  => Propel_Core::LIMIT_RECORDS,
                        Propel_Core::ATTR_IDXNAME_FORMAT               => "%s_idx",
                        Propel_Core::ATTR_SEQNAME_FORMAT               => "%s_seq",
                        Propel_Core::ATTR_TBLNAME_FORMAT               => "%s",
                        Propel_Core::ATTR_FKNAME_FORMAT                => "%s",
                        Propel_Core::ATTR_QUOTE_IDENTIFIER             => false,
                        Propel_Core::ATTR_SEQCOL_NAME                  => 'id',
                        Propel_Core::ATTR_PORTABILITY                  => Propel_Core::PORTABILITY_NONE,
                        Propel_Core::ATTR_EXPORT                       => Propel_Core::EXPORT_ALL,
                        Propel_Core::ATTR_DECIMAL_PLACES               => 2,
                        Propel_Core::ATTR_DEFAULT_PARAM_NAMESPACE      => 'propel',
                        Propel_Core::ATTR_AUTOLOAD_TABLE_CLASSES       => false,
                        Propel_Core::ATTR_USE_DQL_CALLBACKS            => false,
                        Propel_Core::ATTR_AUTO_ACCESSOR_OVERRIDE       => false,
                        Propel_Core::ATTR_AUTO_FREE_QUERY_OBJECTS      => false,
                        Propel_Core::ATTR_DEFAULT_IDENTIFIER_OPTIONS   => array(),
                        Propel_Core::ATTR_DEFAULT_COLUMN_OPTIONS       => array(),
                        Propel_Core::ATTR_HYDRATE_OVERWRITE            => true,
                        Propel_Core::ATTR_QUERY_CLASS                  => 'Propel_Query',
                        Propel_Core::ATTR_COLLECTION_CLASS             => 'Propel_Collection',
                        Propel_Core::ATTR_TABLE_CLASS                  => 'Propel_Table',
                        Propel_Core::ATTR_CASCADE_SAVES                => true,
                        Propel_Core::ATTR_TABLE_CLASS_FORMAT           => '%sTable'
                        ); 
            foreach ($attributes as $attribute => $value) {
                $old = $this->getAttribute($attribute);
                if ($old === null) {
                    $this->setAttribute($attribute,$value);
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Returns an instance of this class
     * (this class uses the singleton pattern)
     *
     * @return Propel_Manager
     */
    public static function getInstance()
    {
        if ( ! isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Reset the internal static instance
     *
     * @return void
     */
    public static function resetInstance()
    {
        if (self::$_instance) {
            self::$_instance->reset();
            self::$_instance = null;
        }
    }

    /**
     * Reset this instance of the manager
     *
     * @return void
     */
    public function reset()
    {
        foreach ($this->_connections as $conn) {
            $conn->close();
        }
        $this->_connections = array();
        $this->_queryRegistry = null;
        $this->_extensions = array();
        $this->_bound = array();
        $this->_validators = array();
        $this->_loadedValidatorsFromDisk = false;
        $this->_index = 0;
        $this->_currIndex = 0;
        $this->_initialized = false;
    }

    /**
     * Lazy-initializes the query registry object and returns it
     *
     * @return Propel_Query_Registry
     */
    public function getQueryRegistry()
    {
      	if ( ! isset($this->_queryRegistry)) {
      	   $this->_queryRegistry = new Propel_Query_Registry();
      	}
        return $this->_queryRegistry;
    }

    /**
     * Sets the query registry
     *
     * @return Propel_Manager     this object
     */
    public function setQueryRegistry(Propel_Query_Registry $registry)
    {
        $this->_queryRegistry = $registry;
        
        return $this;
    }

    /**
     * Open a new connection. If the adapter parameter is set this method acts as
     * a short cut for Propel_Manager::getInstance()->openConnection($adapter, $name);
     *
     * if the adapter paramater is not set this method acts as
     * a short cut for Propel_Manager::getInstance()->getCurrentConnection()
     *
     * @param PDO|Propel_Adapter_Interface $adapter   database driver
     * @param string $name                              name of the connection, if empty numeric key is used
     * @throws Propel_Manager_Exception               if trying to bind a connection with an existing name
     * @return Propel_Connection
     */
    public static function connection($adapter = null, $name = null)
    {
        if ($adapter == null) {
            return Propel_Manager::getInstance()->getCurrentConnection();
        } else {
            return Propel_Manager::getInstance()->openConnection($adapter, $name);
        }
    }

    /**
     * Opens a new connection and saves it to Propel_Manager->connections
     *
     * @param PDO|Propel_Adapter_Interface $adapter   database driver
     * @param string $name                              name of the connection, if empty numeric key is used
     * @throws Propel_Manager_Exception               if trying to bind a connection with an existing name
     * @throws Propel_Manager_Exception               if trying to open connection for unknown driver
     * @return Propel_Connection
     */
    public function openConnection($adapter, $name = null, $setCurrent = true)
    {
        if (is_object($adapter)) {
            if ( ! ($adapter instanceof PDO) && ! in_array('Propel_Adapter_Interface', class_implements($adapter))) {
                throw new Propel_Manager_Exception("First argument should be an instance of PDO or implement Propel_Adapter_Interface");
            }

            $driverName = $adapter->getAttribute(Propel_Core::ATTR_DRIVER_NAME);
        } else if (is_array($adapter)) {
            if ( ! isset($adapter[0])) {
                throw new Propel_Manager_Exception('Empty data source name given.');
            }
            $e = explode(':', $adapter[0]);

            if ($e[0] == 'uri') {
                $e[0] = 'odbc';
            }

            $parts['dsn']    = $adapter[0];
            $parts['scheme'] = $e[0];
            $parts['user']   = (isset($adapter[1])) ? $adapter[1] : null;
            $parts['pass']   = (isset($adapter[2])) ? $adapter[2] : null;
            $driverName = $e[0];
            $adapter = $parts;
        } else {
            $parts = $this->parseDsn($adapter);
            $driverName = $parts['scheme'];
            $adapter = $parts;
        }

        // Decode adapter information
        if (is_array($adapter)) {
            foreach ($adapter as $key => $value) {
                $adapter[$key]  = $value ? urldecode($value):null;
            }
        }

        // initialize the default attributes
        $this->setDefaultAttributes();

        if ($name !== null) {
            $name = (string) $name;
            if (isset($this->_connections[$name])) {
                if ($setCurrent) {
                    $this->_currIndex = $name;
                }
                return $this->_connections[$name];
            }
        } else {
            $name = $this->_index;
            $this->_index++;
        }

        if ( ! isset($this->_connectionDrivers[$driverName])) {
            throw new Propel_Manager_Exception('Unknown driver ' . $driverName);
        }

        $className = $this->_connectionDrivers[$driverName];
        $conn = new $className($this, $adapter);
        $conn->setName($name);

        $this->_connections[$name] = $conn;

        if ($setCurrent) {
            $this->_currIndex = $name;
        }
        return $this->_connections[$name];
    }
    
    /**
     * Parse a pdo style dsn in to an array of parts
     *
     * @param array $dsn An array of dsn information
     * @return array The array parsed
     * @todo package:dbal
     */
    public function parsePdoDsn($dsn)
    {
        $parts = array();

        $names = array('dsn', 'scheme', 'host', 'port', 'user', 'pass', 'path', 'query', 'fragment', 'unix_socket');

        foreach ($names as $name) {
            if ( ! isset($parts[$name])) {
                $parts[$name] = null;
            }
        }

        $e = explode(':', $dsn);
        $parts['scheme'] = $e[0];
        $parts['dsn'] = $dsn;

        $e = explode(';', $e[1]);
        foreach ($e as $string) {
            if ($string) {
                $e2 = explode('=', $string);

                if (isset($e2[0]) && isset($e2[1])) {
                    if (count($e2) > 2)
                    {
                        $key = $e2[0];
                        unset($e2[0]);
                        $value = implode('=', $e2);
                    } else {
                        list($key, $value) = $e2;
                    }
                    $parts[$key] = $value;
                }
            }
        }

        return $parts;
    }

    /**
     * Build the blank dsn parts array used with parseDsn()
     *
     * @see parseDsn()
     * @param string $dsn 
     * @return array $parts
     */
    protected function _buildDsnPartsArray($dsn)
    {
        // fix sqlite dsn so that it will parse correctly
        $dsn = str_replace("////", "/", $dsn);
        $dsn = str_replace("\\", "/", $dsn);
        $dsn = preg_replace("/\/\/\/(.*):\//", "//$1:/", $dsn);

        // silence any warnings
        $parts = @parse_url($dsn);

        $names = array('dsn', 'scheme', 'host', 'port', 'user', 'pass', 'path', 'query', 'fragment', 'unix_socket');

        foreach ($names as $name) {
            if ( ! isset($parts[$name])) {
                $parts[$name] = null;
            }
        }

        if (count($parts) == 0 || ! isset($parts['scheme'])) {
            throw new Propel_Manager_Exception('Could not parse dsn');
        }

        return $parts;
    }

    /**
     * Parse a Propel style dsn string in to an array of parts
     *
     * @param string $dsn
     * @return array Parsed contents of DSN
     * @todo package:dbal
     */
    public function parseDsn($dsn)
    {
        $parts = $this->_buildDsnPartsArray($dsn);

        switch ($parts['scheme']) {
            case 'sqlite':
            case 'sqlite2':
            case 'sqlite3':
                if (isset($parts['host']) && $parts['host'] == ':memory') {
                    $parts['database'] = ':memory:';
                    $parts['dsn']      = 'sqlite::memory:';
                } else {
                    //fix windows dsn we have to add host: to path and set host to null
                    if (isset($parts['host'])) {
                        $parts['path'] = $parts['host'] . ":" . $parts["path"];
                        $parts['host'] = null;
                    }
                    $parts['database'] = $parts['path'];
                    $parts['dsn'] = $parts['scheme'] . ':' . $parts['path'];
                }

                break;

            case 'mssql':
            case 'dblib':
                if ( ! isset($parts['path']) || $parts['path'] == '/') {
                    throw new Propel_Manager_Exception('No database available in data source name');
                }
                if (isset($parts['path'])) {
                    $parts['database'] = substr($parts['path'], 1);
                }
                if ( ! isset($parts['host'])) {
                    throw new Propel_Manager_Exception('No hostname set in data source name');
                }

                $parts['dsn'] = $parts['scheme'] . ':host='
                              . $parts['host'] . (isset($parts['port']) ? ':' . $parts['port']:null) . ';dbname='
                              . $parts['database'];

                break;

            case 'mysql':
            case 'oci8':
            case 'oci':
            case 'pgsql':
            case 'odbc':
            case 'mock':
            case 'oracle':
                if ( ! isset($parts['path']) || $parts['path'] == '/') {
                    throw new Propel_Manager_Exception('No database available in data source name');
                }
                if (isset($parts['path'])) {
                    $parts['database'] = substr($parts['path'], 1);
                }
                if ( ! isset($parts['host'])) {
                    throw new Propel_Manager_Exception('No hostname set in data source name');
                }

                $parts['dsn'] = $parts['scheme'] . ':host='
                              . $parts['host'] . (isset($parts['port']) ? ';port=' . $parts['port']:null) . ';dbname='
                              . $parts['database'];

                break;
            default:
                $parts['dsn'] = $dsn;
        }

        return $parts;
    }

    /**
     * Get the connection instance for the passed name
     *
     * @param string $name                  name of the connection, if empty numeric key is used
     * @return Propel_Connection
     * @throws Propel_Manager_Exception   if trying to get a non-existent connection
     */
    public function getConnection($name)
    {
        if ( ! isset($this->_connections[$name])) {
            throw new Propel_Manager_Exception('Unknown connection: ' . $name);
        }

        return $this->_connections[$name];
    }

    /**
     * Get the name of the passed connection instance
     *
     * @param Propel_Connection $conn     connection object to be searched for
     * @return string                       the name of the connection
     */
    public function getConnectionName(Propel_Connection $conn)
    {
        return array_search($conn, $this->_connections, true);
    }

    /**
     * Binds given component to given connection
     * this means that when ever the given component uses a connection
     * it will be using the bound connection instead of the current connection
     *
     * @param string $componentName
     * @param string $connectionName
     * @return boolean
     */
    public function bindComponent($componentName, $connectionName)
    {
        $this->_bound[$componentName] = $connectionName;
    }

    /**
     * Get the connection instance for the specified component
     *
     * @param string $componentName
     * @return Propel_Connection
     */
    public function getConnectionForComponent($componentName)
    {
        Propel_Core::modelsAutoload($componentName);

        if (isset($this->_bound[$componentName])) {
            return $this->getConnection($this->_bound[$componentName]);
        }

        return $this->getCurrentConnection();
    }

    /**
     * Check if a component is bound to a connection
     *
     * @param string $componentName
     * @return boolean
     */
    public function hasConnectionForComponent($componentName = null)
    {
        return isset($this->_bound[$componentName]);
    }

    /**
     * Closes the specified connection
     *
     * @param Propel_Connection $connection
     * @return void
     */
    public function closeConnection(Propel_Connection $connection)
    {
        $connection->close();

        $key = array_search($connection, $this->_connections, true);

        if ($key !== false) {
            unset($this->_connections[$key]);

            if ($key === $this->_currIndex) {
                $key = key($this->_connections);
                $this->_currIndex = ($key !== null) ? $key : 0;
            }
        }

        unset($connection);
    }

    /**
     * Returns all opened connections
     *
     * @return array
     */
    public function getConnections()
    {
        return $this->_connections;
    }

    /**
     * Sets the current connection to $key
     *
     * @param mixed $key                        the connection key
     * @throws InvalidKeyException
     * @return void
     */
    public function setCurrentConnection($key)
    {
        $key = (string) $key;
        if ( ! isset($this->_connections[$key])) {
            throw new Propel_Manager_Exception("Connection key '$key' does not exist.");
        }
        $this->_currIndex = $key;
    }

    /**
     * Whether or not the manager contains specified connection
     *
     * @param mixed $key                        the connection key
     * @return boolean
     */
    public function contains($key)
    {
        return isset($this->_connections[$key]);
    }

    /**
     * Returns the number of opened connections
     *
     * @return integer
     */
    public function count()
    {
        return count($this->_connections);
    }

    /**
     * Returns an ArrayIterator that iterates through all connections
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_connections);
    }

    /**
     * Get the current connection instance
     *
     * @throws Propel_Connection_Exception       if there are no open connections
     * @return Propel_Connection
     */
    public function getCurrentConnection()
    {
        $i = $this->_currIndex;
        if ( ! isset($this->_connections[$i])) {
            throw new Propel_Connection_Exception('There is no open connection');
        }
        return $this->_connections[$i];
    }

    /**
     * Creates databases for all existing connections
     *
     * @param string $specifiedConnections Array of connections you wish to create the database for
     * @return void
     * @todo package:dbal
     */
    public function createDatabases($specifiedConnections = array())
    {
        if ( ! is_array($specifiedConnections)) {
            $specifiedConnections = (array) $specifiedConnections;
        }

        foreach ($this as $name => $connection) {
            if ( ! empty($specifiedConnections) && ! in_array($name, $specifiedConnections)) {
                continue;
            }

            $connection->createDatabase();
        }
    }

    /**
     * Drops databases for all existing connections
     *
     * @param string $specifiedConnections Array of connections you wish to drop the database for
     * @return void
     * @todo package:dbal
     */
    public function dropDatabases($specifiedConnections = array())
    {
        if ( ! is_array($specifiedConnections)) {
            $specifiedConnections = (array) $specifiedConnections;
        }

        foreach ($this as $name => $connection) {
            if ( ! empty($specifiedConnections) && ! in_array($name, $specifiedConnections)) {
                continue;
            }

            $connection->dropDatabase();
        }
    }

    /**
     * Returns a string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        $r[] = "<pre>";
        $r[] = "Propel_Manager";
        $r[] = "Connections : ".count($this->_connections);
        $r[] = "</pre>";
        return implode("\n",$r);
    }

    /**
     * Get available propel validators
     *
     * @return array $validators
     */
    public function getValidators()
    {
        if ( ! $this->_loadedValidatorsFromDisk) {
            $this->_loadedValidatorsFromDisk = true;

            $validators = array();

            $dir = Propel_Core::getPath() . DIRECTORY_SEPARATOR . 'Propel' . DIRECTORY_SEPARATOR . 'Validator';

            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::LEAVES_ONLY);
            foreach ($files as $file) {
                $e = explode('.', $file->getFileName());

                if (end($e) == 'php') {
                    $name = strtolower($e[0]);

                    $validators[] = $name;
                }
            }

            $this->registerValidators($validators);
        }

        return $this->_validators;
    }

    /**
     * Register validators so that Propel is aware of them
     *
     * @param  mixed $validators Name of validator or array of validators
     * @return void
     */
    public function registerValidators($validators)
    {
        $validators = (array) $validators;
        foreach ($validators as $validator) {
            if ( ! in_array($validator, $this->_validators)) {
                $this->_validators[] = $validator;
            }
        }
    }

    /**
     * Register a new driver for hydration
     *
     * @return void
     */
    public function registerHydrator($name, $class)
    {
        $this->_hydrators[$name] = $class;
    }

    /**
     * Get all registered hydrators
     *
     * @return array $hydrators
     */
    public function getHydrators()
    {
        return $this->_hydrators;
    }

    /**
     * Register a custom connection driver
     *
     * @return void
     */
    public function registerConnectionDriver($name, $class)
    {
        $this->_connectionDrivers[$name] = $class;
    }

    /**
     * Get all the available connection drivers
     *
     * @return array $connectionDrivers
     */
    public function getConnectionDrivers()
    {
        return $this->_connectionsDrivers;
    }

    /**
     * Register a Propel extension for extensionsAutoload() method
     *
     * @param string $name 
     * @param string $path 
     * @return void
     */
    public function registerExtension($name, $path = null)
    {
        if (is_null($path)) {
            $path = Propel_Core::getExtensionsPath() . '/' . $name . '/lib';
        }
        $this->_extensions[$name] = $path;
    }

    /**
     * Get all registered Propel extensions
     *
     * @return $extensions
     */
    public function getExtensions()
    {
        return $this->_extensions;
    }
}