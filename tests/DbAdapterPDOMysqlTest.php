<?php
// vendor/bin/phpunit --configuration phpunit_mysql.xml --coverage-text
namespace DBAdapterPDOMysqlTest;

use Exception;
use PDO;
use PHPUnit\Framework\TestCase;
use RTK\DB\DB;

class DBAdapterPDOMysqlTest extends TestCase {

	const INSTANCE_NAME = 'DBAdapterPDOMysqlTest';

	const TABLE_NAME = 'DBAdapterPDOMysqlTest';

	public static function setUpBeforeClass() {

		$settings = array(
			'type' => 'PDOMysql'
		);

		$map = array(
			'host'      => 'db_host',
			'user'      => 'db_username',
			'password'  => 'db_password',
			'database'  => 'db_database',
		);

		foreach ($map as $dest => $source) {
			if (isset($GLOBALS[$source])) {
				$settings[$dest] = $GLOBALS[$source];
			}
		}

		DB::addInstance(self::INSTANCE_NAME, $settings);

	}

	public function setUp() {

		$db = DB::get(self::INSTANCE_NAME);

		$res = $db->query('show tables like "' . self::TABLE_NAME . '"');
		$res->fetchAll(PDO::FETCH_ASSOC);

		if ($res->rowCount() !== 0) {
			throw new Exception('table "' . self::TABLE_NAME . '" should not exist in test database');
		}

		$sql = 'CREATE TABLE ' . self::TABLE_NAME . '('
			. 'id INT NOT NULL AUTO_INCREMENT,'
			. 'foo VARCHAR(255),'
			. 'bar VARCHAR(255),'
			. 'PRIMARY KEY (id)'
			. ')';

		$db->exec($sql);

	}

	public function tearDown() {

		$db = DB::get(self::INSTANCE_NAME);
		$db->exec('DROP TABLE ' . self::TABLE_NAME);

	}

	public function testConnectd() {
		
		$db = DB::get(self::INSTANCE_NAME);
		$this->assertTrue($db->isConnected());

	}

	public function testReconnect() {

		$initial_timeout = '';
		$desired_timeout = '1';
		$current_timeout = '0';

		$db = DB::get(self::INSTANCE_NAME);

		$q = $db->query('SHOW VARIABLES WHERE variable_name = "wait_timeout"');
		while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
			if ($row['Variable_name'] == 'wait_timeout') {
				$initial_timeout = $row['Value'];
			}
		}

		$db->query("SET SESSION wait_timeout = $desired_timeout;");

		$q = $db->query('SHOW VARIABLES WHERE variable_name = "wait_timeout"');
		while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
			if ($row['Variable_name'] == 'wait_timeout') {
				$current_timeout = $row['Value'];
			}
		}

		#echo "\n\nTimeouts:\ninitial: $initial_timeout, desired: $desired_timeout, current: $current_timeout\n\n";

		$this->assertEquals($desired_timeout, $current_timeout);

		sleep(2);

		$this->assertFalse($db->isConnected());

		$db = DB::get(self::INSTANCE_NAME);

		$this->assertTrue($db->isConnected());

	}

	public function testLastInsertId() {

		$db = DB::get(self::INSTANCE_NAME);

		$res = $db->query("INSERT INTO " . self::TABLE_NAME . " set foo = 1, bar = 2;");
		$this->assertEquals($db->lastInsertId(), 1);

		$res = $db->query("INSERT INTO " . self::TABLE_NAME . " set foo = 1, bar = 2;");
		$this->assertEquals($db->lastInsertId(), 2);

	}

	public function testFetch() {

		$db = DB::get(self::INSTANCE_NAME);

		$db->query("INSERT INTO " . self::TABLE_NAME . " set foo = 'something', bar = 'elsething'");

		$res = $db->query('SELECT foo, bar FROM ' . self::TABLE_NAME);
		while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
			$this->assertEquals(
				$row,
				array(
					'foo' => 'something',
					'bar' => 'elsething',
				)
			);
		}

	}

	public function testFetchAll() {

		$db = DB::get(self::INSTANCE_NAME);

		$db->query("INSERT INTO " . self::TABLE_NAME . " set foo = 'something', bar = 'elsething'");

		$res = $db->query('SELECT foo, bar FROM ' . self::TABLE_NAME);
		$rows = $res->fetchAll(PDO::FETCH_ASSOC);

		$this->assertEquals(
			$rows,
			array(
				array(
					'foo' => 'something',
					'bar' => 'elsething',
				)
			)
		);

	}

}
