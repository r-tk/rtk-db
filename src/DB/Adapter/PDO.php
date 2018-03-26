<?php
namespace RTK\DB\Adapter;

/**
 * PDO wrapper
 *
 * @method boolean beginTransaction()
 * @method boolean commit()
 * @method mixed errorCode()
 * @method null|array errorInfo
 * @method int|boolean exec( string $statement )
 * @method mixed getAttribute( int $attribute )
 * @method array getAvailableDrivers
 * @method boolean inTransaction
 * @method mixed lastInsertId ( string $name = NULL )
 * @method \PDOStatement prepare( string $statement, array $driver_options = array() )
 * @method \PDOStatement query( string $statement, int $mode, mixed $arg3, array $ctorargs____optional )
 * @method mixed quote( string $string, int $parameter_type = \PDO::PARAM_STR )
 * @method mixed rollBack()
 * @method boolean setAttribute( int $attribute , mixed $value )
 */
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
