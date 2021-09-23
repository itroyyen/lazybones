<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * 資料模型
 * 
 * @author 顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @package core
 */
class LZ_Model {
	/**
	 * 表格模式
	 */
	const RELATE_TABLE   = 100;

	/**
	 * 巢狀模式
	 */
	const RELATE_NESTED  = 101;

	/**
	 * get()回傳：標準物件
	 */
	const GET_OBJ   = 200;

	/**
	 * get()回傳：LZ_Model物件
	 */
	const GET_MODEL = 201;

	/**
	 * get()回傳：陣列
	 */
	const GET_ARRAY = 202;

	/**
	 * 已設定的關聯物件資料
	 * @var array
	 */
	private $_relationObjs = array();

	/**
	 * 是否有關聯資料
	 * @var bool
	 */
	private $_hasRelations = false;

	/**
	 * LZ_IDbDriver的資源記錄
	 */
	protected $_res;

	/**
	 * 主鍵欄位名
	 * @var string
	 */
	protected $_pk = 'id';

	/**
	 * 擁有的欄位名
	 * @var array
	 */
	protected $_fields;

	/**
	 * 資料表名稱
	 * @var string
	 */
	protected $_table;

	/**
	 * 關連欄位前綴詞
	 * @var string
	 */
	private $_prefix;

	/**
	 * 關連模式
	 * @var int
	 */
	private $_relationMode = self::RELATE_TABLE;

	/**
	 * 額外欄位記錄
	 * @var array
	 */
	private $_extraFields = array();

	/**
	 * SQL附加語句
	 * @var array
	 */
	private $_sentences = array();

	/**
	 * 記錄總數
	 * @var int
	 */
	private $_rowCount;

	/**
	 * 建構子
	 */
	public function __construct($table = null,$fields = null,$primaryKey = null){
		$cls = get_class($this);
		if(null === $this->_fields && null === $fields){
			foreach(get_class_vars($cls) as $k => $v) {
				if('_pk' !== $k ) $fields[] = $k;
			}
		}
		if(null !== $table)$this->_table = $table;
		if(null !== $fields)$this->_fields = $fields;
		if(null !== $primaryKey)$this->_pk = $primaryKey;
	}

	/**
	 * [特殊方法成員]
	 * 覆蓋此成員，回傳驗證設定，可進行資料驗證
	 */
	protected function __Validation__(){}

	/**
	 * 設定或取得關連模式
	 * @param int $mode
	 * @return int
	 */
	public function relationMode($mode = null){
		if(null === $mode){
			return $this->_relationMode;
		}else{
			if($mode >= 100 && $mode < 200){
				$this->_relationMode = $mode;
			}elseif($mode >= 200 && $mode < 300){
				$this->_fetchAllMode = $mode;
			}
		}
	}

	/**
	 * 執行SQL查詢
	 * @param string $sql SQL語句
	 * @param bool $autoFetch
	 * @param bool $withRelation 若有關聯時，是否包含關聯操作
	 * @return int 結果總數
	 */
	public function query($sql,$autoFetch = false){
		$this->_res = app::db()->query($sql);
		if($autoFetch === true) $this->fetch();
		$this->_rowCount = app::db()->count($this->_res);
		return $this->_rowCount;
	}

	/**
	 * 取得資料
	 * @return bool
	 */
	public function fetch(){

		$row = app::db()->fetch($this->_res);
		$this->reset(true);
		if(!$row) return false;
		$this->fields($row);

		if($this->hasRelation() && self::RELATE_NESTED == $this->_relationMode){
			foreach($this->_relationObjs as $rel){
				$rel['obj']->reset(true);
				$rel['obj']->relationMode($this->_relationMode);
				if($rel['conf'][0] == $rel['model']){
					$rel['obj']->$rel['conf'][1] = $this->primary();
				}else{
					$rel['obj']->primary($this->$rel['conf'][1]);
				}
				$autFetch = (REL_HAS_ONE == $rel['relType'] || REL_BELONGS_TO == $rel['relType']) ? true : false;
				$rel['obj']->find($autFetch);
			}
		}
		
		return true;
	}

