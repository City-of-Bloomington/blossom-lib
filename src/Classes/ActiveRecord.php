<?php
/**
 * @copyright 2011-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Blossom\Classes;
use Aura\SqlQuery\QueryFactory;

abstract class ActiveRecord
{
	protected $tablename;
	protected $data = array();

	const DB_DATE_FORMAT     = 'Y-m-d';
	const DB_TIME_FORMAT     = 'H:i:s';
	const DB_DATETIME_FORMAT = 'Y-m-d H:i:s';

	abstract public function validate();

	/**
	 * Populates the object with data
	 *
	 * Passing in an associative array of data will populate this object without
	 * hitting the database.
	 *
	 * Passing in a scalar will load the data from the database.
	 * This will load all fields in the table as properties of this class.
	 * You may want to replace this with, or add your own extra, custom loading
	 *
	 * @param int|array $id
	 */
	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id)) {
				$this->data = $id;
			}
			else {
                $sql = "select * from {$this->tablename} where id=?";

				$rows = self::doQuery($sql, [$id]);
                if (count($rows)) {
                    $this->data = $rows[0];
                }
                else {
                    throw new \Exception("{$this->tablename}/unknown");
                }
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
		}
	}

	/**
	 * @param string $sql
	 * @param array $params Bound parameters
	 */
	public function doQuery($sql, array $params=null)
	{
        $pdo = Database::getConnection();
        $query = $pdo->prepare($sql);
        $query->execute($params);
        return $query->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * Writes the database back to the database
	 */
	protected function save()
	{
		$this->validate();
		$pdo = Database::getConnection();

		// Convert PHP datatypes to strings for the database
		$data = $this->data;
		foreach ($data as $k=>$v) {
            if ($v instanceof \DateTime) { $data[$k] = $v->format(self::MYSQL_DATETIME_FORMAT); }
		}

        $factory = new QueryFactory(Database::getPlatform());
		if ($this->getId()) {
            $update = $factory->newUpdate();
            $update->table($this->tablename)
                   ->cols($data)
                   ->where('id=?', $this->getId());

            $query = $pdo->prepare($update->getStatement());
            $query->execute($update->getBindValues());
		}
		else {
            $insert = $factory->newInsert();
            $insert->into($this->tablename)
                   ->cols($data);

            $query = $pdo->prepare($insert->getStatement());
            $query->execute($insert->getBindValues());
            $this->data['id'] = $pdo->lastInsertId($insert->getLastInsertIdName('id'));
		}
	}

	/**
	 * Removes this record from the database
	 */
	protected function delete()
	{
		if ($this->getId()) {
            $factory = new QueryFactory(Database::getPlatform());
            $delete = $factory->newDelete();
            $delete->from($this->tablename)->where('id=?', $this->getId());

            $pdo = Database::getConnection();
            $query = $pdo->prepare($delete->getStatement());
            $query->execute($delete->getBindValues());
		}
	}

	/**
	 * Returns any field stored in $data
	 *
	 * @param  string $fieldname
	 * @return mixed
	 */
	protected function get($fieldname)
	{
		if (isset($this->data[$fieldname])) {
			return $this->data[$fieldname];
		}
	}

	/**
	 * @param string $fieldname
	 * @param mixed  $value
	 */
	protected function set($fieldname, $value)
	{
        if (is_string($value)) {
            $value = trim($value);
            $this->data[$fieldname] = $value ? $value : null;
        }
        else {
            $this->data[$fieldname] = $value;
        }
	}

	/**
	 * Returns the date/time in the desired format
	 *
	 * Format is specified using PHP's date() syntax
	 * http://www.php.net/manual/en/function.date.php
	 * If no format is given, the database's raw data is returned
	 *
	 * @param string $field
	 * @param string $format
	 * @return string
	 */
	protected function getFormattedDate($dateField, $format=null)
	{
		if (isset($this->data[$dateField])) {
			if ($format) {
                return $this->data[$dateField]->format($format);
			}
			else {
				return $this->data[$dateField];
			}
		}
	}

	/**
	 * Return a DateTime object for a date string
	 *
	 * Dates should be in $format.
	 * If we cannot parse the string using $format, we will
	 * fall back to trying something strtotime() understands
	 * http://www.php.net/manual/en/function.strtotime.php
	 *
	 * @param  string    $date
	 * @param  string    $format
	 * @throws Exception
	 * @return DateTime
	 */
	public static function parseDate($date, $format=DATETIME_FORMAT)
	{
        $d = \DateTime::createFromFormat($format, $date);
        if (!$d) {
            $d = new \DateTime($date);
        }
        return $d;
	}

	/**
	 * Loads and returns an object for a foreign key _id field
	 *
	 * Will cache the object in a protected variable to avoid multiple database
	 * lookups. Make sure to declare a protected variable matching the class
	 *
	 * @param string $class Fully namespaced classname
	 * @param string $field
	 */
	protected function getForeignKeyObject($class, $field)
	{
		$var = preg_replace('/_id$/', '', $field);
		if (!$this->$var && isset($this->data[$field])) {
			$this->$var = new $class($this->data[$field]);
		}
		return $this->$var;
	}

	/**
	 * Verifies and saves the ID for a foreign key field
	 *
	 * Loads the object record for the foreign key and caches
	 * the object in a private variable
	 *
	 * @param string $class Fully namespaced classname
	 * @param string $field Name of field to set
	 * @param string $id The value to set
	 */
	protected function setForeignKeyField($class, $field, $id)
	{
		$id = trim($id);
		$var = preg_replace('/_id$/', '', $field);
		if ($id) {
			$this->$var = new $class($id);
			$this->data[$field] = $this->$var->getId();
		}
		else {
			$this->$field = null;
			$this->data[$field] = null;
		}
	}

	/**
	 * Verifies and saves the ID for a foreign key object
	 *
	 * Caches the object in a private variable and sets
	 * the ID value in the data
	 *
	 * @param string $class Fully namespaced classname
	 * @param string $field Name of field to set
	 * @param Object $object Value to set
	 */
	protected function setForeignKeyObject($class, $field, $object)
	{
		if ($object instanceof $class) {
			$var = preg_replace('/_id$/', '', $field);
			$this->data[$field] = $object->getId();
			$this->$var = $object;
		}
		else {
			throw new \Exception('Object does not match the given class');
		}
	}

	/**
	 * Returns whether the value can be an ID for a record
	 *
	 * return @bool
	 */
	public static function isId($id)
	{
		return ((is_int($id) && $id>0) || (is_string($id) && ctype_digit($id)));
	}
}
