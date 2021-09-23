<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyam0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * CAPTCHA (Completely Automated Public Test to tell Computers and Humans Apart)
 * 
 * @author 顏宏育(Hong Yu Yan) <royyam0812@gmail.com>
 * @package helper
 */
class CaptchaHelper extends LZ_Helper {

	private static $_instance;

	public $width = 150;
	public $height = 80;
	public $fontSize = 16;
	public $fontPath;
	public $fontColors;
	public $fonts;
	public $textSpacing = 5;
	public $backgroundImages;
	public $textTemplate = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	
	/**
	 * 圖片輸出類型 jpg | png | gif
	 * @var string 
	 */
	public $outputType = 'png';

	/**
	 * 取得單一實例
	 * @return CaptchaHelper
	 */
	public static function getInstance(){
		if(null === self::$_instance) self::$_instance = new CaptchaHelper();
		return self::$_instance;
	}
	
	public function generate(&$sessionRef,$length,$useBackground = true){
		
		$code = TextHelper::getInstance()->random($length,$this->textTemplate);
		$chars = str_split($code);
		$fonts = $this->fonts;
		shuffle($fonts);
		$fontCount = sizeof($fonts);
		
		if(null !== $this->backgroundImages){
			srand((double)microtime()*1000000);
			$background = $this->backgroundImages[rand(0,count($this->backgroundImages)-1)];
			$image = imagecreatefrompng($background);
			$this->width = ImageSX($image);
			$this->height = ImageSY($image);
		}else{
			$image = imagecreatetruecolor($this->width, $this->height);
			imagefilledrectangle($image, 0, 0, $this->width, $this->height, imagecolorallocate($image, 255, 255, 255));
		}

		if(null == $this->fontColors) $this->fontColors  = $this->_defaultFontColors();

		foreach ($this->fontColors as $color){
			$textColors[] = ImageColorAllocate($image,$color[0],$color[1],$color[2]);
		}

		$textWidth = $textHeight = $fi = 0;

		foreach ($chars as $char){
			$bbox = imagettfbbox($this->fontSize,0,$this->fontPath.$fonts[$fi],$char);
			$textWidth += $bbox[4] - $bbox[6];
			$h = $bbox[1] - $bbox[7];
			if($h > $textHeight) $textHeight = $h;
			if(++$fi >($fontCount-1)) $fi = 0;
		}
		
		$textWidth += ($length-1) * $this->textSpacing;

		$x = ($this->width - $textWidth) / 2.0;
		$y = ($this->height - $textHeight) /2.0 + $textHeight;
		
		$i = $fi = 0;
		foreach($chars as $char) {
			ImageTTFText($image,$this->fontSize,0,$x,$y,$textColors[$i++],$this->fontPath.$fonts[$fi],$char);
			$bbox = imagettfbbox($this->fontSize,0,$this->fontPath.$fonts[$fi],$char);
			$x += $bbox[4] - $bbox[6] + $this->textSpacing;
			if(++$fi >($fontCount-1)) $fi = 0;
		}

		app::conf()->CONTENT_TYPE = 'image/png';
		
		ImagePng($image);
		ImageDestroy($image);
		
		return $code;
	}

	private function _defaultFontColors(){
		return array(
			array(206,59,145),
			array(57,76,204),
			array(153,0,255),
			array(102,102,102),
			array(0,51,204),
			array(102,0,51),
			array(51,102,0),
			array(102,51,102),
			array(153,0,0),
			array(104,71,71)
		);
	}
}