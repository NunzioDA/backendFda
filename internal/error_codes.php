<?php

	/*
	 *	This contains all messages codes, useful
	 *	for using the API.
	 *
	 *	All the codes will have a prefix : 'B'.
	 *	This will avoid chaos with standard HTTP errors.
	 */

	/*	
	 * 000 base code
	 * General
	 */

	define('successful_operation', 'B000');
	define('access_denied', 'B001');
	define('bad_request', 'B002');
	define('parameter_not_found', 'B003');
	define('wrong_request', 'B004');
	define('empty_result', 'B005');
	
	/*
	 * 100 base code
	 * DB Transition error codes
	 */

	define('failed_transition', 'B101');

	/*
	 * 200 base code
	 * Login Signin in error codes
	 */
	define('failed_username_validation', 'B201');
	define('failed_password_validation', 'B202');
	define('wrong_username_or_password', 'B203');
	define('username_already_used', 'B204');
	define('failed_name_validation', 'B205');
	define('wrong_username_or_token', 'B206');	
	
	/*
	 * 300 base code
	 * Appointments errors
	 */
	define('overlapping_extraordinary_closing', 'B301');
	define('overlapping_appointments', 'B302');
	define('starts_after_closing', 'B303');
	define('starts_before_opening', 'B304');
	define('ends_after_closing', 'B305');
	define('ends_before_opening', 'B306'); 	
	
	/*
	 * 900 base code
	 * Internal codes
	 */
	define('internal_api_error', 'B901');
	
	
?>