<?php
	include './internal/error_codes.php';
	include './internal/db_access.php';
	include './internal/http_parameters_controller.php';
	include './internal/permission_check.php';
	include './internal/credential_management.php';
	include './internal/image_management.php';
	
	header("Access-Control-Allow-Origin: *");
	
	$username = _GET_("username");
	$token = _GET_("token");
	$name = _GET_("name");
	$description = _GET_("description");
	$price = _GET_("price");
	$category_name = _GET_("category_name");
	$image = _POST_('image');
	
	$cred_manager = new CredentialManager();
	$perm_checker = new PermissionChecker($cred_manager -> db_user_handler);
	
	$db = new DBHandler();
	$db -> mydb_open_connection();
	$conn = $db -> conn;
	
	if($cred_manager -> matching_username_token($username, $token))
	{		
		if($perm_checker -> user_have_permissions($username))
		{			
			$image_name = saveBase64Image($image);
	
			$create_category_query = "INSERT INTO product (name, description, price, image, category_name) VALUES (:name, :description, :price, :image, :category_name)";
			$stmt = $conn -> prepare($create_category_query);
			$stmt -> bindParam(':name', $name);
			$stmt -> bindParam(':description', $description);
			$stmt -> bindParam(':price', $price);
			$stmt -> bindParam(':image', $image_name);
			$stmt -> bindParam(':category_name', $category_name);
			$stmt -> execute();
			echo successful_operation;
		}else echo access_denied;
	}
	else echo wrong_username_or_token;
?>