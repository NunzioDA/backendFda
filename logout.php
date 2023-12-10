<?php
	include './internal/error_codes.php';
	include './internal/db_access.php';
	include './internal/http_parameters_controller.php';
	include './internal/credential_management.php';
	header("Access-Control-Allow-Origin: *");
	
	$username = _GET_("username");
	$token = _GET_("token");
	
	$cred_manager = new CredentialManager();
	
	$connection = $cred_manager -> db_user_handler -> conn;
	
	if($cred_manager -> matching_username_token($username, $token))
	{
		$token = hash("sha512", $token);
		$token_deactivation_query = "UPDATE `login_token` SET `active` = '0' WHERE `login_token`.`token` = :token";
		$stmt = $connection -> prepare($token_deactivation_query);
		$stmt -> bindParam(':token', $token);
		$stmt -> execute();
		
		echo successful_operation;
	}
	else echo wrong_username_or_token;
?>