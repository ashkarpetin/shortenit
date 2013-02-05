<?php

class Db
{
    private static $db;

    public static function instance()
    {
    	if (!self::$db)    		
    	{
    		try 
      		{
         		self::$db = new Predis\Client(array(
	            "scheme" => "tcp",
	            "host" => DB_HOST,
	            "port" => DB_PORT
	         	)); 
         		self::$db->connect();  
      		}
		      catch (Exception $e) 
          {
		        	die("Couldn't connect to Redis");
		    	}
    		}

    	return self::$db;
    }
}