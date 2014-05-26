<?php

class CaptchaImage {

	public $img_width = 201;
	public $img_height = 61;

	// The following settings are only used for TTF fonts
	public $min_size = 20;
	public $max_size = 32;

	public $min_angle = -30;
	public $max_angle = 30;

	public $ttf_fonts = array();
	public $im = '';

	protected $captcha_backs = 'resources/captcha_backs';
	protected $captcha_fonts = 'resources/captcha_fonts';

	private $imagestring = '';

	protected $backgrounds = array();

	function __construct($imagestring) {
		// We have support for true-type fonts (FreeType 2)
		if (function_exists("imagefttext")) {
			// Get a list of the files in the 'catpcha_fonts' directory
			$ttfdir = opendir($this->captcha_fonts);
			if ($ttfdir) {
				while ($file = readdir($ttfdir)) {
					// If this file is a ttf file, add it to the list
					if (is_file($this->captcha_fonts . "/$file") && $this->get_extension($file) == "ttf") {
						$this->ttf_fonts[] = $this->captcha_fonts . "/$file";
					}
				}
			}
		}

		// Have one or more TTF fonts in our array, we can use TTF captha's
		$this->use_ttf = (count($this->ttf_fonts) > 0);

		// Get backgrounds
		if ($handle = opendir($this->captcha_backs)) {
			while ($filename = readdir($handle)) {
				if (preg_match('#\.(gif|jpg|jpeg|jpe|png)$#i', $filename)) {
					$this->backgrounds[] = $this->captcha_backs . "/$filename";
				}
			}
			closedir($handle);
		}

		$this->imagestring = $imagestring;
	}

	public function create_image() {

		$notdone = true;

		while ($notdone && !empty($this->backgrounds)) {
			$index = mt_rand(0, count($this->backgrounds) - 1);
			$background = $this->backgrounds["$index"];
			switch (strtolower($this->file_extension($background))) {
				case 'jpg':
				case 'jpe':
				case 'jpeg':
					if (!function_exists('imagecreatefromjpeg') || !$this->im = imagecreatefromjpeg($background)) {
						unset($this->backgrounds["$index"]);
						echo 'oops';
					} else {
						$notdone = false;
					}
					break;
				case 'gif':
					if (!function_exists('imagecreatefromgif') || !$this->im = imagecreatefromgif($background)) {
						unset($this->backgrounds["$index"]);
					} else {
						$notdone = false;
					}
					break;
				case 'png':
					if (!function_exists('imagecreatefrompng') || !$this->im = imagecreatefrompng($background)) {
						unset($this->backgrounds["$index"]);
					} else {
						$notdone = false;
					}
					break;
			}
			sort($this->backgrounds);
		}

		if (!$this->im) {
			throw new Exception("No GD support.");
		}

		if (time() & 2 && function_exists('imagerotate'))
			$this->im = imagerotate($this->im, 180, 0);

		// Fill the background with white
		$bg_color = imagecolorallocate($this->im, 255, 255, 255);
		imagefill($this->im, 0, 0, $bg_color);

	}

	public function draw_image() {

		if (!$this->im)
			$this->create_image();

		// Draw random circles, squares or lines?
		$to_draw = rand(0, 2);
		if ($to_draw == 1) {
			$this->draw_circles($this->im);
		} elseif ($to_draw == 2) {
			$this->draw_squares($this->im);
		} else {
			$this->draw_lines($this->im);
		}

		// Draw dots on the image
		$this->draw_dots($this->im);

		// Write the image string to the image
		$this->draw_string($this->im, $this->imagestring);

		// Draw a nice border around the image
		$border_color = imagecolorallocate($this->im, 0, 0, 0);
		imagerectangle($this->im, 0, 0, $this->img_width - 1, $this->img_height - 1, $border_color);

		return $this->im;

	}

