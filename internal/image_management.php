<?php
	
	function convertPNGto8bitPNGAndSave ($srcimage, $destPath) {
		$srcimage = imageCreateFromString($srcimage);
		$width  = imagesx($srcimage);
		$height = imagesy($srcimage);

		$img = imagecreatetruecolor($width, $height);
		$bga = imagecolorallocatealpha($img, 0, 0, 0, 127);
		imagecolortransparent($img, $bga);
		imagefill($img, 0, 0, $bga);
		imagecopy($img, $srcimage, 0, 0, 0, 0, $width, $height);
		imagetruecolortopalette($img, false, 255);
		imagesavealpha($img, true);

		imagepng($img, $destPath);
		imagedestroy($img);

	 }
	
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
		convertPNGto8bitPNGAndSave($decodedImage, getImagePath().$name);
		
		return $name;
	}
?>