	/**
	 * 取得並傳回指定格式資料
	 * @param int $type 回傳類型，可用(LZ_Model::GET_ARRAY , LZ_Model::GET_MODEL , LZ_Model::GET_OBJ)
	 * @return array
	 */
	public function get($type = self::GET_ARRAY){
		
		$row = null;
		switch($type){
			case self::GET_ARRAY:
				$row = app::db()->fetch($this->_res);
				break;
			case self::GET_OBJ:
				$row = app::db()->fetchObject($this->_res);
				break;
			case self::GET_MODEL:
				$row = app::db()->fetchObject($this->_res,get_class($this));
				break;
			default:
				app::throwError(app::ft('','LZ_Model : get() 類型錯誤'));
				break;
		}
		
		if($this->hasRelation() && self::RELATE_NESTED == $this->relationMode() && $row){
			foreach($this->getRelations() as $rel){
				
				$relName = $rel['obj']->prefix();
				
				if($rel['conf'][0] == $rel['model']){
					$pk = $this->primaryKey();
					$rel['obj']->$rel['conf'][1] = self::GET_ARRAY === $type ? $row[$pk] : $row->$pk;
				}else{
					$rel['obj']->primary($row[$rel['conf'][1]]);
					$rel['obj']->primary(self::GET_ARRAY === $type ? $row[$rel['conf'][1]] : $rel['conf'][1]);
				}

				$rel['obj']->find();
				$subAll = null;
				while($subRow = $rel['obj']->get($type)){
					$subAll[] = $subRow;
				}

				switch($type){
					case self::GET_ARRAY:
						$row[$relName] = $subAll;
						break;
					case self::GET_OBJ:
					case self::GET_MODEL:
						$row->$relName = $subAll;
						break;
					default:
						app::throwError(app::ft('','LZ_Model : get() 類型錯誤'));
						break;
				}
			}
			
			$rtval[] = $row;
		}
		return $row;
	}

	/**
	 * 取得並傳傳回全部資料
	 * @param int $type 回傳類型。可用：LZ_Model::GET_ARRAY, LZ_Model::GET_OBJ , LZ_Model::GET_MODEL
	 * @return array
	 */
	public function getAll($type = self::GET_ARRAY){
		$rtval = null;
		while($row = $this->get($type)){
			$rtval[] = $row;
		}
		return $rtval;
	}

	/**
	 * 取得分頁資料
	 * @param resource $res
	 * @param int $page 目前分頁
	 * @param int $pageSize 每頁筆數
	 * @param int $type 回傳類型，可用(LZ_Model::GET_ARRAY , LZ_Model::GET_MODEL , LZ_Model::GET_OBJ)
	 * @return array
	 */
	public function fetchPagination($page,$pageSize,$type = self::GET_ARRAY){
		$rowCount = $this->rowCount();
		$pageCount = intval(ceil($rowCount / $pageSize));
		if(1 > $page) $page = 1;
		if(0 == $pageCount) $page = 0;
		if($page > $pageCount) $page = $pageCount;
		$rowStart = ($page-1)*$pageSize;
		$rtval = null;
		$this->limit("$rowStart,$pageSize");
		$this->find();
		while($row = $this->get($type)){
			$rtval[] = $row;
		}
		return $rtval;
	}

	
	/**
	 * 取得已關聯的物件設定
	 * @return array
	 */
	public function getRelations(){
		return $this->_relationObjs;
	}

	/**
	 * 搜尋
	 * @param bool $autoFetch
	 * @param bool $withRelation 若有關聯時，是否包含關聯操作
	 */
	public function find($autoFetch = false,$withRelation = true){
		return $this->_find($this, $autoFetch, $withRelation);
	}

	/**
	 * 內部搜尋
	 * @param LZ_Model 條件Model
	 * @param bool $autoFetch
	 * @param bool $withRelation 若有關聯時，是否包含關聯操作
	 * @return int 查詢結果總數
	 */
	private function _find($model,$autoFetch = false,$withRelation = true){
		return $this->query( LZ_ModelAssistant::getInstance()->getFindSQL($model,$withRelation) , $autoFetch );
	}

	/**
	 * 以主鍵為條件搜尋
	 * @param int $id
	 * @param bool $autFetch
	 */
	public function findById($id,$autFetch = false){
		$this->reset(true);
		$this->primary($id);
		return $this->find($autFetch);
	}

