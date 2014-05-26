<?php

class Captcha {

	protected $template = 'generic_captcha';

	protected $captcha_string = '';
	protected $captcha_hash = '';

	protected $salt = COOKIE_SALT;

	function __construct() {
		$this->generate();
	}

	public function generate() {
		$this->captcha_string = $this->random_string();
		$this->captcha_hash = md5($this->captcha_string . $this->salt);
		$_SESSION['imagestring_' . $this->captcha_hash] = $this->captcha_string;
	}

	private function random_string($length = 5) {
		$set = array("2","3","4","5","6","8","9");
		$str;
		for($i = 1; $i <= $length; $i++) {
			$ch = rand(0, count($set)-1);
			$str .= $set[$ch];
		}

		return $str;
	}

	public function getHtmlForm() {

		return include "templates/$this->template.php";
	}

	public function validateCaptchaHelper() {

		return $_POST['imagestring'] === $_SESSION['imagestring_' . $_POST['imagehash']];
	}

	public function validateCaptcha() {
		$result = $this->validateCaptchaHelper();
		unset($_SESSION['imagestring_' . $_POST['imagehash']]);

		return $result;

		/* ToDo:
			[DONE] Invalidate hash after use
			Usa database
			etc...
		*/

	}

}

class GenericCaptcha extends Captcha {

	/* Placeholder to split CommentsCaptcha and GenericCaptcha */

}

?>