<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * 應用程式事件訊息處理器
 *
 * @author 顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @package core
 */
class LZ_Event {

	private $_listeners;

	/**
	 * 取得已加入的事件監聽器
	 * @return array
	 */
	public function getListeners(){
		return $this->_listeners;
	}

	/**
	 * 觸發事件
	 * @param string $event 事件名稱
	 * @param array $args 參數
	 * @return array(mixed) 事件處理者回值
	 */
	public function trigger($event,$args = null){
		$argsLen = count($args);
		if(!isset($this->_listeners[$event]))return null;
		if(null === $args) $args = array();
		$listeners = $this->_listeners[$event];
		for($i=0;$i<2;$i++){
			if(!isset($listeners[$i])) continue;
			$handlers = $listeners[$i];
			foreach($handlers as $handler){
				$result = call_user_func_array($handler,$args);
				if(null !== $result) return $result;
			}
		}
	}

	/**
	 * 加入事件監聽器<br>
	 * 事件處理者可傳入以下任一種:<br>
	 * [1] '函數名稱'<br>
	 * [2] array('類別名稱','方法名稱')<br>
	 * [3] array($object,'方法名稱')<br>
	 * @param string $event 事件名稱
	 * @param mixed $handler 事件處理者
	 * @param int $priority 優先權 0 - 2 數值小者先執行
	 */
	public function addListener($event,$handler,$priority = 1){
		$this->_listeners[$event][$priority][] = $handler;
	}

	/**
	 * 移除指定事件
	 * @param string $event
	 */
	public function removeEvents($event){
		if(isset($this->_listeners[$event])){
			unset($this->_listeners[$event]);
		}
	}
}