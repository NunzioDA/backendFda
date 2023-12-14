<?php
	include './internal/error_codes.php';
	include './internal/db_access.php';
	include './internal/http_parameters_controller.php';
	include './internal/credential_management.php';
	
	$username = _GET_("username");
	$token = _GET_("token");
	$intercom = _GET_("intercom");
	$address = _GET_("address");
	$house_number = _GET_("house_number");
	$city = _GET_("city");
	
	$cred_manager = new CredentialManager();
	
	$db = new DBHandler();
	$db -> mydb_open_connection("my_coinquilinipercaso");
	$conn = $db -> conn;
	
	if(true)//$cred_manager -> matching_username_token($username, $token))
	{		
		$place_order = "SELECT count(*) FROM cart WHERE username=:username";
		$stmt = $conn -> prepare($place_order);
		$stmt -> bindParam(':username', $username);
		$stmt -> execute();
		$products_count = $stmt -> fetch(PDO::FETCH_NUM)[0];
	
		if($products_count){
			try{
				$conn -> beginTransaction();
				$place_order = "INSERT INTO placed_order (username, city, intercom, address, house_number, date_time, status) VALUES (:username, :city, :intercom, :address, :house_number, UTC_TIMESTAMP(), 'placed')";
				$stmt = $conn -> prepare($place_order);
				$stmt -> bindParam(':username', $username);
				$stmt -> bindParam(':city', $city);
				$stmt -> bindParam(':address', $address);
				$stmt -> bindParam(':intercom', $intercom);
				$stmt -> bindParam(':house_number', $house_number);
				$stmt -> execute();
				
				$select_last_inserted_id = "SELECT LAST_INSERT_ID();";
				$last_inserted_id = $conn->query($select_last_inserted_id) -> fetch(PDO::FETCH_NUM)[0];	
				
				
				$add_products_from_cart = "INSERT INTO order_include (order_id, product_id, price_at_the_time) SELECT :last_inserted, product_id, (SELECT price FROM product WHERE id = product_id) as price FROM cart WHERE username =:username";
				$stmt = $conn -> prepare($add_products_from_cart);
				$stmt -> bindParam(':username', $username);
				$stmt -> bindParam(':last_inserted', $last_inserted_id);
				$stmt -> execute();
				
				
				$delte_products_from_cart = "DELETE FROM cart WHERE username=:username";
				$stmt = $conn -> prepare($delte_products_from_cart);
				$stmt -> bindParam(':username', $username);
				$stmt -> execute();
				$conn->commit();
				echo successful_operation;
			}
			catch (\Throwable $e) {
				$conn->rollback();
				die(failed_transition." ".$e);
			}
		}
		else {
			echo bad_request." No products in the cart";
		}
	}
	else echo wrong_username_or_token;
?>