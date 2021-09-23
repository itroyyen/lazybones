<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

define('REL_HAS_ONE'   , 'has_one');
define('REL_HAS_MANY'  , 'has_many');
define('REL_MANY_MANY' , 'many_many');
define('REL_BELONGS_TO', 'belongs_to');

/**
 * 協助Model處理相關事務
 *
 * @author 顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @package core
 */
class LZ_ModelAssistant{

	private static $_instance;

	private function __construct() {}

	const JOIN_LEFT = 'LEFT';
    const JOIN_RIGHT = 'RIGHT';
	const JOIN_INNER = 'INNER';
    const JOIN_LEFT_OUTER = 'LEFT OUTER';
    const JOIN_RIGHT_OUTER = 'RIGHT OUTER';

	/**
	 * 取得實體
	 * @return LZ_ModelAssistant
	 */
	public static function getInstance(){
		if(null === self::$_instance){
			self::$_instance = new LZ_ModelAssistant();
		}
		return self::$_instance;
	}
	
	/**
	 * 名稱轉換為表名稱 e.g. UserInfoModel >> user_info 或 UserInfo >> user_info
	 * @param string $name
	 * @return string
	 */
	public function convertTableName($name){
		$part = splitUpper(stripSuffix($name,'Model'));
		return  strtolower(join('_',$part));
	}

	/**
	 * 綁定SQL參數
	 * @param string $str 原始字串
	 * @param string $params 參數
	 * @param string $table 表名
	 * @return string
	 */
	public function bindParam($str,$table,$params = null){
		$matches = null;
		$db = app::db();
		
		if($params != null){ //參數
			if(is_array($params)){ // {:varname}
				$newParams = array();
				$cnt = count($params);
				foreach($params as $k => $v){
					if(!is_numeric($v)) $v = "'".$db->escape($v)."'";
					$newParams['{:'.$k.'}'] = $v;
				}
				$str = strtr($str,$newParams);
			}else{// {?}
				if(!is_numeric($params)) $params = "'".$db->escape($params)."'";
				$str = strtr($str,array('{?}' => $params));
			}
		}
		
		// {Post.id} = ? AND {name} = '' 轉為 `post`.`id` = ? AND `name` = ''
		if(preg_match_all('/\{([\w\.]+)\}/',$str,$matches)){
			foreach($matches[1] as $match){
				$part = explode('.', $match);
				if(isset ($part[1])){
					$part[0] = '`'.$this->convertTableName($part[0]).'`';
					$part[1] = ".`{$part[1]}`";
				}else{
					$part[1] = '';
					if('' !== $table){
						$part[0] = '`'.$table.'`.`'.$part[0].'`';
					}else{
						$part[0] = '`'.$part[0].'`';
					}
				}

				$str = strtr($str,array('{'.$match.'}' => $part[0].$part[1]));
			}
		}

		return $str;
	}

	/**
	 * 取得where條件
	 * @param $table 資料表名稱
	 * @param $fields 欄位
	 * @param $sentences 附加語句
	 * @return string
	 */
	public function getWhere($table,$fields,$sentences){
		$whereQueue = array();
		$whereOrQueue = array();
		$db = app::db();
		
		foreach($fields as $fieldName => $v){
			$orLink = false;
			if(is_array($v)){
				$op = strtoupper($v[0]);
				$value = $v[1];
				if(strpos($op, ' ') !== false){
					$part = explode(' ', $op);
					$op = $part[1];
					$orLink = true;
				}
			}else{
				$op = '=';
				$value = $v;
			}

			if(!is_numeric($value)) $value = "'".$db->escape($value)."'";

			if($orLink == true){
				$whereOrQueue[] = "`$table`.`$fieldName` $op $value";
			}else{
				$whereQueue[] = "`$table`.`$fieldName` $op $value";
			}
		}

		$where = join(' AND ',$whereQueue);

		if(isset($sentences['where'])){
			if(count($sentences['where']) != 0 ){
				foreach($sentences['where'] as  $w){
					if($where != '') $where .= ' '.$w[2].' ';
					$where .= $this->bindParam($w[0],$table,$w[1]);
				}
			}
		}

		if(count($whereOrQueue) != 0){
			if($where != '') $where .= ' OR ';
			$where .= join(' OR ',$whereOrQueue);
		}
		
		return $where;
	}

