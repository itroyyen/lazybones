<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HelloController
 *
 * @author Roy
 */
class HelloController extends LZ_Controller{
    //put your code here

	/**
	 * Say Hello 頁面
	 */
	public function Action_Index(){
		$this->displayInfo();
		echo app::_getTestUriList();
		
	}
	
	/**
	 * Say Hello 頁面
	 */
	public function Action_SayHello(){
		$this->displayInfo();
		echo app::_getTestUriList();
	}
}