# RTK DB
DB connections manager with some unifying between adapters

## Define connection
```php
use RTK\DB\DB;

DB::addInstance('default', array(
	'type'      => 'mysqli',
	'host'      => 'localhost',
	'user'      => 'root',
	'password'  => '',
	'database'  => 'test',
));
// or
DB::addInstance('default', array(
	'type'      => 'PDOMysql',
	'host'      => 'localhost',
	'user'      => 'root',
	'password'  => '',
	'database'  => 'test',
));
// or
DB::addInstance('default', array(
	'type'      => 'PDOMysql',
	'dsn'      => 'mysql:dbname=test;host=localhost',
	'user'      => 'root',
	'password'  => '',
));
```
## Get connection instance
```php
use RTK\DB\DB;

$db = DB::get('default');

// omitting instance name will return instance 'default'
$db = DB::get();
```
