<?php

/*
 *  $Id: DataSQLBuilder.php 1262 2010-10-26 20:54:39Z francois $
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

require_once 'propel/engine/builder/DataModelBuilder.php';
require_once 'propel/engine/database/model/PropelTypes.php';

/**
 * Baseclass for SQL data dump SQL building classes.
 *
 * @author     Hans Lellelid <hans@xmpl.org>
 * @package    propel.engine.builder.sql
 */
abstract class DataSQLBuilder extends DataModelBuilder {

	/**
	 * Perform any reset between runs of this builder.
	 *
	 * This can be used, for example, to clear any stored start/end SQL.
	 */
	public static function reset()
	{
		// does nothing by default
	}

	/**
	 * Gets any SQL to place at the start of all the row inserts.
	 *
	 * @return     string
	 */
	public static function getDatabaseStartSql()
	{
		return '';
	}

	/**
	 * Gets any SQL to place at the end of all the row inserts.
	 *
	 * @return     string
	 */
	public static function getDatabaseEndSql()
	{
		return '';
	}

	/**
	 * Gets any SQL to place before row inserts for a new table.
	 *
	 * @return     string
	 */
	public function getTableStartSql()
	{
		return '';
	}

	/**
	 * Gets any SQL to place at the end of row inserts for a table.
	 *
	 * @return     string
	 */
	public function getTableEndSql()
	{
		return '';
	}

	/**
	 * The main method in this class, returns the SQL for INSERTing data into a row.
	 * @param      DataRow $row The row to process.
	 * @return     string
	 */
	public function buildRowSql(DataRow $row)
	{
		$sql = "";
		$platform = $this->getPlatform();
		$table = $this->getTable();

		$sql .= "INSERT INTO ".$this->quoteIdentifier($this->prefixTablename($this->getTable()->getName()))." (";

		// add column names to SQL
		$colNames = array();
		foreach ($row->getColumnValues() as $colValue) {
			$colNames[] = $this->quoteIdentifier($colValue->getColumn()->getName());
		}

		$sql .= implode(',', $colNames);

		$sql .= ") VALUES (";

		$colVals = array();
		foreach ($row->getColumnValues() as $colValue) {
			$colVals[] = $this->getColumnValueSql($colValue);
		}

		$sql .= implode(',', $colVals);
		$sql .= ");
";

		return $sql;
	}

	/**
	 * Gets the propertly escaped (and quoted) value for a column.
	 * @param      ColumnValue $colValue
	 * @return     mixed The proper value to be added to the string.
	 */
	protected function getColumnValueSql(ColumnValue $colValue)
	{
		$column = $colValue->getColumn();
		$method = 'get' . $column->getPhpNative() . 'Sql';
		return $this->$method($colValue->getValue());
	}



	/**
	 * Gets a representation of a binary value suitable for use in a SQL statement.
	 * Default behavior is true = 1, false = 0.
	 * @param      boolean $value
	 * @return     int
	 */
	protected function getBooleanSql($value)
	{
		return (int) $value;
	}


	/**
	 * Gets a representation of a BLOB/LONGVARBINARY value suitable for use in a SQL statement.
	 * @param      mixed $blob Blob object or string data.
	 * @return     string
	 */
	protected function getBlobSql($blob)
	{
		// they took magic __toString() out of PHP5.0.0; this sucks
		if (is_object($blob)) {
			return $this->getPlatform()->quote($blob->__toString());
		} else {
			return $this->getPlatform()->quote($blob);
		}
	}

	/**
	 * Gets a representation of a CLOB/LONGVARCHAR value suitable for use in a SQL statement.
	 * @param      mixed $clob Clob object or string data.
	 * @return     string
	 */
	protected function getClobSql($clob)
	{
		// they took magic __toString() out of PHP5.0.0; this sucks
		if (is_object($clob)) {
			return $this->getPlatform()->quote($clob->__toString());
		} else {
			return $this->getPlatform()->quote($clob);
		}
	}

	/**
	 * Gets a representation of a date value suitable for use in a SQL statement.
	 * @param      string $value
	 * @return     string
	 */
	protected function getDateSql($value)
	{
		return "'" . date('Y-m-d', strtotime($value)) . "'";
	}

	/**
	 * Gets a representation of a decimal value suitable for use in a SQL statement.
	 * @param      double $value
	 * @return     float
	 */
	protected function getDecimalSql($value)
	{
		return (float) $value;
	}

	/**
	 * Gets a representation of a double value suitable for use in a SQL statement.
	 * @param      double $value
	 * @return     double
	 */
	protected function getDoubleSql($value)
	{
		return (double) $value;
	}

	/**
	 * Gets a representation of a float value suitable for use in a SQL statement.
	 * @param      float $value
	 * @return     float
	 */
	protected function getFloatSql($value)
	{
		return (float) $value;
	}

	/**
	 * Gets a representation of an integer value suitable for use in a SQL statement.
	 * @param      int $value
	 * @return     int
	 */
	protected function getIntSql($value)
	{
		return (int) $value;
	}

	/**
	 * Gets a representation of a NULL value suitable for use in a SQL statement.
	 * @return     null
	 */
	protected function getNullSql()
	{
		return 'NULL';
	}

	/**
	 * Gets a representation of a string value suitable for use in a SQL statement.
	 * @param      string $value
	 * @return     string
	 */
	protected function getStringSql($value)
	{
		return $this->getPlatform()->quote($value);
	}

	/**
	 * Gets a representation of a time value suitable for use in a SQL statement.
	 * @param      string $value
	 * @return     string
	 */
	protected function getTimeSql($paramIndex, $value)
	{
		return "'" . date('H:i:s', strtotime($value)) . "'";
	}

	/**
	 * Gets a representation of a timestamp value suitable for use in a SQL statement.
	 * @param      string $value
	 * @return     string
	 */
	function getTimestampSql($value)
	{
		return "'" . date('Y-m-d H:i:s', strtotime($value)) . "'";
	}

}
