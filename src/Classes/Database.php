<?php
/**
 * Manages singletons for database connections
 *
 * Allows for connecting to multiple databases, using
 * only a single instance for each database connection.
 *
 * @copyright 2006-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Blossom\Classes;

class Database
{
	private static $connections = [];

	/**
	 * @param boolean $reconnect If true, drops the connection and reconnects
	 * @param string $db         Label for database configuration
	 * @return resource
	 */
	public static function getConnection($reconnect=false, $db='default')
	{
        global $DATABASES;

		if ($reconnect) {
            if (isset(self::$connections[$db])) { unset(self::$connections[$db]); }
		}
        if (!isset(self::$connections[$db])) {
			try {
                $conf = $DATABASES[$db];
                $options = !empty($conf['options']) ? $conf['options'] : [];

				self::$connections[$db] = new \PDO($conf['dsn'], $conf['username'], $conf['password'], $options);
				self::$connections[$db]->setAttibute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			}
			catch (Exception $e) { die($e->getMessage()); }
		}
		return self::$connections[$db];
	}

	/**
	 * @param string $db Label for database configuration
	 * @return string
	 */
	public static function getPlatform($db='default')
	{
        $pdo = self::getConnection($db);
        return ucfirst($pdo->getAttribute(\PDO::ATTR_DRIVER_NAME));
	}
}
