<?php
	include './internal/error_codes.php';
	include './internal/db_access.php';
	include './internal/http_parameters_controller.php';
	include './internal/permission_check.php';
	include './internal/credential_management.php';
	header("Access-Control-Allow-Origin: *");
	
	$username = _GET_("username");
	$token = _GET_("token");
	
	$cred_manager = new CredentialManager();
	
	$connection = $cred_manager -> db_user_handler -> conn;
	
	if($cred_manager -> matching_username_token($username, $token))
	{		
		$get_user = "SELECT username, profile_pic, name, has_permission FROM user WHERE username = :username";
		$stmt = $connection -> prepare($get_user);
		$stmt -> bindParam(':username', $username);
		$stmt -> execute();
		$result = $stmt->fetch();
		
		$json_result = json_encode($result);
		
		if($json_result != "false")
			echo($json_result);
		else echo(empty_result);
	}
	else echo wrong_username_or_token;