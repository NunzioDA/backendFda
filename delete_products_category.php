<?php
	include './internal/error_codes.php';
	include './internal/db_access.php';
	include './internal/credential_management.php';
	include './internal/http_parameters_controller.php';
	include './internal/permission_check.php';
	header("Access-Control-Allow-Origin: *");
	$username = _GET_("username");
	$token = _GET_("token");
	$name = _GET_("name");
	
	$cred_manager = new CredentialManager();
	$perm_checker = new PermissionChecker($cred_manager -> db_user_handler );
	
	$db = new DBHandler();
	$db -> mydb_open_connection("my_coinquilinipercaso");
	$conn_appointment = $db -> conn;
	
	if($cred_manager -> matching_username_token($username, $token))
	{
		if($perm_checker -> user_have_permissions($username))
		{			
			$remove_category = "DELETE FROM category WHERE name = :name";
			$stmt = $conn_appointment -> prepare($remove_category);
			$stmt -> bindParam(':name', $name);
			$stmt -> execute();	
			echo successful_operation;
		}
		else echo access_denied;
	}
	else echo wrong_username_or_token;
?>