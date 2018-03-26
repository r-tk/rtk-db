<?php
namespace RTK\DB\Adapter;

use Exception;

class PDOMysql extends PDO implements DBAdapterInterface {

	protected $pdo;

	protected $settings;

	private function constructDsnFromIniSettings($settings) {

		$host = ini_get("mysqli.default_host");
		$socket = ini_get("mysqli.default_socket");

		if (!empty($host)) {
			return 'mysql:dbname=' . $settings['database'] . ';host=' . $host;
		}
		if (!empty($settings['socket'])) {
			return 'mysql:dbname=' . $settings['database'] . ';unix_socket=' . $socket;
		}

	}

	public function __construct($settings = array()) {

		if (!is_array($settings)) {
			$settings = array();
		}

		if (!isset($settings['user'])) {
			if (isset($settings['username'])) {
				$settings['user'] = $settings['username'];
				unset($settings['username']);
			} else {
				$settings['user'] = ini_get("mysqli.default_user");
			}
		}

		if (!isset($settings['password'])) {
			if (isset($settings['pass'])) {
				$settings['password'] = $settings['pass'];
				unset($settings['pass']);
			} elseif (isset($settings['pw'])) {
				$settings['password'] = $settings['pw'];
				unset($settings['pw']);
			} else {
				$settings['password'] = ini_get("mysqli.default_pw");
			}
		}

		if (!isset($settings['database'])) {
			// NOTE: there seems no difference between the following use cases:
			//        - mysql:dbname=;host=localhost
			//        - mysql:host=localhost
			//       so we can skip empty($settings['database']) check and allow empty string
			$settings['database'] = '';
		}

		if (!isset($settings['options'])) {
			$settings['options'] = array();
		}

		// http://www.php.net/manual/en/ref.pdo-mysql.connection.php
		$dsn = '';

		if (isset($settings['dsn'])) {
			$dsn = $settings['dsn'];
		} elseif (isset($settings['host'])) {
			$dsn = 'mysql:dbname=' . $settings['database'] . ';host=' . $settings['host'];
		} elseif (isset($settings['socket'])) {
			$dsn = 'mysql:dbname=' . $settings['database'] . ';unix_socket=' . $settings['socket'];
		} else {
			$dsn = $this->constructDsnFromIniSettings($settings);
		}

		if (empty($dsn)) {
			throw new DBAdapterException('No connection settings given, and unable to guess from php.ini');
		}

		if (!empty($settings['database']) && stristr($dsn, 'dbname=') === false) {
			// TODO: shoulda, woulda, coulda overwrite?
			$dsn .= strstr($dsn, ':') ? ';' : ':';
			$dsn .= 'dbname=' . $settings['database'];
		}

		if (!empty($settings['charset']) && stristr($dsn, 'charset=') === false) {
			$dsn .= strstr($dsn, ':') ? ';' : ':';
			$dsn .= 'charset=' . $settings['charset'];
		}

		if (!empty($settings['port']) && stristr($dsn, 'port=') === false) {
			$dsn .= strstr($dsn, ':') ? ';' : ':';
			$dsn .= 'port=' . $settings['port'];
		}

		$this->settings = array(
			'dsn'      => $dsn,
			'username' => $settings['user'],
			'password' => $settings['password'],
			'options'  => $settings['options'],
		);

	}

	public function isConnected() {

		try {

			// @ is to suppress the Warnings:
			// Warning: PDO::query(): MySQL server has gone away in ...
			// Warning: PDO::query(): Error reading result set's header ...
			@$this->query('DO 1');

		} catch (Exception $e) {
			return false;
		}
		return true;

	}

}
