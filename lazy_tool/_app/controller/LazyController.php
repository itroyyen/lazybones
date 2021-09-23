<?php
/* 
 * 主控制器
 */
class LazyController extends LZ_Controller{

	/**
	 * 入口動作
	 */
	public function Action_Index(){
		$this->view()->render();
	}

	public function Action_CreateModel(){
		$creator = $this->loadCreator();
		if(isset($this->v[0])){
			$table = $this->v[0];
			if(isset($this->v[1])){
				$result = $creator->create($table);
				$modelName = nameToUpper($table).'Model';
				$filename = app::conf()->app_model_path . $modelName . PHP_EXT;
				file_put_contents($filename, $result);
				
				app::redirectUrl('create_model');
			}else{
				$v['result'] = $creator->create($table);
				$v['tbname'] = $table;
				$this->view()->render($v);
			}
		}else{
			$v['tables'] = $creator->getTabels();
			$this->view()->render($v);
		}
		
	}

	/**
	 * 載入Model產生器
	 * @return IModelCreator
	 */
	protected function loadCreator(){
		app::loadAppClass('ModelCreator/IModelCreator');
		$dbconf = app::conf()->getActiveDbConfig();
		$className = $dbconf['DRIVER'] . '_ModelCreator';
		app::loadAppClass('ModelCreator/'.$className);
		return new $className;
	}
	
}