	/**
	 * 儲存
	 * @param LZ_Model $conditionModel 儲存依據的條件模型
	 * @param bool $withRelation 若有關聯時，是否包含關聯操作
	 * @return int 影響列數
	 */
	public function save($conditionModel = null,$withRelation = true){
		$modAst = LZ_ModelAssistant::getInstance();
		$sql = $modAst->getSaveSQL($this,$conditionModel);

		if($this->hasRelation() && $withRelation){
			if(null == $conditionModel) $conditionModel = $this;
			$backupFields =  $this->fields();
			
			if(!$this->_find($conditionModel,false,false)) return 0;
			
			while($this->fetch()){
				foreach($this->getRelations() as $rel){
					if(REL_MANY_MANY !== $rel['relType']){
						if(null === $rel['obj']->primary()){
							if($rel['conf'][0] === $rel['model']){
								$rel['obj']->$rel['conf'][1] = $this->primary();
								$rel['obj']->add();
							}else{
								$relLastId = $rel['obj']->add();
								$this->$rel['conf'][1] = $rel['obj']->primary();
								$this->save(null,false);
							}
						}else{
							if($rel['conf'][0] === $rel['model']){
								$rel['obj']->$rel['conf'][1] = $this->primary();
								$rel['obj']->save();
							}else{
								$this->$rel['conf'][1] = $rel['obj']->primary();
								$this->save(null,false);
							}
						}
					}else{
						$relpk = $rel['obj']->primaryKey();

						if(null === $rel['obj']->$relpk) continue;
						$rel['obj']->save();

						$thisModel = nameToUpper($this->table());
						$relModel = $rel['model'];
						$thisfdName = $rel['conf'][1][$thisModel];
						$relfdName = $rel['conf'][1][$relModel];

						$many = new LZ_Model();
						$many->fieldNames(array($thisfdName,$relfdName));
						$many->table($modAst->convertTableName($rel['conf'][0]));
						$many->$thisfdName = $this->primary();
						$many->$relfdName = $rel['obj']->$relpk;
						$many->add();
					}
				}
			}

			$this->fields($backupFields);

		}
		
		if(false === $sql) return 0;
		$this->query($sql);
		return app::db()->affectedRows();
	}

	/**
	 * 新增
	 * @param bool $withRelation 若有關聯時，是否包含關聯操作
	 * @return int 新增的ID
	 */
	public function add($withRelation = true){
		$this->query(LZ_ModelAssistant::getInstance()->getAddSQL($this));
		$lastId = app::db()->lastInsertId();
		$this->primary($lastId);
		
		if($this->hasRelation() && $withRelation){
			foreach($this->getRelations() as $rel){
				if(REL_MANY_MANY !== $rel['relType']){
					if(null === $rel['obj']->primary()){
						if($rel['conf'][0] === $rel['model']){
							$rel['obj']->$rel['conf'][1] = $lastId;
							$rel['obj']->add();
						}else{
							$relLastId = $rel['obj']->add();
							$this->primary($lastId);
							$this->$rel['conf'][1] = $relLastId;
							$this->save(null,false);
						}
					}else{
						if($rel['conf'][0] === $rel['model']){
							$rel['obj']->$rel['conf'][1] = $lastId;
							$rel['obj']->save();
						}else{
							$this->$rel['conf'][1] = $rel['obj']->primary();
							$this->save(null,false);
						}
					}
				}else{
					$relpk = $rel['obj']->primaryKey();
					$relId = null === $rel['obj']->$relpk ? $rel['obj']->add() : $rel['obj']->$relpk;
					
					$modAst = LZ_ModelAssistant::getInstance();
					$thisModel = nameToUpper($this->table());
					$relModel = $rel['model'];
					$thisfdName = $rel['conf'][1][$thisModel];
					$relfdName = $rel['conf'][1][$relModel];

					$many = new LZ_Model( $modAst->convertTableName($rel['conf'][0]) , array($thisfdName,$relfdName) );
					$many->$thisfdName = $lastId;
					$many->$relfdName = $relId;
					$many->add();
				}
				
			}
		}
		return $lastId;
	}

	/**
	 * 以主鍵為條件儲存
	 * @param int $id
	 * @return int 影響列數
	 */
	public function saveById($id){
		$this->reset(true);
		$this->primary($id);
		$this->save();
	}

