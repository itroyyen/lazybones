<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SimpleLaout
 *
 * @author 顏宏育(Hong Yu Yan) <royyam0812@gmail.com>
 */
class SimpleLayout{

	public function MyInfo(){
		return array(
			'msg' => 'Hello World',
			'title' => 'My first layout component'
		);
	}
	
    public function MyInfo2($msg,$title){
		return array(
			'msg' => $msg,
			'title' => $title
		);
	}
}