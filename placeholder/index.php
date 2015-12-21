<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2015.07.18.
 * Time: 14:43
 */

if(isset($_GET)){
	$getdata = explode('/',$_GET['data']);
	if (strpos(strtolower($getdata[0]), "x")) {
		$tmpdata = explode("x", strtolower($getdata[0]));
		unset($getdata[0]);
		$getdata = array_merge($tmpdata, $getdata);
	}
	$default = array(
		100,
		100,
		'CCCCCC',
		'979797',
	);
	$imagedata = array();
	foreach ($default as $key => $value) {
		if (isset($getdata[$key])) {
			$imagedata[] = $getdata[$key];
		} else {
			$imagedata[] = $value;
		}
	}

	create_image($imagedata[0],
		$imagedata[1],
		$imagedata[2],
		$imagedata[3]);
	exit;
}


function create_image($width, $height, $bg_color, $txt_color )

{

	$text = "$width X $height";

	//Create the image resource
	$image = ImageCreate($width, $height);
	//Making of colors, we are changing HEX to RGB
	$bg_color = ImageColorAllocate($image,
		base_convert(substr($bg_color, 0, 2), 16, 10),
		base_convert(substr($bg_color, 2, 2), 16, 10),
		base_convert(substr($bg_color, 4, 2), 16, 10));


	$txt_color = ImageColorAllocate($image,
		base_convert(substr($txt_color, 0, 2), 16, 10),
		base_convert(substr($txt_color, 2, 2), 16, 10),
		base_convert(substr($txt_color, 4, 2), 16, 10));

	//Fill the background color
	ImageFill($image, 0, 0, $bg_color);
	//Calculating font size
	$fontsize = ($width>$height)? ($height / 8) : ($width / 8) ;

	//Inserting Text

	imagettftext($image,$fontsize, 0,
		($width/2) - ($fontsize * 2.75),
		($height/2) + ($fontsize* 0.2),
		$txt_color, '../backend/thirdparty/ImageWorkshop/tests/Resources/fonts/arial.ttf', $text);

	//Tell the browser what kind of file is come in
	header("Content-Type: image/png");
	header("Content-disposition: inline; filename=" . $width . "x" . $height . ".png");
	//Output the newly created image in png format
	imagepng($image);
	//Free up resources
	ImageDestroy($image);
}