<?php
	include './internal/error_codes.php';
	include './internal/db_access.php';
	include './internal/http_parameters_controller.php';
	include './internal/credential_management.php';
	header("Access-Control-Allow-Origin: *");
	
	$name =  _GET_("name");
	$username =  _GET_("username");
	$password =  _GET_("password");
	
	
	$handler = new DBHandler();	
	$handler -> mydb_open_connection("my_coinquilinipercaso");
	$conn = $handler -> conn;
	
	$c_manager = new CredentialManager($handler);
	
	if($c_manager -> existing_user($username))
		die(username_already_used);	
	
	$c_manager -> validate_with_name_or_die($name,$username,$password);
	
	$password = hash("sha512", $password);
	
	try {		
		$conn -> beginTransaction();
		
		$_query = "INSERT INTO user (name,username, password) VALUES (:name,:username,:password);";
		$stmt = $conn -> prepare($_query);
		$stmt -> bindParam(':name', $name);
		$stmt -> bindParam(':username', $username);
		$stmt -> bindParam(':password', $password);
		$stmt -> execute();
		
		$conn->commit();
		echo(successful_operation);
	}
	catch (\Throwable $e) {
		$conn->rollback();
		print failed_transition." ".$e;
	}
?>