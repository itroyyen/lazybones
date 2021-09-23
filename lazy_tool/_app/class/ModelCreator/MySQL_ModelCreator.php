<?php
/**
 * Description of MySqlModelCreator
 *
 * @author 顏宏育(Hong Yu Yan) <royyam0812@gmail.com>
 */
class MySQL_ModelCreator implements IModelCreator {

	public function getTabels(){
		$db = app::db();
		$res = $db->query('SHOW TABLES');
		$rtval = null;
		if($res){
			while($row = $db->fetch($res,1)){
				$rtval[] = $row[0];
			}
		}
		return $rtval;
	}

	public function getFields($table){
		$db = app::db();
		$table = $db->escape($table);
		$res = $db->query("SHOW FULL COLUMNS FROM `$table`");
		$rtval = null;
		if($res){
			while($row = $db->fetch($res)){
				$rtval[] = array(
					'name' => $row['Field'],
					'comment' => $row['Comment'],
				);
			}
		}
		return $rtval;
	}

	public function getPrimaryKey($table){
		$db = app::db();
		$table = $db->escape($table);
		$res = $db->query("SHOW COLUMNS FROM `$table`");
		if($res){
			while($row = $db->fetch($res)){
				if('PRI' === $row['Key']) return $row['Field'];
			}
		}
	}

	public function create($table){
		$modelName = nameToUpper($table);
		$primary = $this->getPrimaryKey($table);
		$fileds = $this->getFields($table);
		$tab = "\t";
		$field_list = '';
		$content = "<?php\n";
		$content .= "/**\n * {$modelName}\n *\n * @author RoyYan <royyan0812@gmail.com>\n */\n";
		$content .= "class {$modelName}Model extends LZ_Model {\n\n";
		
		$fieldNames = array();
		if($fileds){
			foreach($fileds as $field){
				if(!empty($field['comment'])){
					$content .= "{$tab}/**\n";
					$comments = explode("\n", $field['comment']);
					foreach($comments as $comment){
						$content .= "{$tab} * {$comment}\n";
					}
					
					$content .= "{$tab} */\n";
				}
				$content .= "{$tab}public {$tab}\${$field['name']};\n\n";
				$fieldNames[] = $field['name'];
			}
			
			$field_list = "'".(join("','",$fieldNames))."'";
		}

		$content .= "\n{$tab}protected \$_pk = '$primary';\n";
		$content .= "{$tab}protected \$_fields = array($field_list);\n";
		$content .= "\n}";

		return $content;
	}
}