	public function output_image() {
		// Output the image
		header("Content-type: image/png");
		header('Cache-control: max-age=31536000');
		header('Expires: ' . gmdate('D, d M Y H:i:s', (time() + 31536000)) . ' GMT');
		header('Content-disposition: inline; filename=captcha.png');
		header('Content-transfer-encoding: binary');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
		imagepng($this->draw_image());
	}

	/**
	 * Draws a random number of lines on the image.
	 *
	 * @param resource The image.
	 */
	public function draw_lines(&$im) {
		for ($i = 10; $i < $this->img_width; $i += 10) {
			$color = imagecolorallocate($im, rand(150, 255), rand(150, 255), rand(150, 255));
			imageline($im, $i, 0, $i, $this->img_height, $color);
		}
		for ($i = 10; $i < $img_height; $i += 10) {
			$color = imagecolorallocate($im, rand(150, 255), rand(150, 255), rand(150, 255));
			imageline($im, 0, $i, $this->img_width, $i, $color);
		}
	}

	/**
	 * Draws a random number of circles on the image.
	 *
	 * @param resource The image.
	 */
	public function draw_circles(&$im) {
		$circles = $this->img_width*$this->img_height / 100;
		for($i = 0; $i <= $circles; $i++) {
			$color = imagecolorallocate($im, rand(180, 255), rand(180, 255), rand(180, 255));
			$pos_x = rand(1, $this->img_width);
			$pos_y = rand(1, $this->img_height);
			$circ_width = ceil(rand(1, $this->img_width)/2);
			$circ_height = rand(1, $this->img_height);
			imagearc($this->im, $pos_x, $pos_y, $circ_width, $circ_height, 0, rand(200, 360), $color);
		}
	}

	/**
	 * Draws a random number of dots on the image.
	 *
	 * @param resource The image.
	 */
	public function draw_dots(&$im) {
		$dot_count = $this->img_width*$this->img_height/5;
		for ($i = 0; $i <= $dot_count; $i++) {
			$color = imagecolorallocate($this->im, rand(200, 255), rand(200, 255), rand(200, 255));
			imagesetpixel($this->im, rand(0, $this->img_width), rand(0, $this->img_height), $color);
		}
	}

	/**
	 * Draws a random number of squares on the image.
	 *
	 * @param resource The image.
	 */
	public function draw_squares(&$im) {
		$square_count = 30;
		for ($i = 0; $i <= $square_count; $i++) {
			$color = imagecolorallocate($this->im, rand(150, 255), rand(150, 255), rand(150, 255));
			$pos_x = rand(1, $this->img_width);
			$pos_y = rand(1, $this->img_height);
			$sq_width = $sq_height = rand(10, 20);
			$pos_x2 = $pos_x + $sq_height;
			$pos_y2 = $pos_y + $sq_width;
			imagefilledrectangle($this->im, $pos_x, $pos_y, $pos_x2, $pos_y2, $color); 
		}
	}

