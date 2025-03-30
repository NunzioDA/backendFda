<?php
	/**
	*	You need to 
	*   include 'db_access.php'
	*
	*/


	function existing_row($table, $column_name, $value, $conn)
	{
		
		$check_query = "SELECT count(*) FROM {$table} WHERE {$column_name} = :value;";
		$stmt = $conn -> prepare($check_query);
		$stmt -> bindParam(':value', $value);
		$stmt -> execute();
		
		$result = $stmt->fetch(PDO::FETCH_NUM);
		return $result[0] > 0;
	}
	
	function existing_row_matching_values($table, $column_name, $value, $matching_column, $value_m, $conn)
	{
		
		$check_query = "SELECT count(*) FROM {$table} WHERE {$column_name} = :value AND {$matching_column} = :value_m;";
		$stmt = $conn -> prepare($check_query);
		$stmt -> bindParam(':value', $value);
		$stmt -> bindParam(':value_m', $value_m);
		$stmt -> execute();
		
		$result = $stmt->fetch(PDO::FETCH_NUM);
			
		return $result[0] > 0;
	}
?>
