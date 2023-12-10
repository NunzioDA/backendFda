<?php
	class DBHandler
	{
		public $conn = NULL;
	
		function mydb_open_connection($dbname)
		{			
			$this -> close_connection();
			
			$host = "localhost";
			$username = "root";
			$password = "";
			try {
				$this -> conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
			} catch (PDOException $pe) {
				die("Could not connect to the database:" . $pe->getMessage());
			}		
		}
		
		function close_connection()
		{
			if(!is_null($this -> conn))
			{
				$this -> conn = NULL;
			}
		}
		
	}

	
?>