	/**
	 * 刪除
	 * @param bool $withRelation 若有關聯時，是否包含關聯操作
	 * @return int 影響列數
	 */
	public function delete($withRelation = true){
		$modAst = LZ_ModelAssistant::getInstance();
		$db = app::db();
		$sql = $modAst->getDeleteSQL($this);
		
		if($this->hasRelation() && $withRelation){
			if(!$this->find(false,false)) return 0;
			$relations = $this->getRelations();
			while($this->fetch()){
				foreach($relations as $rel){
					switch ($rel['relType']){
						case REL_HAS_ONE:
							if($rel['conf'][0] === $rel['model']){
								$rel['obj']->reset(true);
								$rel['obj']->$rel['conf'][1] = $this->primary();
								$rel['obj']->delete();
							}else{
								$rel['obj']->reset(true);
								$fkVal = $this->$rel['conf'][1];
								$rel['obj']->primary($fkVal);
								$rel['obj']->delete();
							}
							break;
						case REL_MANY_MANY:
							$thisModel = nameToUpper($this->table());
							$thisfdName = $rel['conf'][1][$thisModel];
							$many = new LZ_Model();
							$many->fieldNames(array($thisfdName));
							$many->table($modAst->convertTableName($rel['conf'][0]));
							$many->$thisfdName = $this->primary();
							$many->delete();
							unset($many);
							break;
					}
				}
			}
		}
		
		$this->reset();
		$db->query($sql);
		return $db->affectedRows();
	}

	/**
	 * 以主鍵為條件刪除
	 * @param int $id
	 * @return int 影響列數
	 */
	public function deleteById($id){
		$this->reset(true);
		$this->primary($id);
		$this->delete();
	}


	/**
	 * 設定關連物件
	 * @param LZ_Model $relObj 可傳入LZ_Model組成的陣列
	 * @param string $joinType JOIN 類型 LEFT,INNER,RIGHT
	 * @return LZ_Model
	 */
	public function relate($relObj,$joinType = null){

		if(is_array($relObj)){
			foreach($relObj as $r){
				$this->relate($r,$joinType);
			}
			return $this;
		}

		if(null == $joinType) $joinType = LZ_ModelAssistant::JOIN_LEFT;
		$this->_hasRelations = true;
		$cls = get_class($relObj);
		$table = LZ_ModelAssistant::getInstance()->convertTableName($cls);
		$model =  stripSuffix($cls, 'Model');
		$thisModel = stripSuffix(get_class($this), 'Model');
		
		$relConf = app::conf()->getActiveDbConfig(true);
		$relConf = $relConf['RELATION'][$thisModel];

		$relTypes = array(REL_HAS_ONE,REL_HAS_MANY,REL_BELONGS_TO,REL_MANY_MANY);
		$relType = null;

		foreach($relTypes as $type){
			if(isset($relConf[$type][$model])){
				$relType = $type;
				$conf = $relConf[$type][$model];
				break;
			}
		}

		if(null !== $relType){
			$rel = array(
				'calss' => $cls,
				'table' => $table,
				'obj' => $relObj,
				'joinType' => $joinType,
				'relType' => $relType,
				'conf' => $conf,
				'model' => $model
			);
			$this->_relationObjs[] = $rel;
		}else{
			app::throwError(app::ft('','LZ_Model : 關連設定錯誤，沒有 [:0] 對應 [:1] 的關聯設定',array($thisModel,$model)));
		}

		if(self::RELATE_NESTED == $this->_relationMode){
			$rel['obj']->relationMode($this->_relationMode);
			if(REL_HAS_ONE == $rel['relType'] || REL_BELONGS_TO == $rel['relType']){
				$rel['obj']->limit(1);
			}
		}
		
		return $this;
	}

	/**
	 * where附加語句 - 連結邏輯運算子 AND
	 * @param string $setVal 附加語句內容
	 * @param mixed $setParams 綁定參數
	 * @return LZ_Model
	 */
	public function where($setVal,$setParams = null){
		$this->_sentences['where'][] = array($setVal,$setParams,'AND');
		return $this;
	}

	/**
	 * where附加語句 - 連結邏輯運算子 OR
	 * @param string $setVal 附加語句內容
	 * @param mixed $setParams 綁定參數
	 * @return LZ_Model
	 */
	public function orWhere($setVal,$setParams = null){
		$this->_sentences['where'][] = array($setVal,$setParams,'OR');
		return $this;
	}

	/**
	 * select附加語句
	 * @param string $setVal 附加語句內容
	 * @param mixed $setParams 綁定參數
	 * @return LZ_Model
	 */
	public function select($setVal,$setParams = null){
		$this->_sentences['select'][] = array($setVal,$setParams);
		return $this;
	}

