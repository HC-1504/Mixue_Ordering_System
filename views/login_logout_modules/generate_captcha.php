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

// Create a random string for the captcha
$captcha_string = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);

// Store the captcha string in the session
$_SESSION['captcha_text'] = $captcha_string;

// Create the image
$image = imagecreatetruecolor(120, 40); // Width, Height

// Set colors
$bg_color = imagecolorallocate($image, 255, 255, 255); // White background
$text_color = imagecolorallocate($image, 0, 0, 0);     // Black text
$line_color = imagecolorallocate($image, 150, 150, 150); // Grey lines

// Fill background
imagefilledrectangle($image, 0, 0, 120, 40, $bg_color);

// Add some random lines to make it harder for bots
for ($i = 0; $i < 5; $i++) {
    imageline($image, 0, rand(0, 40), 120, rand(0, 40), $line_color);
}

// Add the captcha string to the image
// You might need to specify a path to a font file if 'arial.ttf' isn't found
// For simplicity, we'll use a built-in font
$font = 5; // Built-in font, size 5 (largest)
$x = 10;
$y = 10;

// You can try to use a TrueType font if you have one available, e.g.:
// $font_path = __DIR__ . '/../../assets/fonts/arial.ttf'; // Adjust path if necessary
// imagettftext($image, 20, rand(-10, 10), $x, 30, $text_color, $font_path, $captcha_string);

// Using built-in font as a fallback/simple option
imagestring($image, $font, $x, $y, $captcha_string, $text_color);


// Output the image as a PNG
imagepng($image);

// Free up memory
imagedestroy($image);
?>