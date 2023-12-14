<?php
	include './internal/error_codes.php';
	include './internal/db_access.php';
	include './internal/http_parameters_controller.php';
	include './internal/credential_management.php';
	
	$username = _GET_("username");
	$token = _GET_("token");
	$product_id = _GET_("product_id");
	
	$cred_manager = new CredentialManager();
	
	$db = new DBHandler();
	$db -> mydb_open_connection("my_coinquilinipercaso");
	$conn = $db -> conn;
	
	if($cred_manager -> matching_username_token($username, $token))
	{		
		$remove_product = "DELETE FROM cart WHERE username=:username AND product_id=:product_id LIMIT 1";
		$stmt = $conn -> prepare($remove_product);
		$stmt -> bindParam(':username', $username);
		$stmt -> bindParam(':product_id', $product_id);
		$stmt -> execute();
		echo successful_operation;
	}
	else echo wrong_username_or_token;
?>