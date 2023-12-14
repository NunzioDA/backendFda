<?php
	include './internal/error_codes.php';
	include './internal/db_access.php';
	include './internal/http_parameters_controller.php';
	include './internal/credential_management.php';
	include './internal/permission_check.php';
	header("Access-Control-Allow-Origin: *");
	
	function getOrderProducts(&$order, $connection)
	{
		$get_product = "SELECT product.id, product.name, product.description, product.image, ".
						"product.category_name as product_count, (SELECT price_at_the_time ".
						"FROM order_include WHERE order_id = :order_id AND product_id=product.id LIMIT 1) as price,".
						" count(*) as product_count FROM product RIGHT JOIN order_include ON product.id = order_include.product_id".
						" WHERE order_id = :order_id GROUP BY product.id;";
		$stmt = $connection -> prepare($get_product);
		$stmt -> bindParam(':order_id', $order["id"]);
		$stmt -> execute();
		$result = $stmt->fetchAll();		
		
		$order["products"] = $result;		
	}

	$username = _GET_("username");
	$token = _GET_("token");
	$mode = _GET_("mode");
	
	if($mode != "management" && $mode != "normal")
	{
		die("Incorrect mode");
	}

	$cred_manager = new CredentialManager();
	$perm_checker = new PermissionChecker($cred_manager -> db_user_handler );

	$db = new DBHandler();
	$db -> mydb_open_connection("my_coinquilinipercaso");
	$conn = $db -> conn;

	if($cred_manager -> matching_username_token($username, $token))
	{		
		$query_user = " WHERE username = :username;";

		if($mode == "management" && $perm_checker -> user_have_permissions($username))
		{
			$query_user =";";
		}
		else if($mode == "management") die(access_denied);

		$select_placed_orders = "SELECT * FROM placed_order".$query_user;
		$stmt = $conn -> prepare($select_placed_orders);
		if($query_user !=";")
			$stmt -> bindParam(':username', $username);
		$stmt -> execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($result as &$order)
		{
			getOrderProducts($order, $conn);
		}
		
		$json_result = json_encode($result);
		
		if($json_result != "false")
			echo($json_result);
		else echo(empty_result);
	}
	else echo wrong_username_or_token;
?>