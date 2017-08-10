<?php
// vendor/bin/phpunit --configuration phpunit_mysql.xml --coverage-text
namespace DBAdapterMysqliTest;

use PHPUnit\Framework\TestCase;
use RTK\DB\DB;

class DBAdapterMysqliTest extends TestCase {

	const INSTANCE_NAME = 'DBAdapterMysqliTest';

	public function setUp() {

		$settings = array(
			'type' => 'mysqli'
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
		while ($row = $q->fetch_array(MYSQLI_ASSOC)) {
			if ($row['Variable_name'] == 'wait_timeout') {
				$initial_timeout = $row['Value'];
			}
		}

		$db->query("SET SESSION wait_timeout = $desired_timeout;");

		$q = $db->query('SHOW VARIABLES WHERE variable_name = "wait_timeout"');
		while ($row = $q->fetch_array(MYSQLI_ASSOC)) {
			if ($row['Variable_name'] == 'wait_timeout') {
				$current_timeout = $row['Value'];
			}
		}

		$this->assertEquals($desired_timeout, $current_timeout);

		sleep(2);

		$this->assertFalse($db->isConnected());

		$this->db = DB::get(self::INSTANCE_NAME);

		$this->assertTrue($db->isConnected());

	}

}
