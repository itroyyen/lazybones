<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * Model搜尋結果分頁助手
 * 
 * @author 顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @package helper
 */
class PaginationHelper extends LZ_Helper {

	public $model;

	private static $_instance;

	/**
	 * 取得單一實例
	 * @return PaginationHelper
	 */
	public static function getInstance(){
		if(null === self::$_instance) self::$_instance = new PaginationHelper();
		return self::$_instance;
	}

	/**
	 * 取得分頁資訊
	 * @param int $page
	 * @param int $pageSize
	 * @param int $groupSize
	 * @return array
	 */
	public function getInfo($page,$pageSize,$groupSize){
		$rowCount = $this->model->rowCount();
		$pageCount = intval(ceil($rowCount / $pageSize));
		if(1 > $page) $page = 1;
		if(0 == $pageCount) $page = 0;
		if($page > $pageCount) $page = $pageCount;
		$next = $page+1 <= $pageCount ? $page+1 : null;
		$prev = $page-1 > 0 ? $page-1 : null;
		$groupCount = intval(ceil($pageCount / $groupSize));
		$group = intval(ceil($page / $groupSize));
		$nextGroup = $group+1 <= $groupCount ? $group+1 : null;
		$prevGroup = $group-1 > 0 ? $group-1 : null;
		$groupStart = $page > 0 ? ($group-1) * $groupSize +1 : null;

		return array(
			'page' => $page,
			'pageCount' => $pageCount,
			'pageSize' => $pageSize,
			'next' => $next ,
			'prev' => $prev,
			'group' => $group ,
			'groupStart' => $groupStart,
			'groupSize' => $groupSize,
			'grouCount' => $groupCount,
			'prevGroup' => $prevGroup,
			'nextGroup' => $nextGroup
		);
	}

	/**
	 * 取哦HTML分頁清單
	 * @param int $page
	 * @param int $pageSize
	 * @param int $groupSize
	 * @return string
	 */
	public function getHtmlList($page,$pageSize,$groupSize){
		$pg = $this->getInfo($page, $pageSize, $groupSize);
		$rtval = "";
		$rtval = "<ul>";
		$end = $pg['groupStart']+$pg['groupSize'];
		if($pg['prevGroup']) $rtval .= "<li>&laquo;</li>";
		if($pg['prev']) $rtval .= "<li>&#8249;</li>";
		for($i=$pg['groupStart'];$i<$end;++$i){
			$rtval .= "<li>$i</li>";
		}
		if($pg['next']) $rtval .= "<li>&#8250;</li>";
		if($pg['nextGroup']) $rtval .= "<li>&raquo;</li>";
		$rtval .= "</ul>";
		return $rtval;
	}
}