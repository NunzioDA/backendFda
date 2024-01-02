<?php
	include './internal/error_codes.php';
	include './internal/db_access.php';
	include './internal/http_parameters_controller.php';
	include './internal/credential_management.php';
	header("Access-Control-Allow-Origin: *");

	$username = _GET_("username");
	$token = _GET_("token");

	$cred_manager = new CredentialManager();

	$db = new DBHandler();
	$db -> mydb_open_connection();
	$conn = $db -> conn;

	if($cred_manager -> matching_username_token($username, $token))
	{		
		$remove_product = "SELECT username, intercom, city, address, house_number, max(date_time) as max_date FROM `placed_order` WHERE username = :username GROUP BY intercom, city, address, house_number ORDER BY max_date DESC;";
		$stmt = $conn -> prepare($remove_product);
		$stmt -> bindParam(':username', $username);
		$stmt -> execute();
		$result = $stmt->fetchAll();
		
		$json_result = json_encode($result);
		
		if($json_result != "false")
			echo($json_result);
		else echo(empty_result);
	}
	else echo wrong_username_or_token;
?>