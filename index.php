<?php

require('core/core.php');

define('COOKIE_SALT', 'some salt to protect cookies and do other stuff');

include 'classes/Captcha.php';
include 'classes/CommentsCaptcha.php';

$cap = new GenericCaptcha();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$is_valid_cap = $cap->validateCaptcha();
}

echo <<<VIEW
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head><title>Captcha Test</title>
	<link rel="stylesheet" href="styles/style.css" type="text/css">
	</head>
<body>
<form action="index.php" method="post">
VIEW;

echo $cap->getHtmlForm();

$validation_result = include 'templates/validation.php';

echo <<<VIEW
<br /><input type="submit" value="Проверить" />
</form>
Результат проверки кода: $validation_result
</body>
</html>
VIEW;

?>