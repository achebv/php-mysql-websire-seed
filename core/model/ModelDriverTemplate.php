<?php 

/**
 * Template for each Database Driver
 * @author florin
 *
 */
interface ModelDriverTemplate{
	

	/**
	 * Setup SQL phrase type
	 * @param String $type
	 * @return void
	 */
	public function initSql($type);
	
	
	/**
	 * Setup where statment
	 * @param String $w
	 * @return void
	 */
	public function setupWhere($w);


	/**
	 * Access the SQL phrase
	 * @return String
	 */
	public function getSql();
	

	/**
	 * Overwrite SQL phrase by $s
	 * @param String $s
	 * @return void
	 */
	public function setSql($s);


	/**
	 * The sql statment RUN here
	 * @return 
	 */
	public function doSql();

	
	/**
	 * Set query fields...
	 */
	public function setFields($f);
	
}