	/**
	 * Writes text to the image.
	 *
	 * @param resource The image.
	 * @param string The string to be written
	 */
	public function draw_string(&$im, $string) {
		$string_length = $this->my_strlen($string);
		$spacing = $this->img_width / $string_length;
		
		for ($i = 0; $i < $string_length; $i++) {
			// Using TTF fonts
			if($this->use_ttf) {
				// Select a random font size
				$font_size = rand($this->min_size, $this->max_size);
				
				// Select a random font
				$font = array_rand($this->ttf_fonts);
				$font = $this->ttf_fonts[$font];

				// Select a random rotation
				$rotation = rand($this->min_angle, $this->max_angle);

				// Set the colour
				$r = rand(0, 200);
				$g = rand(0, 200);
				$b = rand(0, 200);
				$color = imagecolorallocate($this->im, $r, $g, $b);
				
				// Fetch the dimensions of the character being added
				$dimensions = imageftbbox($font_size, $rotation, $font, $string[$i], array());
				$string_width = $dimensions[2] - $dimensions[0];
				$string_height = $dimensions[3] - $dimensions[5];

				// Calculate character offsets
				//$pos_x = $pos_x + $string_width + ($string_width/4);
				$pos_x = $spacing / 4 + $i * $spacing;
				$pos_y = ceil(($this->img_height-$string_height/2));
				
				if($pos_x + $string_width > $this->img_width) {
					$pos_x = $pos_x - ($pos_x - $string_width);
				}

				// Draw a shadow
				$shadow_x = rand(-3, 3) + $pos_x;
				$shadow_y = rand(-3, 3) + $pos_y;
				$shadow_color = imagecolorallocate($im, $r+20, $g+20, $b+20);
				imagefttext($this->im, $font_size, $rotation, $shadow_x, $shadow_y, $shadow_color, $font, $string[$i], array());

				// Write the character to the image
				imagefttext($this->im, $font_size, $rotation, $pos_x, $pos_y, $color, $font, $string[$i], array());
			} else {
				// Get width/height of the character
				$string_width = imagefontwidth(5);
				$string_height = imagefontheight(5);

				// Calculate character offsets
				$pos_x = $spacing / 4 + $i * $spacing;
				$pos_y = $this->img_height / 2 - $string_height -10 + rand(-3, 3);

				// Create a temporary image for this character
				if(gd_version() >= 2) {
					$temp_im = imagecreatetruecolor(15, 20);
				} else {
					$temp_im = imagecreate(15, 20);
				}
				$bg_color = imagecolorallocate($temp_im, 255, 255, 255);
				imagefill($temp_im, 0, 0, $bg_color);
				imagecolortransparent($temp_im, $bg_color);

				// Set the colour
				$r = rand(0, 200);
				$g = rand(0, 200);
				$b = rand(0, 200);
				$color = imagecolorallocate($temp_im, $r, $g, $b);
				
				// Draw a shadow
				$shadow_x = rand(-1, 1);
				$shadow_y = rand(-1, 1);
				$shadow_color = imagecolorallocate($temp_im, $r+50, $g+50, $b+50);
				imagestring($temp_im, 5, 1+$shadow_x, 1+$shadow_y, $string[$i], $shadow_color);
				
				imagestring($temp_im, 5, 1, 1, $string[$i], $color);
				
				// Copy to main image
				imagecopyresized($this->im, $temp_im, $pos_x, $pos_y, 0, 0, 40, 55, 15, 20);
				imagedestroy($temp_im);
			}
		}
	}

	/**
	 * Obtain the version of GD installed.
	 *
	 * @return float Version of GD
	 */
	public function gd_version() {
		static $gd_version;

		if($gd_version) {
			return $gd_version;
		}

		if(!extension_loaded('gd')) {
			return;
		}

		ob_start();
		phpinfo(8);
		$info = ob_get_contents();
		ob_end_clean();
		$info = stristr($info, 'gd version');
		preg_match('/\d/', $info, $gd);
		$gd_version = $gd[0];

		return $gd_version;
	}

	public function get_extension($file) {
		return strtolower($this->my_substr(strrchr($file, "."), 1));
	}

	public function my_substr($string, $start, $length="") {
		if (function_exists("mb_substr")) {
			if ($length != "") {
				$cut_string = mb_substr($string, $start, $length);
			} else {
				$cut_string = mb_substr($string, $start);
			}
		} else {
			if ($length != "") {
				$cut_string = substr($string, $start, $length);
			} else {
				$cut_string = substr($string, $start);
			}
		}

		return $cut_string;
	}

	public function my_strlen($string) {
		$string = preg_replace("#&\#(0-9]+);#", "-", $string);
		if(function_exists("mb_strlen")) {
			$string_length = mb_strlen($string);
		} else {
			$string_length = strlen($string);
		}

		return $string_length;
	}

	public function file_extension($filename) {
		return substr(strrchr($filename, '.'), 1);
	}

}

?>