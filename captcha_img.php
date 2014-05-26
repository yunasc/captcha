<?php

require('core/core.php');

include 'classes/CaptchaImage.php';

$imagestring = $_SESSION['imagestring_' . $_GET['imagehash']];

if (empty($imagestring))
	$imagestring = 'WTF?';

$image = new CaptchaImage($imagestring);

$image->output_image();

?>