<?php
	
	function getImagesDirectory()
	{
		$imagesPath = '../images';
		return $imagesPath;
	}
	
	
	function getImagePath()
	{
		$imagesPath = getImagesDirectory().'/';
		return $imagesPath;
	}
	
	function saveBase64Image($base64_encoded_string)
	{
		global $imagesPath;
		$name = bin2hex(openssl_random_pseudo_bytes(16));	
		$files = scandir(getImagesDirectory());
		
		$check = true;
		
		do{
			$check = true;
			foreach($files as $file){
				if(str_contains($file, $name)){
					$name = bin2hex(openssl_random_pseudo_bytes(16));
					$check = false;
				}
			}
		}while(!$check);
		
		$decodedImage = base64_decode($base64_encoded_string); 
		
		file_put_contents(getImagePath().$name, $decodedImage);
		
		return $name;
	}
?>