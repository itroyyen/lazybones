<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NewsController
 *
 * @author Roy
 */
class NewsController extends LZ_Controller{
    //put your code here

	public function Action_Index(){
		$this->displayInfo();
		echo app::_getTestUriList();
	}

	public function Action_ViewNews(){
		$this->displayInfo();
		echo app::_getTestUriList();
	}
}