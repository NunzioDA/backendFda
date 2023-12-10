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
	
	/*
	* 1999-02-21 will be used as a dummy date to 
	* compare appointment interval with salon 
	* opening hours in on "o_h_datetime" view in the database.
	*
	* 1999-02-21 was a sunday so it will be our day of week 0.
	* To get the corresponding date of the appointment 
	* we will add the current day of week to the dummy date.
	* This will be compared to onpening and closing dates in 
	* the view.
	*/
	function is_salon_open($salon_id, $time_offset, $interval_date_start, $duration, $conn)
	{
		$week_day = date('w', strtotime($interval_date_start));
		
		$format = 'Y-m-d H:i:s';
		$date = DateTime::createFromFormat($format, $interval_date_start);
		$time = $date->format('H:i:s');
		
		$interval_end_date = $date -> add(new DateInterval('PT' . $duration . 'M'));		
		$interval_end_date_str  = $interval_end_date -> format($format);
		
		$check_query = "SELECT count(*) FROM extraordinary_closing_hours WHERE salon_id = :salon_id AND ((:interval_start_date BETWEEN `start` AND `end` AND :interval_start_date <> `end`) OR (:interval_end_date BETWEEN `start` AND `end` AND :interval_end_date <> `start`) OR (`start` BETWEEN :interval_start_date AND :interval_end_date))";
		
		$stmt = $conn -> prepare($check_query);
		$stmt -> bindParam(':salon_id', $salon_id);
		$stmt -> bindParam(':interval_start_date', $interval_date_start);
		$stmt -> bindParam(':interval_end_date', $interval_end_date_str);
		$stmt -> execute();
		
		$result = $stmt->fetch(PDO::FETCH_NUM)[0];
		
		$return_value = $result == 0;
		
		if($return_value) // no extraordinary closing hours found
		{		
			$interval_start_to_dummy_date = "1999-02-21 $time";		
			
			$dummy_date = DateTime::createFromFormat($format, $interval_start_to_dummy_date);		
			$dummy_date ->modify("+{$week_day} days");		
			$interval_start_to_dummy_date = $dummy_date -> format($format);	
			
			$interval_end_dummy_date = $dummy_date -> add(new DateInterval('PT' . $duration . 'M'));		
			$interval_end_dummy_date = $interval_end_dummy_date -> format($format);
			
			$o_h_datetime = "select timestamp('1999-02-21',`my_bams`.`opening_hours`.`open`) + interval `my_bams`.`opening_hours`.`week_day` day - interval :time_zone hour AS `open_date`,timestamp('1999-02-21',`my_bams`.`opening_hours`.`close`) + interval `my_bams`.`opening_hours`.`week_day` day - interval :time_zone hour AS `close_date`,`my_bams`.`opening_hours`.`salon_id` AS `salon_id` from `my_bams`.`opening_hours` where `my_bams`.`opening_hours`.`open` < `my_bams`.`opening_hours`.`close` union select timestamp('1999-02-21',`my_bams`.`opening_hours`.`open`) + interval `my_bams`.`opening_hours`.`week_day` day - interval :time_zone hour AS `open_date`,timestamp('1999-02-21',`my_bams`.`opening_hours`.`close`) + interval 1 + `my_bams`.`opening_hours`.`week_day` day - interval :time_zone hour AS `close_date`,`my_bams`.`opening_hours`.`salon_id` AS `salon_id` from `my_bams`.`opening_hours` where `my_bams`.`opening_hours`.`open` > `my_bams`.`opening_hours`.`close`";
			$check_query = "SELECT count(*) FROM ($o_h_datetime) AS o_h_datetime WHERE salon_id = :salon_id AND (:interval_start_to_dummy_date BETWEEN `open_date` AND `close_date`) AND  (:interval_end_dummy_date BETWEEN `open_date` AND `close_date`)";
			
			$stmt = $conn -> prepare($check_query);
			$stmt -> bindParam(':salon_id', $salon_id);
			$stmt -> bindParam(':time_zone', $time_offset);
			$stmt -> bindParam(':interval_start_to_dummy_date', $interval_start_to_dummy_date);
			$stmt -> bindParam(':interval_end_dummy_date', $interval_end_dummy_date);
			$stmt -> execute();
			
			$result = $stmt->fetch(PDO::FETCH_NUM)[0];
			$return_value = $result > 0;
		}
			
		return $return_value;
	}
?>