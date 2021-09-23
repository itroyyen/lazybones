<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * 圖片處理助手
 *
 * @author 顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @package helper
 */
class ImageHelper  extends LZ_Helper {

	private static $_instance;

	/**
	 * 取得單一實例
	 * @return ImageHelper
	 */
	public static function getInstance(){
		if(null === self::$_instance) self::$_instance = new ImageHelper();
		return self::$_instance;
	}

	/**
	 * 調整圖片大小
	 * @param string $from 來源檔案路徑
	 * @param string $to 目標檔案路徑
	 * @param int $width 最大寬度
	 * @param int $height 最大高度
	 * @param string $imgType 圖片類型 jpg/png/gif
	 * @return bool
	 */
	public function reszie($from,$to,$width,$height,$imgType = null){
		list($w, $h,$type) = GetImageSize($from);
		
		if(null !== $imgType) $type = $this->_extToType($imgType);

		$img = $this->_getImg($from,$type);

		$distWidth = $w + 0.0;
		$distHeight = $h + 0.0 ;
		$scale = $scaleW = $scaleH = 0.0;

		if($distWidth > $width){
			$scale = $width / ($w + 0.0);
			$distWidth = round($w *  $scale);
			$distHeight = round($h *  $scale);
		}

		if ($distHeight > $height){
			$scale = $height / ($h + 0.0);
			$distWidth = round($w *  $scale);
			$distHeight = round($h *  $scale);
		}

		$distImg = imagecreatetruecolor($distWidth, $distHeight);

		if(!imagecopyresampled($distImg, $img, 0, 0, 0, 0, $distWidth, $distHeight, $w, $h)) return false;
		
		return $this->_outputImg($distImg,$to,$type);
	}

	/**
	 * 剪裁圖片 - 依據高度與寬度
	 * @param string $from 來源檔案路徑
	 * @param string $to 目標檔案路徑
	 * @param int $x 開始剪裁 X 座標
	 * @param int $y 開始剪裁 Y 座標
	 * @param int $width 剪裁寬度
	 * @param int $height 剪裁高度
	 * @param string $imgType 圖片類型 jpg/png/gif
	 * @return bool
	 */
	public function crop($from,$to,$x,$y,$width,$height,$imgType = null){
		list($w, $h,$type) = GetImageSize($from);

		if(null !== $imgType) $type = $this->_extToType($imgType);

		$img = $this->_getImg($from,$type);

		$distImg = imagecreatetruecolor($width, $height);

		if(!imagecopy($distImg, $img, 0, 0, $x, $y, $width, $height)) return false;

		return $this->_outputImg($distImg,$to,$type);
	}

	/**
	 * 剪裁圖片 - 依據結束座標
	 * @param string $from 來源檔案路徑
	 * @param string $to 目標檔案路徑
	 * @param int $x  開始剪裁 X 座標
	 * @param int $y  開始剪裁 Y 座標
	 * @param int $x2 結束剪裁 X 座標
	 * @param int $y2 結束剪裁 Y 座標
	 * @param string $imgType 圖片類型 jpg/png/gif
	 * @return bool
	 */
	public function crop2($from,$to,$x1,$y1,$x2,$y2,$imgType = null){
		return $this->crop($from,$to,$x1,$y1,($x2 - $x1),( $y2 - $y1),$imgType);
	}

	private function _getImg($path,$type){
		switch ($type) {
			case 1:
				return ImageCreateFromGIF($path);
			case 2:
				return ImageCreateFromJPEG($path);
			case 3:
				return ImageCreateFromPNG($path);
		}
	}

	private function _outputImg($img,$to,$type){
		switch ($type) {
			case 1:
				return ImageGIF($img, $to);
			case 2:
				return ImageJPEG($img, $to, 85);
			case 3:
				return ImagePNG($img, $to);
		}
	}

	private function _extToType($ext){
		switch ($ext) {
			case 'gif':
				return 1;
			case 'jpg':
				return 2;
			case 'png':
				return 3;
		}
	}
}