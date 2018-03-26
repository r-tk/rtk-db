<?php
namespace RTK\DB\Adapter;

class PDO {

	protected $pdo;

	protected $settings;

	public function __construct($settings) {

		$this->settings = $settings;

	}

	public function getInstance() {

		$this->open();

		if (!$this->isConnected()) {
			$this->close();
			$this->open();
		}

		return $this;

	}

	public function open() {

		if ($this->pdo && !$this->isConnected()) {
			$this->close();
		}

		if (!$this->pdo) {
			$this->pdo = new \PDO($this->settings['dsn'], $this->settings['username'], $this->settings['password']);
			$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		}

	}

	public function close() {

		$this->pdo = null;

	}

	public function isConnected() {

		// TODO
		throw new DBAdapterException('PDO isConnected method has not been implemented yet');

	}

	public function __call($method, $args) {

		if (!method_exists($this->pdo, $method)) {
			throw new DBAdapterException("unknown method [$method]");
		}

		return $this->pdo->$method(...$args);

	}

}