	/**
	 * 結合附加語句
	 * @param string $table 資料表名稱
	 * @param array $data 要結合的附加語句
	 * @param string $gule 結合符號
	 * @return string
	 */
	public function combineSentences($table,$sentences,$gule){
		$rtval = '';
		foreach($sentences as  $s){
			if($rtval != '') $rtval .= $gule;
			$rtval .= $this->bindParam($s[0],$table,$s[1]);
		}
		return $rtval;
	}

	/**
	 * 取得搜尋用的SQL語句
	 * @param LZ_Model $model
	 * @param bool $withRelation 若有關聯時，是否包含關聯操作
	 * @return string
	 */
	public function getFindSQL($model,$withRelation = true){
		$isTableJoin = $model->relationMode() === LZ_Model::RELATE_TABLE &&
				$model->hasRelation()&& $withRelation ? true : false;
		
		$table = $model->table();
		$sentences = $model->getSentences();
		
		$sqlQueue = array();
		$select[] = '`'.$table.'`.*';
		$from = '`'.$table.'`';
		$whereTemp = $this->getWhere($table,$model->fields(null,true),$sentences);
		if('' !== $whereTemp) $where[] = $whereTemp;

		if(isset($sentences['select'][0])){
			$select[] = $this->combineSentences($table,$sentences['select'], ' , ');
		}
		
		if(isset($sentences['groupBy'][0])){
			$groupBy[] = $this->combineSentences($table,$sentences['groupBy'], ' , ');
		}

		if(isset($sentences['having'][0])){
			$having[] = $this->combineSentences('',$sentences['having'], ' AND ');
		}

		if(isset($sentences['orderBy'][0])){
			$orderBy[] = $this->combineSentences($table,$sentences['orderBy'], ' , ');
		}

		if(isset($sentences['limit'][0])){
			$limit = $this->combineSentences($table,$sentences['limit'], ' , ');
		}
		
		if($isTableJoin && $withRelation){
			$joinQueue = array();
			foreach($model->getRelations() as $rel) {
				//Join and select fields
				$joinTemp = $rel['joinType'].' JOIN `'.$rel['table'].'` ON ';
				$relSentences = $rel['obj']->getSentences();
				if($rel['conf'][0] == $rel['model']){
					$pk = $model->primaryKey();
					$ltable = $table;
					$rtable = $rel['table'];
				}else{
					$pk = $rel['obj']->primaryKey();
					$ltable = $rel['table'];
					$rtable = $table;
				}

				$joinTemp .= "`$ltable`.`$pk` = `$rtable`.`{$rel['conf'][1]}`";
				$joinQueue[] = $joinTemp;

				$filedNames = $rel['obj']->fieldNames();
				$prefix = $rel['obj']->prefix();
				if('' !== $prefix) $prefix .= '_';
				
				foreach($filedNames as $name){
					$select[] = "`{$rel['table']}`.`$name` AS `{$prefix}{$name}`";
				}
				
				//relation where
				$whereTemp = $this->getWhere($rel['table'],$rel['obj']->fields(null,true),$relSentences);
				if('' !== $whereTemp) $where[] = $whereTemp;
				
				//relation sentences
				if(isset($relSentences['select'][0])){
					$select[] = $this->combineSentences($rel['table'],$relSentences['select'], ' , ');
				}

				if(isset($relSentences['groupBy'][0])){
					$groupBy[] = $this->combineSentences($rel['table'],$relSentences['groupBy'], ' , ');
				}

				if(isset($relSentences['having'][0])){
					$having[] = $this->combineSentences($rel['table'],$relSentences['having'], ' AND ');
				}

				if(isset($relSentences['orderBy'][0])){
					$orderBy[] = $this->combineSentences($rel['table'],$relSentences['orderBy'], ' , ');
				}
			}
			
			$join = join(' ',$joinQueue);
		}

		$sqlQueue[] = 'SELECT '.join(' , ',$select);
		$sqlQueue[] = 'FROM '.$from;
		if($isTableJoin) $sqlQueue[] = $join;
		if(isset($where)){
			$where = join(' AND ',$where);
		}else{
			$where = '';
		}
		if('' === $where) $where = '1';
		$sqlQueue[] = 'WHERE '.$where;
		if(isset($groupBy)) $sqlQueue[] = 'GROUP BY ' . join(' , ',$groupBy);
		if(isset($having)) $sqlQueue[] = 'HAVING ' . join(' AND ',$having);
		if(isset($orderBy)) $sqlQueue[] = 'ORDER BY ' . join(' , ',$orderBy);
		if(isset($limit)) $sqlQueue[] = 'LIMIT ' . $limit;

		return join(' ' , $sqlQueue);
	}