	/**
	 * limit附加語句<br>
	 * <b>[注意]</b> 於表格模式關聯時，只適用於主表
	 * @param string $setVal 附加語句內容
	 * @param mixed $setParams 綁定參數
	 * @return LZ_Model
	 */
	public function limit($setVal,$setParams = null){
		$this->_sentences['limit'][0] = array($setVal,$setParams);
		return $this;
	}

	/**
	 * groupBy附加語句
	 * @param string $setVal 附加語句內容
	 * @param mixed $setParams 綁定參數
	 */
	public function groupBy($setVal,$setParams = null){
		$this->_sentences['groupBy'][] = array($setVal,$setParams);
		return $this;
	}

	/**
	 * orderBy附加語句
	 * @param string $setVal 附加語句內容
	 * @param mixed $setParams 綁定參數
	 * @return LZ_Model
	 */
	public function orderBy($setVal,$setParams = null){
		$this->_sentences['orderBy'][] = array($setVal,$setParams);
		return $this;
	}

	/**
	 * having附加語句
	 * @param string $setVal 附加語句內容
	 * @param mixed $setParams 綁定參數
	 * @return LZ_Model
	 */
	public function having($setVal,$setParams = null){
		$this->_sentences['having'][] = array($setVal,$setParams);
		return $this;
	}

	/**
	 * 初始化欄位資料及搜尋設定
	 * @param bool $keepRealtion 是否保留關連
	 */
	public function reset($keepRealtion = false){
		foreach($this->_fields as $k){
			$this->$k = null;
		}
		if(!$keepRealtion){
			$this->_hasRelations = false;
			$this->_relationObjs  = null;
		}
		$this->_sentences = array();
		$this->_extraFields = array();
	}

	/**
	 * 取得或設定關聯資料搜尋時加入的前綴，不傳入參數將傳回值<br>
	 * <b>附註：</b><br>
	 * 在巢狀模式中，將做為關聯子資料的名稱<br>
	 * 在表格模式中，將做為關聯欄位的前綴詞<br>
	 * @return string
	 */
	public function prefix($prefix = null){
		if(null !== $prefix){
			$this->_prefix = $prefix;
		}else{
			if(null === $this->_prefix){
				$this->_prefix = stripSuffix(get_class($this),'Model');
			}
			return $this->_prefix;
		}
	}

	/**
	 * 取得或設定資料表名稱，不傳入參數將傳回值
	 * @param string $tbName
	 * @return string
	 */
	public function table($tbName = null){
		if(null === $tbName){
			if(null === $this->_table || '' === $this->_table){
				$this->_table =  LZ_ModelAssistant::getInstance()->convertTableName(get_class($this));
			}
			return app::conf()->TABLE_PREFIX.$this->_table;
		}else{
			$this->_table = $tbName;
		}
	}

	/**
	 * 取得或設定欄位資料，不傳入參數將傳回值
	 * @param array|LZ_Model|object $data 設定資料
	 * @param bool $onlyDefined 是否只取得已定義的欄位
	 * @return array
	 */
	public function fields($data = null,$onlyDefined = false,$withoutNull = true){
		if(null === $data){
			$vars = array();
			foreach($this->_fields as $k){
				if($withoutNull){
					if(null !== $this->$k) $vars[$k] = $this->$k;
				}else{
					$vars[$k] = $this->$k;
				}
			}

			if(!$onlyDefined && !$withoutNull){
				$vars = array_merge($vars,$this->_extraFields);
			}elseif(!$onlyDefined && $withoutNull){
				foreach($this->_extraFields as $k){
					if(null !== $this->_extraFields[$k]) $vars[$k] = $this->_extraFields[$k];
				}
			}
			return $vars;
		}else{
			if(is_object($data)){
				if(get_class($data) === get_class($this)){
					$data = $data->fields(null,false,false);
				}else{
					$data = get_object_vars($data);
				}
			}
			foreach($data as $k => $v){
				if(isset($v)) $this->$k = $data[$k];
			}
		}
	}

	/**
	 * 取得或設定欄位名稱，不傳入參數將傳回值
	 * @param array $fields 設定資料
	 * @return array
	 */
	public function fieldNames($fields = null){
		if(null === $fields){
			return $this->_fields;
		}else{
			$this->_fields = $fields;
		}
	}

