# RTK DB
DB connections manager with some unifying between adapters

[![Latest version](https://img.shields.io/packagist/v/r-tk/rtk-db.svg)](https://packagist.org/packages/r-tk/rtk-db)
[![travis-ci](https://travis-ci.org/r-tk/rtk-db.svg?branch=master)](https://travis-ci.org/r-tk/rtk-db)
[![Coverage Status](https://coveralls.io/repos/github/r-tk/rtk-db/badge.svg?branch=master)](https://coveralls.io/github/r-tk/rtk-db?branch=master)
[![License](https://img.shields.io/packagist/l/r-tk/rtk-db.svg)](https://packagist.org/packages/r-tk/rtk-db)
[![Total Downloads](https://img.shields.io/packagist/dt/r-tk/rtk-db.svg)](https://packagist.org/packages/r-tk/rtk-db)



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
