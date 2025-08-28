<?php
session_start();

// Disable caching
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Set the content type header - very important!
header('Content-Type: image/png');

// random string
$captcha_string = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);

// Store the captcha string in the session
$_SESSION['captcha_text'] = $captcha_string;


$image = imagecreatetruecolor(120, 40); 


$bg_color = imagecolorallocate($image, 255, 255, 255); // White background
$text_color = imagecolorallocate($image, 0, 0, 0);     // Black text
$line_color = imagecolorallocate($image, 150, 150, 150); // Grey lines


imagefilledrectangle($image, 0, 0, 120, 40, $bg_color);


for ($i = 0; $i < 5; $i++) {
    imageline($image, 0, rand(0, 40), 120, rand(0, 40), $line_color);
}

$font = 5; 
$x = 10;
$y = 10;

imagestring($image, $font, $x, $y, $captcha_string, $text_color);

imagepng($image);

imagedestroy($image);
?>