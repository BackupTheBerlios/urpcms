<?php

// ==================================================================
// | UrPCMS : Your Powerful Content Management System
// ==================================================================
// |
// | Copyright (c) 2003 UrStudios.Net by Bruno Cluizel
// | http://www.urpcms.com
// |
// | This program is free software; you can redistribute it and/or
// | modify it under the terms of the GNU General Public License as
// | as published by the Free Software Foundation; version 2 of the License.
// |
// | img_maker.php : V 0.0.2
// ==================================================================

$op = $_GET["op"];
$txt = $_GET["txt"];

switch ($op) {

	case "jpgcode";
	mk_txtjpg($txt);
	break;

	case "pngcode";
	mk_txtpng($txt);
	break;
}

function mk_txtjpg($txt) {
	header("Content-type: image/jpeg");
	$im = imagecreatefromjpeg("pics/back-code.jpg");
	$txt_color = imagecolorallocate($im, 123, 156, 189);
	imagestring($im, 5, 26, 2,  $txt, $txt_color);
	imagejpeg($im);
	imagedestroy($im);
	die();
}

function mk_txtpng($txt) {
	header("Content-type: image/png");
	$im = imagecreatefrompng("pics/back-code.png");
	$txt_color = imagecolorallocate($im, 123, 156, 189);
	imagestring($im, 5, 26, 2,  $txt, $txt_color);
	imagepng($im);
	imagedestroy($im);
	die();
}

?>
