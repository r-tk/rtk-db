<?php
namespace RTK\DB\Adapter;

use Exception;

class Mysqli extends \mysqli implements DBAdapterInterface {

	private $settings;

	private $initialized = false;

	private $connection_opened = false;

	public function __construct($settings) {

		if (!is_array($settings)) {
			$settings = array();
		}
		if (!isset($settings['host'])) {
			$settings['host'] = ini_get("mysqli.default_host");
		}
		if (!isset($settings['user'])) {
			$settings['user'] = ini_get("mysqli.default_user");
		}
		if (!isset($settings['password'])) {
			$settings['password'] = ini_get("mysqli.default_pw");
		}
		if (!isset($settings['database'])) {
			$settings['database'] = '';
		}
		if (!isset($settings['port'])) {
			$settings['port'] = ini_get("mysqli.default_port");
		}
		if (!isset($settings['socket'])) {
			$settings['socket'] = ini_get("mysqli.default_socket");
		}
		$this->settings = $settings;

	}

	public function getInstance() {
		$this->open();
		return $this;

	}

	/**
	 * open or re-open connection to database
	 */
	public function open() {

		$this->openConnection();

		if (parent::ping() === false) {
			$this->openConnection(true);
		}

	}

	/**
	 * close connection to database
	 */
	public function close() {

		if ($this->connection_opened) {
			$this->connection_opened = false;
			return parent::close();
		}
		return true;

	}

	/**
	 * open a connection to the database
	 *
	 * @param boolean $force re-connect to DB even if
	 *                a connection has already been established
	 * @throws DBAdapterException
	 */
	private function openConnection($force = false) {

		if ($this->connection_opened && !$force) {
			return;
		}

		if (!$this->initialized) {
			parent::init();
			$this->initialized = true;
		}

		$connection = @parent::real_connect(
			$this->settings['host'],
			$this->settings['user'],
			$this->settings['password'],
			$this->settings['database'],
			$this->settings['port'],
			$this->settings['socket']
		);

		if ($connection === false) {
			throw new DBAdapterException('Connection failed: #'
				. $this->connect_errno
				. ' - '
				. $this->connect_error);
		}

		if (isset($this->settings['charset'])) {
			parent::set_charset($this->settings['charset']);
		}

		$this->connection_opened = true;

	}

	/**
	 * is the connection open
	 */
	public function isConnected() {

		if (!$this->connection_opened) {
			return false;
		}

		try {
			$this->query('DO 1');
		} catch (Exception $e) {
			return false;
		}
		return true;

	}

	/**
	 * mysqli query method wrapper
	 *
	 * @param string $query
	 * @param $resultmode
	 * @return bool|\mysqli_result
	 * @throws DBAdapterException
	 */
	public function query($query, $resultmode = MYSQLI_STORE_RESULT) {

		$res = @parent::query($query, $resultmode);

		if ($res === false) {
			throw new DBAdapterException("query failed: " . $this->error);
		}

		return $res;

	}

}