	/**
	 * 取得儲存用的SQL語法
	 * @param LZ_Model $model
	 * @param LZ_Model $condModel 儲存依據的條件
	 * @return string
	 */
	public function getSaveSQL($model,$condModel = null){
		$pk = $model->primaryKey();
		$db = app::db();
		$table = $model->table();

		if(null !== $condModel){ // 依照條件
			$where = $this->getWhere($condModel->table(),$condModel->fields(null,true),$condModel->getSentences());
			if('' === $where) $where = '1';
			$where = 'WHERE '.$where;
		}elseif(null !== $model->primary()){ // 依照主鍵
			$where = 'WHERE `'.$pk.'` = '.$model->$pk;
		}else{
			if($model->hasRelation()) return false;
			app::throwError(app::ft('','LZ_Model : save() 發生錯誤，主鍵或條件未設定'));
		}

		$data = $model->fields(null,true);

		$sets = array();
		$fields = $values = array();
		foreach($data as $k => $v){
			$field = '`'.$k.'`';
			if($k == $pk) continue;;
			if(is_numeric($v)){
				$value = $v;
			}else{
				$value = "'".$db->escape($v)."'";
			}
			$sets[] = $field . ' = ' . $value;
		}
		if(!isset($sets[0])) return false;
		return  'UPDATE `'. $table.'` SET '.join(' , ',$sets).' '.$where;
	}

	/**
	 * 取得新增用的SQL語法
	 * @param LZ_Model $model
	 * @return string
	 */
	public function getAddSQL($model){
		$db = app::db();
		$fieldData = $model->fields(null,true);
		$fields = $values = array();
		foreach($fieldData as $k => $v){
			$fields[] = '`'.$k.'`';
			if(is_numeric($v)){
				$values[] = $v;
			}else{
				$values[] = "'".$db->escape($v)."'";
			}
		}
		return 'INSERT INTO `'.$model->table().'` ('.join(' , ',$fields).') VALUES ('.join(' , ',$values).')';
	}

	/**
	 * 取得刪除用的SQL語法
	 * @param LZ_Model $model
	 * @return string
	 */
	public function getDeleteSQL($model){
//		$db = app::db();
//		$fieldData = $model->fields(null,true);
//		$fields = $values = array();
//		foreach($fieldData as $k => $v){
//			$fields[] = '`'.$k.'`';
//			if(is_numeric($v)){
//				$values[] = $v;
//			}else{
//				$values[] = "'".$db->escape($v)."'";
//			}
//		}
		$where = $this->getWhere($model->table(), $model->fields(null,true), $model->getSentences());
		if('' === $where) $where = '1';
		return 'DELETE FROM `'.$model->table().'` WHERE '.$where;
	}

}