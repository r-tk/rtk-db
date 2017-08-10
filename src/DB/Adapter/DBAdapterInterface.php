<?php
namespace RTK\DB\Adapter;

interface DBAdapterInterface {

	/**
	 * create a new instance, does NOT need to open db connection
	 *
	 * @param array $settings
	 */
	public function __construct($settings);

	/**
	 * called every time instance is requested
	 * should ensure an instance with open connection is returned
	 *
	 * @return DBAdapterInterface
	 */
	public function getInstance();

	/**
	 * (re)open the database connection
	 */
	public function open();

	/**
	 * close the database connection
	 */
	public function close();

	 /**
	  * checks if the connection is actually open
	  *
	  * @return boolean
	  */
	public function isConnected();

}
