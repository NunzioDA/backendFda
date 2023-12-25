<?php
	include './internal/error_codes.php';
	include './internal/db_access.php';
	include './internal/http_parameters_controller.php';
	include './internal/credential_management.php';
	header("Access-Control-Allow-Origin: *");
	
	$username = _GET_("username");
	$token = _GET_("token");
	$product_id = _GET_("product_id");
	
	
	if(array_key_exists('remove_all', $_GET))
	{
		$remove_all = $_GET["remove_all"];
	}
	else{
		$remove_all = 0;
	}
	
	$cred_manager = new CredentialManager();
	
	$db = new DBHandler();
	$db -> mydb_open_connection("my_coinquilinipercaso");
	$conn = $db -> conn;
	
	if($cred_manager -> matching_username_token($username, $token))
	{		
		$remove_product = "DELETE FROM cart WHERE username=:username AND product_id=:product_id";
		
		if($remove_all != "1")
		{
			$remove_product .= " LIMIT 1";
		}		
		
		$stmt = $conn -> prepare($remove_product);
		$stmt -> bindParam(':username', $username);
		$stmt -> bindParam(':product_id', $product_id);
		$stmt -> execute();
		echo successful_operation;
	}
	else echo wrong_username_or_token;
?>