	/**
	 * 取得或設定主鍵名稱，不傳入參數將傳回值
	 * @param $pk 主鍵名稱
	 * @return string
	 */
	public function primaryKey($pk = null){
		if(null === $pk || '' === $pk){
			return $this->_pk;
		}else{
			$this->_pk = $pk;
		}
	}

	/**
	 * 取得或設定主鍵值
	 * @param $val 主鍵值
	 * @return int
	 */
	public function primary($val = null){
		$pk = $this->primaryKey();
		if(null === $val || '' === $val){
			return $this->$pk;
		}else{
			$this->$pk = $val;
		}
	}

	/**
	 * 清除主鍵值
	 */
	public function clearPrimary(){
		$pk = $this->primaryKey();
		$this->$pk = null;
	}

	/**
	 * 清除關聯
	 */
	public function clearRelations(){
		$this->_hasRelations = false;
		$this->_relationObjs  = null;
	}

	/**
	 * 提供魔術方法 findBy / deleteBy / saveBy
	 */
	public function  __call($name, $args) {
		$methods = array('findBy','deleteBy' ,'saveBy');
		$allow = false;
		foreach($methods as $method){
			$len = strlen($method);
			$prefix = substr($name, 0,$len);
			$next = substr($name, $len);
			
			if($prefix === $method && !empty($next)){
				$allow = true;
				$fields = explode('_', $next);
				$cnt = count($fields);
				$model = ('saveBy' === $method) ? newInstance($this) : $this;

				for($i=0;$i<$cnt;++$i){
					$fields[$i] = nameToUnderline($fields[$i]);
					if(isset($args[$i])){
						$model->$fields[$i] = $args[$i];
					}
				}

				switch($method){
					case 'findBy':
						$autoFetch = isset($args[$i]) ? $args[$i] : false;
						$withRelation = isset($args[$i+1]) ? $args[$i+1] : false;
						return $this->find($autoFetch, $withRelation);
						break;
					case 'delteBy':
						$withRelation = isset($args[$i]) ? $args[$i] : false;
						return $this->delete($withRelation);
						break;
					case 'saveBy':
						$withRelation = isset($args[$i]) ? $args[$i] : false;
						return $this->save($model, $withRelation);
						break;
				}
			}
		}

		if(!$allow){
			$cls = get_class($this);
			app::throwError(app::ft('','LZ_Model : 呼叫未定義的方法成員 [:class]::[:method]()',array('class' => $cls,'method' => $name)));
		}
	}

	/**
	 * 提供取得額外欄位
	 */
	public function __get($name){
		if(isset($this->_extraFields[$name])){
			return $this->_extraFields[$name];
		}else{
			return null;
		}
	}

	/**
	 * 提供記錄額外欄位
	 */
	public function __set($name,$value){
		$this->_extraFields[$name] = $value;
	}

	/**
	 * 取得已設定之查詢或SQL附加語句
	 * @return array
	 * @deprecated 不推薦直接操作
	 */
	public function getSentences (){
		return $this->_sentences;
	}

	/**
	 * 取得搜尋用的SQL語法
	 * @param bool $withRelation 若有關聯時，是否包含關聯操作
	 * @return string
	 */
	public function getFindSQL($withRelation = true){
		return LZ_ModelAssistant::getInstance()->getFindSQL($this,$withRelation);
	}

	/**
	 * 取得儲存用的SQL語法
	 * @param LZ_Model $conditionModel 儲存依據的條件
	 * @return string
	 */
	public function getSaveSQL($conditionModel = null){
		return LZ_ModelAssistant::getInstance()->getSaveSQL($this,$conditionModel);
	}

	/**
	 * 取得新增用的SQL語法
	 * @param array $fieldData 欄位資料
	 * @return string
	 */
	public function getAddSQL(){
		return LZ_ModelAssistant::getInstance()->getAddSQL($this);
	}

	/**
	 * 取得刪除用的SQL語法
	 * @return string
	 */
	public function getDeleteSQL(){
		return LZ_ModelAssistant::getInstance()->getDeleteSQL($this);
	}

	/**
	 * 是否擁有關連物件
	 * @return bool
	 */
	public function hasRelation(){
		return $this->_hasRelations;
	}

	/**
	 * 取得記錄總數
	 * @return int
	 */
	public function rowCount(){
		return $this->_rowCount;
	}
}