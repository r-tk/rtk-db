<?php
// vendor/bin/phpunit --configuration phpunit_mysql.xml --coverage-text
namespace DBAdapterPDOMysqlTest;

use PDO;
use PHPUnit\Framework\TestCase;
use RTK\DB\DB;

class DBAdapterPDOMysqlTest extends TestCase {

	const INSTANCE_NAME = 'DBAdapterPDOMysqlTest';

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

}
