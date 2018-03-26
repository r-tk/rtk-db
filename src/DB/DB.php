<?php
namespace RTK\DB;

class DB {

	/**
	 * list of DB instances
	 */
	private static $instances = array();

	/**
	 * list of initialized DB instances
	 */
	private static $connections;

	/**
	 * add DB instance
	 *
	 * @param string $name
	 * @param array $settings
	 * @throws DBException
	 */
	public static function addInstance($name, $settings) {

		if (empty($name) || empty($settings)) {
			return;
		}

		if (empty($settings['type'])) {
			throw new DBException('type not set for db instance ' . $name);
		}

		self::$instances[$name] = $settings;

	}

	/**
	 * quick-access to get instance of a database connection
	 *
	 * @param string $name
	 * @return Adapter\mysqli|Adapter\PDO|Adapter\PDOMysql|Adapter\DBAdapterInterface
	 * @throws DBException
	 */
	public static function get($name = 'default') {

		if (isset(self::$connections[$name])) {
			return self::$connections[$name]->getInstance();
		}

		if (!isset(self::$instances[$name])) {
			throw new DBException('db instance "'.$name.'" has not been defined');
		}

		$instance_config = self::$instances[$name];
		$adapter = 'RTK\\DB\\Adapter\\' . ucfirst($instance_config['type']);

		if (!class_exists($adapter)) {
			throw new DBException('Failed to load DB adapter ' . $adapter);
		}

		$instance = new $adapter($instance_config);

		if (!in_array(
			'RTK\\DB\\Adapter\\DBAdapterInterface',
			class_implements($instance)
		)) {
			throw new DBException('DB Adapter '
				 . $instance_config['type']
				 . ' is not implementing DBAdapterInterface');
		}

		self::$connections[$name] = $instance;

		return $instance->getInstance();

	}

}
