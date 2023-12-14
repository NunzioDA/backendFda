<?php
	include './internal/error_codes.php';
	include './internal/db_access.php';
	include './internal/http_parameters_controller.php';
	include './internal/credential_management.php';

	
	function resultToJson($result)
	{		
		
		foreach($result as $row)
		{
			$map[$row["product_id"]] = $row["p_count"];
		}
		if(!isset($map))
		{
			$map = false;
		}
		return json_encode($map);
	}

	$username = _GET_("username");
	$token = _GET_("token");

	$cred_manager = new CredentialManager();

	$db = new DBHandler();
	$db -> mydb_open_connection("my_coinquilinipercaso");
	$conn = $db -> conn;

	if($cred_manager -> matching_username_token($username, $token))
	{		
		$remove_product = "SELECT product_id, count(*) as p_count FROM `cart` WHERE username = :username GROUP BY product_id;";
		$stmt = $conn -> prepare($remove_product);
		$stmt -> bindParam(':username', $username);
		$stmt -> execute();
		$result = $stmt->fetchAll();
		
		$json_result = resultToJson($result);
		
		if($json_result != "false")
			echo($json_result);
		else echo(empty_result);
	}
	else echo wrong_username_or_token;
?>