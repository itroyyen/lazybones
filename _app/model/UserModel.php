<?php
class UserModel extends LZ_Model {

	public 	$id;

	/**
	 * 姓名
	 */
	public 	$name;

	/**
	 * 帳號
	 */
	public 	$account;

	/**
	 * 密碼
	 */
	public 	$password;


	protected $_pk = 'id';
	protected $_fields = array('id','name','account','password');

}