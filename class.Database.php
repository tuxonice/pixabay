<?php
/**
* Database connector class
* @subpackage Database
*/
class Database {
	
	public $dbh = NULL;
	static $_instance;
	
	
	
	public static function getInstance($config = NULL){
	
		if((self::$_instance instanceof self))
			return self::$_instance;
		elseif(!is_null($config))
			self::$_instance = new self($config['host'],
				 $config['username'],
				 $config['password'], 
				 $config['dbname']);
		else
			die('Error Connecting to DB!');
			
	return self::$_instance;
	}
	
	

    private	function __construct( $host='localhost', $user, $pass, $db='') {
		
		
	   try{
	       $this->dbh = new PDO("mysql:host=$host;dbname=$db", $user, $pass, $driver_options = array());
	       $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	   }catch(PDOException $e){
	   	
	   	echo($e->getMessage());
	   	exit;
	   	
	   }
		
        $this->dbh->exec("SET NAMES 'utf8'");
		
	}
	
   
}