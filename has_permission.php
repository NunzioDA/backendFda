<?php
	include './internal/error_codes.php';
	include './internal/db_access.php';
	include './internal/http_parameters_controller.php';
	include './internal/credential_management.php';
	include './internal/permission_check.php';
	include './internal/utilities.php';
	header("Access-Control-Allow-Origin: *");
	
	$username = _GET_("username");

	$db_user = new DBHandler();
	$db_user -> mydb_open_connection();
	
	$permission_checker = new PermissionChecker($db_user);
	
	if($permission_checker -> user_have_permissions($username))
	{
		echo("YES");
	}
	else echo("NO");
	
?>