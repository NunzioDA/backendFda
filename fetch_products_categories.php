<?php
	include './internal/error_codes.php';
	include './internal/db_access.php';
	include './internal/http_parameters_controller.php';	
	header("Access-Control-Allow-Origin: *");
	
	function getCategoryProducts(&$category, $connection)
	{
		$get_product = "SELECT * FROM product WHERE category_name = :category_name AND active=True";
		$stmt = $connection -> prepare($get_product);
		$stmt -> bindParam(':category_name', $category["name"]);
		$stmt -> execute();
		$result = $stmt->fetchAll();		
		
		$category["products"] = $result;		
	}
	
	$handler = new DBHandler();
	$handler -> mydb_open_connection("my_coinquilinipercaso");				
	
	$conn = $handler -> conn;		

	$get_category = "SELECT * FROM category";
	$stmt = $conn -> prepare($get_category);
	$stmt -> execute();
	$result = $stmt->fetchAll();

	foreach($result as &$category)
	{
		getCategoryProducts($category, $conn);
	}

	$json_result = json_encode($result);

	if($json_result != "false")
		echo($json_result);
	else echo(empty_result);
?>