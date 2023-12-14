<?php
	include './internal/image_management.php';
	include './internal/http_parameters_controller.php';
	header("Access-Control-Allow-Origin: *");
	
	$image_name = _GET_("image");
	$file_url = getImagePath().$image_name;
	$image = imagecreatefromstring(file_get_contents($file_url));
	
	header('Content-type: image/gif');
	
	imagepng($image);
?>