<?php
/**
 * A example class for working with an identity webservice
 *
 * This implementation is only an example.
 *
 * This class is written specifically for the City of Bloomington's
 * Directory web application. You will probably need to customize
 * the fields used in this class.
 *
 * To implement your own identity class, you should create a class
 * in SITE_HOME/Classes.  The SITE_HOME directory does not get
 * overwritten during an upgrade.  The namespace for your class
 * should be Site\Classes\
 *
 * You can use this class as a starting point for your own implementation.
 * You will ned to change the namespace to Site\Classes.  You might also
 * want to change the name of the class to suit your own needs.
 *
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Site\Classes;

use Blossom\Classes\Url;
use Blossom\Classes\ExternalIdentity;

class Employee implements ExternalIdentity
{
	private static $connection;
	private $config;
	private $entry;

	/**
	 * We use CAS, so no authentication attempts should occur here
	 *
	 * @param array $config
	 * @param string $username
	 * @param string $password
	 * @throws Exception
	 */
	public static function authenticate($username, $password)
	{
        return false;
	}


	/**
	 * Loads an entry from the LDAP server for the given user
	 *
	 * @param array $config
	 * @param string $username
	 */
	public function __construct($username)
	{
		global $DIRECTORY_CONFIG;
		$this->config = $DIRECTORY_CONFIG['Employee'];

		$response = Url::get($this->config['DIRECTORY_SERVER'].'/people/view?format=json;username='.$username);
		if ($response) {
            $this->entry = json_decode($response);
            if (!$this->entry) {
                throw new \Exception('Employee/unknownUser');
            }
            if (!empty($this->entry->errors)) {
                throw new \Exception($this->entry->errors[0]);
            }
		}
		else {
            throw new \Exception('Employee/unknownUser');
		}
	}

	/**
	 * @return string
	 */
	public function getUsername()	{ return $this->entry->username;  }
	public function getFirstname()	{ return $this->entry->firstname; }
	public function getLastname()	{ return $this->entry->lastname;  }
	public function getEmail()		{ return $this->entry->email;     }
	public function getPhone()		{ return $this->entry->office;    }
	public function getAddress()	{ return $this->entry->address;   }
	public function getCity()		{ return $this->entry->city;      }
	public function getState()		{ return $this->entry->state;     }
	public function getZip()		{ return $this->entry->zip;       }
}
