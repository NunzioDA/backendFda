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
		echo("YES");
	}
	else echo("NO");
?>