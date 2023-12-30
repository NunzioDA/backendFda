<?php
	
	define('min_pass_len', 6);
	define('min_username_len', 3);	
	define('min_name_len', 3);	

	class CredentialManager
	{
		public $db_user_handler = NULL;
		
		function __construct(DBHandler $handler = null)
		{
			if(is_null($handler))
			{
				$this -> db_user_handler = new DBHandler();
				$this -> db_user_handler -> mydb_open_connection("my_coinquilinipercaso");
			}
			else $this -> db_user_handler = $handler;
		}
		
		function common_characters_count($string1, $string2)
		{
			$string1_arr = str_split($string1);
			$string2_arr = str_split($string2);

			$common = implode(array_unique(array_intersect($string1_arr, $string2_arr)));
			return strlen($common);
		}

		function have_common_characters($string1, $string2)
		{
			return  $this -> common_characters_count($string1, $string2) > 0;
		}
		
		function validate_password($password)
		{
			$pass_special_char = "~`!@#$%^&*()_-+={[}]|:;\\\"'<,>.?/";
			
			$uppercase = preg_match('@[A-Z]@', $password);
			$lowercase = preg_match('@[a-z]@', $password);
			$number    = preg_match('@[0-9]@', $password);
			$specialChars = $this -> have_common_characters($pass_special_char, $password);
			$pass_len = strlen($password) >= min_pass_len;
			
			$result = $uppercase && $lowercase && $number && $specialChars && $pass_len;
			return $result;
		}
		
		function validate_name($name)
		{
			$name_len = strlen($name) >= min_name_len;
						
			$regex = preg_match('/^[a-zA-Z]+$/', $name);
			
			$result = $regex && $name_len;
			
			return $result;
		}
		
		function validate_username($username)
		{	
			$username_len = strlen($username) >= min_username_len;
						
			$regex = preg_match("/^(\_?[a-zA-Z]+\_?[a-zA-Z]*)+$/", $username);
			
			$result = $regex && $username_len;
			
			return $result;
		}
		
		/*
		 *	include 'error_codes.php' before calling this function
		 */
		
		function validate_or_die($username,$password)
		{
			if(!$this -> validate_password($password))
			{
				die(failed_password_validation);
			}

			if(!$this -> validate_username($username))
			{
				die(failed_username_validation);
			}
		}		

		function validate_with_name_or_die($name, $username,$password)
		{
			$this -> validate_or_die($username,$password);		
			
			if(!$this -> validate_name($name))
			{
				die(failed_name_validation);
			}
		}

		
		function matching_username_password(string $username, string $password)
		{
			$conn = $this -> db_user_handler -> conn;
			
			$password = hash("sha512", $password);			
				
			$_query = "SELECT password FROM user WHERE username = BINARY :username;";
			$stmt = $conn -> prepare($_query);
			$stmt -> bindParam(':username', $username);
			$stmt -> execute();
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			return $result && strcmp($password, $result["password"]) == 0;
		}
		
		
		function matching_username_token(string $username, string $token)
		{
			$conn = $this -> db_user_handler -> conn;
			
			if(!$this -> validate_username($username))
				die(failed_username_validation);
			
			$token = hash("sha512", $token);			
				
			$_query = "SELECT token FROM login_token WHERE username = BINARY :username AND token = :token AND active <> 0"; //AND DATE_ADD(creation_datetime,INTERVAL 5 DAY) >= UTC_TIMESTAMP();";
			$stmt = $conn -> prepare($_query);
			$stmt -> bindParam(':username', $username);
			$stmt -> bindParam(':token', $token);
			$stmt -> execute();
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			return $result && strcmp($token, $result["token"]) == 0;
		}
		
		/**
		*	You need to 
		*   include 'db_access.php'
		*
		*   and call
		*   mydb_open_connection("my_bams");	
		*   before calling existing_user
		*/

		
		function existing_user($username)
		{
			$conn = $this -> db_user_handler -> conn;
			
			$_query = "SELECT id FROM user WHERE username = :username;";
			$stmt = $conn -> prepare($_query);
			$stmt -> bindParam(':username', $username);
			$stmt -> execute();
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
				
			return $result;
		}		
	}
?>