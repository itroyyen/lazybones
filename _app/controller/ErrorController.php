<?php
/**
 * Description of ErrorController
 *
 * @author Roy
 */
class ErrorController extends LZ_Controller{
    //put your code here

	
	public function Action_Index(){
		$this->displayInfo();
	}

	public function Action_Error404(){
		echo "<h1>ERROR 404 Not Found!</h1>";
		$this->displayInfo();
	}

	public function Action_VipDeny(){
		$info = $this->displayInfo();
		echo "<h2>This is VIP deny page</h2>";
	}
}