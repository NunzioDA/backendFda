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
	$conn = $db -> conn;
	
	if($cred_manager -> matching_username_token($username, $token))
	{
		if($perm_checker -> user_have_permissions($username))
		{			
			try{
				$conn -> beginTransaction();
				$remove_category = "DELETE FROM category WHERE name = :name";
				$stmt = $conn -> prepare($remove_category);
				$stmt -> bindParam(':name', $name);
				$stmt -> execute();	
				
				$disable_products = "UPDATE product SET active=0 WHERE category_name=:name";
				$stmt = $conn -> prepare($disable_products);
				$stmt -> bindParam(':name', $name);
				$stmt -> execute();	
				$conn->commit();
				echo successful_operation;
			}
			catch(e){
				$conn->rollback();
				die(failed_transition." ".$e);
			}
		}
		else echo access_denied;
	}
	else echo wrong_username_or_token;
?>