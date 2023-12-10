<?php
	/**
	*	You need to 
	*   include 'db_access.php'
	*
	*/
	
	// management levels
	define('god', 0);
	define('admin', 1);
	define('customer_service', 2);
	
	// barber levels
	define('b_owner', 0);
	define('b_manager', 1);
	define('b_smm', 2);
	
	// salon levels
	define('s_manager', 0);
	define('s_employee', 1);
	
	// general const
	define('ignore_permissions', -1); // this allows to ignore salon permission or/and management permission
	define('access_denied_', -1); // this allows to ignore salon permission or/and management permission
	define('any', PHP_INT_MAX);
	define('none', -1);

	class PermissionChecker
	{
		public $db_users_handler = NULL;
		
		function __construct(DBHandler $db_users_handler)
		{
			$this -> db_users_handler = $db_users_handler;
		}
		
		
		function user_have_permissions($username)
		{						
			$conn = $this -> db_users_handler -> conn;
			
			$management_permission_query = "SELECT has_permission FROM user WHERE username = :username AND has_permission=TRUE";			
			$stmt = $conn -> prepare($management_permission_query);
			$stmt -> bindParam(':username', $username);	
			$stmt -> execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);		
			
			return $result;
		}
	}	

?>