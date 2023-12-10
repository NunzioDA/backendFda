<?php
	/*
	 *	This script, after validating and checking
	 * 	username and password, will return a token useful
	 *	for basic API services.
	 *	
	 */

	include './internal/error_codes.php';
	include './internal/db_access.php';
	include './internal/credential_management.php';
	include './internal/http_parameters_controller.php';
	header("Access-Control-Allow-Origin: *");
	
	$username = _GET_("username");
	$password =  _GET_("password");
	
	$handler = new DBHandler();	
	$handler -> mydb_open_connection("my_coinquilinipercaso");
	$conn = $handler -> conn;
	
	$c_manager = new CredentialManager($handler);
	
	$c_manager -> validate_or_die($username,$password);
	
	try {		
		if($c_manager -> matching_username_password($username, $password))
		{
			/*
			 * Succesfully logged in
			 * 
			 * creating a 32 char token.
			 */
			$token = bin2hex(openssl_random_pseudo_bytes(16));
			
			$hashed_token = hash("sha512", $token);
			
			$conn -> beginTransaction();
		
			$_query = "INSERT INTO login_token (token, username, creation_datetime) VALUES (:token, :username, NOW());";
			$stmt = $conn -> prepare($_query);
			$stmt -> bindParam(':token', $hashed_token);
			$stmt -> bindParam(':username', $username);			
			$stmt -> execute();
			
			$conn->commit();
			
			echo ($token.".token");
		}
		else{
			echo wrong_username_or_password;	
		}	
	}
	catch (\Throwable $e) {
		print failed_transition." ".$e;
	}
?>