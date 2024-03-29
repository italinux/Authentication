<?php
/*
 *  $Id: MysqlSchemaParser.php 1262 2010-10-26 20:54:39Z francois $
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
 * and is licensed under the LGPL. For more information please see
 * <http://propel.phpdb.org>.
 */

require_once 'propel/engine/database/reverse/BaseSchemaParser.php';

/**
 * Mysql database schema parser.
 *
 * @author     Hans Lellelid <hans@xmpl.org>
 * @version    $Revision: 1262 $
 * @package    propel.engine.database.reverse.mysql
 */
class MysqlSchemaParser extends BaseSchemaParser {

	/**
	 * @var        boolean
	 */
	private $addVendorInfo = false;

	/**
	 * Map MySQL native types to Propel types.
	 * @var        array
	 */
	private static $mysqlTypeMap = array(
		'tinyint' => PropelTypes::TINYINT,
		'smallint' => PropelTypes::SMALLINT,
		'mediumint' => PropelTypes::SMALLINT,
		'int' => PropelTypes::INTEGER,
		'integer' => PropelTypes::INTEGER,
		'bigint' => PropelTypes::BIGINT,
		'int24' => PropelTypes::BIGINT,
		'real' => PropelTypes::REAL,
		'float' => PropelTypes::FLOAT,
		'decimal' => PropelTypes::DECIMAL,
		'numeric' => PropelTypes::NUMERIC,
		'double' => PropelTypes::DOUBLE,
		'char' => PropelTypes::CHAR,
		'varchar' => PropelTypes::VARCHAR,
		'date' => PropelTypes::DATE,
		'time' => PropelTypes::TIME,
		'year' => PropelTypes::INTEGER,
		'datetime' => PropelTypes::TIMESTAMP,
		'timestamp' => PropelTypes::TIMESTAMP,
		'tinyblob' => PropelTypes::BINARY,
		'blob' => PropelTypes::BLOB,
		'mediumblob' => PropelTypes::BLOB,
		'longblob' => PropelTypes::BLOB,
		'longtext' => PropelTypes::CLOB,
		'tinytext' => PropelTypes::VARCHAR,
		'mediumtext' => PropelTypes::LONGVARCHAR,
		'text' => PropelTypes::LONGVARCHAR,
		'enum' => PropelTypes::CHAR,
		'set' => PropelTypes::CHAR,
	);

	/**
	 * Gets a type mapping from native types to Propel types
	 *
	 * @return     array
	 */
	protected function getTypeMapping()
	{
		return self::$mysqlTypeMap;
	}

	/**
	 *
	 */
	public function parse(Database $database)
	{
		$this->addVendorInfo = $this->getGeneratorConfig()->getBuildProperty('addVendorInfo');

		$stmt = $this->dbh->query("SHOW TABLES");

		// First load the tables (important that this happen before filling out details of tables)
		$tables = array();
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$name = $row[0];
			$table = new Table($name);
			$database->addTable($table);
			$tables[] = $table;
		}

		// Now populate only columns.
		foreach ($tables as $table) {
			$this->addColumns($table);
		}

