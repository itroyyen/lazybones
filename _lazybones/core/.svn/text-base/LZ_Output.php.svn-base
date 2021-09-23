<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * 控制輸出
 *
 * @author 顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @package core
 */
class LZ_Output{

	private $_buffers = array();
	private $_headers = array();
	private $_httpStatus;
	private $_contentTypes;

	/**
	 * 新增 HTTP header
	 * @param <type> $header
	 */
	public function addHeader($header){
		$idx = count($this->_headers);
		$this->_headers[$idx] = $header;
	}

	/**
	 * 傾倒所有的 HTTP header
	 */
	public function dumpHeaders(){
		$cnt = count($this->_headers);
		for($i=0;$i<$cnt;++$i){
			$this->sendHeader($this->_headers[$i],true);
		}
		unset($this->_headers);
		$this->_headers = array();
	}

	/**
	 * 傾倒所有的緩衝內容
	 */
	public function dumpBuffers(){
		$cnt = count($this->_buffers);
		for($i = 0;$i<$cnt;++$i){
			echo $this->_buffers[$i];
		}
		unset($this->_buffers);
		$this->_buffers = array();
		if(ob_get_length() > 0 ){
			$content =  ob_get_contents();
			ob_end_clean();
			echo $content;
		}
	}

	/**
	 * 傾倒所有的緩衝及HTTP header
	 */
	public function dump(){
		$this->dumpHeaders();
		$this->dumpBuffers();
	}

	/**
	 * 清除緩衝
	 */
	public function clearBuffers(){
		unset($this->_buffers);
		$this->_buffers = array();
	}

	/**
	 * 開始緩衝功能
	 */
	public function startBuffer(){
		ob_start();
	}

	/**
	 * 結束緩衝功能
	 */
	public function endBuffer(){
		$this->pauseBuffer();
		$this->dump();
	}

	/**
	 * 暫停緩衝
	 */
	public function pauseBuffer(){
		if(ob_get_length() > 0 ){
			$idx = count($this->_buffers);
			$this->_buffers[$idx] = ob_get_contents();
			ob_end_clean();
		}
	}

	/**
	 * 直接發送 HTTP Header 若為數字，則發送HTTP狀態碼
	 * @param mixed $headerCode Header
	 * @param bool $replace 是否取代
	 */
	public function sendHeader($headerCode,$replace = true){
		if(is_numeric($headerCode)){
			self::statusHeader($headerCode);
		}else{
			header($headerCode,$replace);
		}
	}

	/**
	 * 發送HTTP狀態碼
	 * 參照：http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html (Status Code Definitions)
	 * @param mixed $headerCode Header
	 * @param bool $replace 是否取代
	 */
	public  function statusHeader($headerCode,$replace = true){
		if(is_numeric($headerCode)){
			if(null === $this->_httpStatus){
				$this->_httpStatus = include(app::conf()->FRAMEWORK_PATH . 'data/http_status' . PHP_EXT);
			}
			$headerCode = strval($headerCode);
			$statusText = isset($this->_httpStatus[$headerCode]) ? $this->_httpStatus[$headerCode] : null;
			if(null !== $statusText) header("HTTP/1.1 {$headerCode} {$statusText}", $replace, $headerCode);
		}
	}

	/**
	 * 依據檔案名稱取得ContentType
	 * @param string $fileName 檔案名稱
	 * @return string
	 */
	public function getContentType($fileName){
		if(null === $this->_contentTypes){
			$this->_contentTypes = $this->_contentTypes = include(app::conf()->FRAMEWORK_PATH . 'data/content_type' . PHP_EXT);
		}
		
		$ext = strtolower(substr(strrchr($fileName, '.'), 1));
		
		if(isset($this->_contentTypes[$ext])){
			return $this->_contentTypes[$ext];
		}else{
			return 'application/octet-stream';
		}
	}
}