		// Now add indexes and constraints.
		foreach ($tables as $table) {
			$this->addForeignKeys($table);
			$this->addIndexes($table);
			$this->addPrimaryKey($table);
			if ($this->addVendorInfo) {
				$this->addTableVendorInfo($table);
			}
		}
	}


	/**
	 * Adds Columns to the specified table.
	 *
	 * @param      Table $table The Table model class to add columns to.
	 */
	protected function addColumns(Table $table)
	{
		$stmt = $this->dbh->query("SHOW COLUMNS FROM `" . $table->getName() . "`");

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

			$name = $row['Field'];
			$is_nullable = ($row['Null'] == 'YES');
			$autoincrement = (strpos($row['Extra'], 'auto_increment') !== false);
			$size = null;
			$precision = null;
			$scale = null;

			if (preg_match('/^(\w+)[\(]?([\d,]*)[\)]?( |$)/', $row['Type'], $matches)) {
				//            colname[1]   size/precision[2]
				$nativeType = $matches[1];
				if ($matches[2]) {
					if ( ($cpos = strpos($matches[2], ',')) !== false) {
						$size = (int) substr($matches[2], 0, $cpos);
						$precision = $size;
						$scale = (int) substr($matches[2], $cpos + 1);
					} else {
						$size = (int) $matches[2];
					}
				}
			} elseif (preg_match('/^(\w+)\(/', $row['Type'], $matches)) {
				$nativeType = $matches[1];
			} else {
				$nativeType = $row['Type'];
			}

			//BLOBs can't have any default values in MySQL
			$default = preg_match('~blob|text~', $nativeType) ? null : $row['Default'];

			$propelType = $this->getMappedPropelType($nativeType);
			if (!$propelType) {
				$propelType = Column::DEFAULT_TYPE;
				$this->warn("Column [" . $table->getName() . "." . $name. "] has a column type (".$nativeType.") that Propel does not support.");
			}

			$column = new Column($name);
			$column->setTable($table);
			$column->setDomainForType($propelType);
			// We may want to provide an option to include this:
			// $column->getDomain()->replaceSqlType($type);
			$column->getDomain()->replaceSize($size);
			$column->getDomain()->replaceScale($scale);
			if ($default !== null) {
				$column->getDomain()->setDefaultValue(new ColumnDefaultValue($default, ColumnDefaultValue::TYPE_VALUE));
			}
			$column->setAutoIncrement($autoincrement);
			$column->setNotNull(!$is_nullable);

			if ($this->addVendorInfo) {
				$vi = $this->getNewVendorInfoObject($row);
				$column->addVendorInfo($vi);
			}

			$table->addColumn($column);
		}


	} // addColumn()

	/**
	 * Load foreign keys for this table.
	 */
	protected function addForeignKeys(Table $table)
	{
		$database = $table->getDatabase();

		$stmt = $this->dbh->query("SHOW CREATE TABLE `" . $table->getName(). "`");
		$row = $stmt->fetch(PDO::FETCH_NUM);

		$foreignKeys = array(); // local store to avoid duplicates

		// Get the information on all the foreign keys
		$regEx = '/CONSTRAINT `([^`]+)` FOREIGN KEY \((.+)\) REFERENCES `([^`]*)` \((.+)\)(.*)/';
		if (preg_match_all($regEx,$row[1],$matches)) {
			$tmpArray = array_keys($matches[0]);
			foreach ($tmpArray as $curKey) {
				$name = $matches[1][$curKey];
				$rawlcol = $matches[2][$curKey];
				$ftbl = $matches[3][$curKey];
				$rawfcol = $matches[4][$curKey];
				$fkey = $matches[5][$curKey];
				
				$lcols = array();
				foreach(preg_split('/`, `/', $rawlcol) as $piece) {
					$lcols[] = trim($piece, '` ');
				}
				
				$fcols = array();
				foreach(preg_split('/`, `/', $rawfcol) as $piece) {
					$fcols[] = trim($piece, '` ');
				}
				
				//typical for mysql is RESTRICT
				$fkactions = array(
					'ON DELETE'	=> ForeignKey::RESTRICT,
					'ON UPDATE'	=> ForeignKey::RESTRICT,
				);

				if ($fkey) {
					//split foreign key information -> search for ON DELETE and afterwords for ON UPDATE action
					foreach (array_keys($fkactions) as $fkaction) {
						$result = NULL;
						preg_match('/' . $fkaction . ' (' . ForeignKey::CASCADE . '|' . ForeignKey::SETNULL . ')/', $fkey, $result);
						if ($result && is_array($result) && isset($result[1])) {
							$fkactions[$fkaction] = $result[1];
						}
					}
				}
				
				$localColumns = array();
				$foreignColumns = array();
				
				$foreignTable = $database->getTable($ftbl);
				
				foreach($fcols as $fcol) {
					$foreignColumns[] = $foreignTable->getColumn($fcol);
				}
				foreach($lcols as $lcol) {
					$localColumns[] = $table->getColumn($lcol);
				}

				if (!isset($foreignKeys[$name])) {
					$fk = new ForeignKey($name);
					$fk->setForeignTableName($foreignTable->getName());
					$fk->setOnDelete($fkactions['ON DELETE']);
					$fk->setOnUpdate($fkactions['ON UPDATE']);
					$table->addForeignKey($fk);
					$foreignKeys[$name] = $fk;
				}
				
				for($i=0; $i < count($localColumns); $i++) {
					$foreignKeys[$name]->addReference($localColumns[$i], $foreignColumns[$i]);
				}
				
			}

		}

	}

	/**
	 * Load indexes for this table
	 */
	protected function addIndexes(Table $table)
	{
		$stmt = $this->dbh->query("SHOW INDEX FROM `" . $table->getName() . "`");

		// Loop through the returned results, grouping the same key_name together
		// adding each column for that key.

		$indexes = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$colName = $row["Column_name"];
			$name = $row["Key_name"];

			if ($name == "PRIMARY") {
				continue;
			}

			if (!isset($indexes[$name])) {
				$isUnique = ($row["Non_unique"] == 0);
				if ($isUnique) {
					$indexes[$name] = new Unique($name);
				} else {
					$indexes[$name] = new Index($name);
				}
				if ($this->addVendorInfo) {
					$vi = $this->getNewVendorInfoObject($row);
					$indexes[$name]->addVendorInfo($vi);
				}
				$table->addIndex($indexes[$name]);
			}

			$indexes[$name]->addColumn($table->getColumn($colName));
		}
	}

	/**
	 * Loads the primary key for this table.
	 */
	protected function addPrimaryKey(Table $table)
	{
		$stmt = $this->dbh->query("SHOW KEYS FROM `" . $table->getName() . "`");

		// Loop through the returned results, grouping the same key_name together
		// adding each column for that key.
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			// Skip any non-primary keys.
			if ($row['Key_name'] !== 'PRIMARY') {
				continue;
			}
			$name = $row["Column_name"];
			$table->getColumn($name)->setPrimaryKey(true);
		}
	}

	/**
	 * Adds vendor-specific info for table.
	 *
	 * @param      Table $table
	 */
	protected function addTableVendorInfo(Table $table)
	{
		$stmt = $this->dbh->query("SHOW TABLE STATUS LIKE '" . $table->getName() . "'");
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$vi = $this->getNewVendorInfoObject($row);
		$table->addVendorInfo($vi);
	}
}
