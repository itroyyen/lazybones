<?php
/**************************************************************************
 * Lazybones web development framework for PHP 5.1.2 or newer
 *
 * @author      顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @copyright   Copyright (c) 2010, 顏宏育(Hong Yu Yan)
 **************************************************************************/

/**
 * 視圖 - Magic模式語法編譯器
 *
 * @author 顏宏育(Hong Yu Yan) <royyan0812@gmail.com>
 * @package core
 */
class LZ_ViewMagicCompiler{

	private static $_instance;

	const B_PARAM    = '(';
	const E_PARAM    = ')';
	const COMMA      = ',';
	const STR        = '"';
	const CHAR       = "'";
	const ARR        = ':';
	const REF        = '.';
	const ESCAPE     = "\\";
	const VARIABLE   = '$';
	const STATIC_REF = '@';
	const SEPARATOR  = ';';
	const LANG       = '#';
	const QUIET      = '!';

	const BLOCK_IF           = '#if';
	const BLOCK_ELSEIF       = '#elseif';
	const BLOCK_ELSE         = '#else';
	const BLOCK_ENDIF        = '#endif';
	const BLOCK_BEGIN        = '#begin';
	const BLOCK_END          = '#end';
	const BLOCK_INCLUDE_VIEW = '#include';
	const BLOCK_BEGIN_HIDE   = '#begin_hide';
	const BLOCK_END_HIDE     = '#end_hide';
	const DELIMITER          = '$a-zA-Z!:(_.#';
	
	const L_DELIMITER           = '{';
	const R_DELIMITER           = '}';
	const L_DELIMITER2           = '<%';
	const R_DELIMITER2          = '%>';
	const L_CONDITION_DELIMITER = '(';
	const R_CONDITION_DELIMITER = ')';
	const L_BLOCK_DELIMITER     = '<!--';
	const R_BLOCK_DELIMITER     = '-->';

	const DEFAULT_VNAME = '$v';

	private static $_breaks;
	private static $_operators;

	private function  __construct() {
		self::$_breaks = array(self::ARR,self::REF,self::B_PARAM,self::COMMA,' ','=','>','<','!','|','-','+','*','/');
		self::$_operators = array('&','=','>','<','!','|','-','+','*','/','%');
	}

	/**
	 * @return LZ_ViewMagicCompiler
	 */
	public static function getInstance(){
		if(null === self::$_instance) self::$_instance = new LZ_ViewMagicCompiler();
		return self::$_instance;
	}

	/**
	 * 編譯樣板
	 * @param string $fromPath 來源路徑
	 * @param string $toPath 目的路徑
	 */
	public function compile($fromPath,$toPath){
		
		$varname = self::DEFAULT_VNAME;
		$useStack = false;
		$content = file_get_contents($fromPath);
		
		$L_DELIMITER           = preg_quote(self::L_DELIMITER);
		$R_DELIMITER           = preg_quote(self::R_DELIMITER);
		$L_DELIMITER2           = preg_quote(self::L_DELIMITER2);
		$R_DELIMITER2           = preg_quote(self::R_DELIMITER2);
		$R_DELIMITER           = preg_quote(self::R_DELIMITER);
		$L_BLOCK_DELIMITER     = preg_quote(self::L_BLOCK_DELIMITER);
		$R_BLOCK_DELIMITER     = preg_quote(self::R_BLOCK_DELIMITER);
		$BLOCK_INCLUDE_VIEW    = preg_quote(self::BLOCK_INCLUDE_VIEW);
		$BLOCK_BEGIN_HIDE      = preg_quote(self::BLOCK_BEGIN_HIDE);
		$BLOCK_END_HIDE        = preg_quote(self::BLOCK_END_HIDE);
		$BLOCK_ENDIF           = preg_quote(self::BLOCK_ENDIF);
		$BLOCK_END             = preg_quote(self::BLOCK_END);
		$BLOCK_ELSE            = preg_quote(self::BLOCK_ELSE);
		$BLOCK_BEGIN           = preg_quote(self::BLOCK_BEGIN);
		$DELIMITER             = self::DELIMITER;
		$BLOCK_IF              = preg_quote(self::BLOCK_IF);
		$BLOCK_ELSEIF          = preg_quote(self::BLOCK_ELSEIF);
		$SEPARATOR             = preg_quote(self::SEPARATOR);
		$L_CONDITION_DELIMITER = preg_quote(self::L_CONDITION_DELIMITER);
		$R_CONDITION_DELIMITER = preg_quote(self::R_CONDITION_DELIMITER);
		
		//include view
		$pattern = "/{$L_BLOCK_DELIMITER}([ ]*){$BLOCK_INCLUDE_VIEW}{$L_CONDITION_DELIMITER}(.*){$R_CONDITION_DELIMITER}([ ]*){$R_BLOCK_DELIMITER}/U";
		if (preg_match_all($pattern, $content, $matchs)) {
			$cnt = count($matchs[1]);
			for($i=0;$i<$cnt;++$i){
				$replace  = file_get_contents(app::conf()->VIEW_PATH . $matchs[2][$i].VIEW_EXT);
				$search = self::L_BLOCK_DELIMITER.$matchs[1][$i].self::BLOCK_INCLUDE_VIEW.'('.$matchs[2][$i].')'.$matchs[3][$i].self::R_BLOCK_DELIMITER;
				$content  =  str_replace($search, $replace, $content);
			}
		}

		app::event()->trigger(LZ_IView::EVENT_PREPROCESS,array(&$content));

		//hide block
		$pattern = "/{$L_BLOCK_DELIMITER}([ ]*){$BLOCK_BEGIN_HIDE}([ ]*){$R_BLOCK_DELIMITER}(.*){$L_BLOCK_DELIMITER}([ ]*){$BLOCK_END_HIDE}([ ]*){$R_BLOCK_DELIMITER}/Us";
		if (preg_match_all($pattern, $content, $matchs)) {
			$cnt = count($matchs[1]);
			for($i=0;$i<$cnt;++$i){
				$search  = self::L_BLOCK_DELIMITER.$matchs[1][$i].self::BLOCK_BEGIN_HIDE.$matchs[2][$i].self::R_BLOCK_DELIMITER;
				$search .= $matchs[3][$i];
				$search .= self::L_BLOCK_DELIMITER.$matchs[4][$i].self::BLOCK_END_HIDE.$matchs[5][$i].self::R_BLOCK_DELIMITER;
				$content  =  str_replace($search, '', $content);
			}
		}

		//endif | end | else
		$pattern = "/{$L_BLOCK_DELIMITER}[ ]*{$BLOCK_ENDIF}[ ]*{$R_BLOCK_DELIMITER}/U";
		$content = preg_replace($pattern,"<?php endif; ?>", $content);
		$pattern = "/{$L_BLOCK_DELIMITER}[ ]*{$BLOCK_END}[ ]*{$R_BLOCK_DELIMITER}/U";
		$content = preg_replace($pattern,"<?php endforeach;\nendif;\n{$varname}=\$_stack_[--\$_stack_lv_];\n?>", $content);
		$pattern = "/{$L_BLOCK_DELIMITER}[ ]{$BLOCK_ELSE}[ ]*{$R_BLOCK_DELIMITER}/U";
		$content = preg_replace($pattern,"<?php else:?>", $content);

		//if | elseif
		$types = array('if','elseif');
		$blockNames = array(self::BLOCK_IF,self::BLOCK_ELSEIF);
		$blockNameQuotes = array($BLOCK_IF,$BLOCK_ELSEIF);
		$j = 0;
		foreach($types as $type){
			$pattern = "/{$L_BLOCK_DELIMITER}([ ]*){$blockNameQuotes[$j]}{$L_CONDITION_DELIMITER}(.*){$R_CONDITION_DELIMITER}([ ]*){$R_BLOCK_DELIMITER}/U";
			if (preg_match_all($pattern, $content, $matchs)) {
				$cnt = count($matchs[1]);
				for($i=0;$i<$cnt;++$i){
					$statement = self::_parseVar($matchs[2][$i]);
					$replace  = "<?php $type($statement): ?>";
					$search = self::L_BLOCK_DELIMITER.$matchs[1][$i].$blockNames[$j].'('.$matchs[2][$i].')'.$matchs[3][$i].self::R_BLOCK_DELIMITER;
					$content  =  str_replace($search, $replace, $content);
				}
			}
			++$j;
		}

		//begin {initialization}{preprocess}
		$type = 'foreach';
		$pattern = "/{$L_BLOCK_DELIMITER}([ ]*){$BLOCK_BEGIN}{$L_CONDITION_DELIMITER}(.*){$R_CONDITION_DELIMITER}".
					"(({$L_DELIMITER}(.*){$R_DELIMITER})({$L_DELIMITER}(.*){$R_DELIMITER}))".
					"([ ]*){$R_BLOCK_DELIMITER}/U";
		if (preg_match_all($pattern, $content, $matchs)) {
			$cnt = count($matchs[1]);
			for($i=0;$i<$cnt;++$i){
				$statement = self::_parseVar($matchs[2][$i]);
				$replace  = "<?php\n\$_stack_[\$_stack_lv_++] = $varname;\nif(isset($statement)):\n";
				
				$inits = explode($SEPARATOR, $matchs[5][$i]);
				foreach($inits as $init){
					$appends[] = ($temp = self::_parseVar($init)) ? $temp.";\n" : '';
				}
				$replace .= ($temp = join(";\n", $appends)) ? $temp : '' ;
				unset($appends);
				
				$replace .= "$type($statement as $varname):";

				$preps = explode($SEPARATOR, $matchs[7][$i]);
				foreach($preps as $prep){
					$appends[] = ($temp = self::_parseVar($prep)) ? $temp.";\n" : '';
				}
				$replace .= ($temp = join(";\n", $appends)) ? "\n".$temp : '' ;
				unset($appends);

				$replace .= "?>";
				
				$search  = self::L_BLOCK_DELIMITER.$matchs[1][$i].self::BLOCK_BEGIN.'('.$matchs[2][$i].')';
				$search .= self::L_DELIMITER.$matchs[5][$i].self::R_DELIMITER;
				$search .= self::L_DELIMITER.$matchs[7][$i].self::R_DELIMITER;
				$search .= $matchs[8][$i].self::R_BLOCK_DELIMITER;
				$content  =  str_replace($search, $replace, $content);
			}
			$useStack = true;
		}

		//begin {initialization}
		$type = 'foreach';
		$pattern = "/{$L_BLOCK_DELIMITER}([ ]*){$BLOCK_BEGIN}{$L_CONDITION_DELIMITER}(.*){$R_CONDITION_DELIMITER}".
					"(({$L_DELIMITER}(.*){$R_DELIMITER}))".
					"([ ]*){$R_BLOCK_DELIMITER}/U";
		if (preg_match_all($pattern, $content, $matchs)) {
			$cnt = count($matchs[1]);
			for($i=0;$i<$cnt;++$i){
				$statement = self::_parseVar($matchs[2][$i]);
				$replace  = "<?php\n\$_stack_[\$_stack_lv_++] = $varname;\nif(isset($statement)):\n";

				$inits = explode($SEPARATOR, $matchs[5][$i]);
				foreach($inits as $init){
					$appends[] = ($temp = self::_parseVar($init)) ? $temp.";\n" : '';
				}
				$replace .= ($temp = join(";\n", $appends)) ? $temp : '' ;
				unset($appends);

				$replace .= "$type($statement as $varname): ?>";

				$search  = self::L_BLOCK_DELIMITER.$matchs[1][$i].self::BLOCK_BEGIN.'('.$matchs[2][$i].')';
				$search .= self::L_DELIMITER.$matchs[5][$i].self::R_DELIMITER;
				$search .= $matchs[6][$i].self::R_BLOCK_DELIMITER;
				$content  =  str_replace($search, $replace, $content);
			}
			$useStack = true;
		}
		
		//begin
		$type = 'foreach';
		$pattern = "/{$L_BLOCK_DELIMITER}([ ]*){$BLOCK_BEGIN}{$L_CONDITION_DELIMITER}(.*){$R_CONDITION_DELIMITER}([ ]*){$R_BLOCK_DELIMITER}/U";
		if (preg_match_all($pattern, $content, $matchs)) {
			$cnt = count($matchs[1]);
			for($i=0;$i<$cnt;++$i){
				$statement = self::_parseVar($matchs[2][$i]);
				$replace  = "<?php\n\$_stack_[\$_stack_lv_++] = $varname;\nif(isset($statement)):\n";
				$replace .= "$type($statement as $varname): ?>";
				$search = self::L_BLOCK_DELIMITER.$matchs[1][$i].self::BLOCK_BEGIN.'('.$matchs[2][$i].')'.$matchs[3][$i].self::R_BLOCK_DELIMITER;
				$content  =  str_replace($search, $replace, $content);
			}
			$useStack = true;
		}

		//variable style 1
		$pattern = "/{$L_DELIMITER}([{$DELIMITER}]{1}.*){$R_DELIMITER}/U";
		if (preg_match_all($pattern, $content, $matchs)) {
			foreach($matchs[1] as $match){
				$quiet = false;
				$expression = $match;
				if(isset($expression{0})){
					if(self::QUIET === $expression{0}){
						$quiet = true;
						$expression = substr($expression, 1);
					}
				}

				$statement = self::_parseVar($expression);

				if($quiet){
					$replace  = "<?php $statement;?>";
				}else{
					$replace  = "<?php echo $statement;?>";
				}
				$search = self::L_DELIMITER.$match.self::R_DELIMITER;
				$content  =  str_replace($search, $replace, $content);
			}
		}

		//variable style 2
		$pattern = "/{$L_DELIMITER2}([{$DELIMITER}]{1}.*){$R_DELIMITER2}/U";
		if (preg_match_all($pattern, $content, $matchs)) {
			foreach($matchs[1] as $match){
				$quiet = false;
				$expression = $match;
				if(isset($expression{0})){
					if(self::QUIET === $expression{0}){
						$quiet = true;
						$expression = substr($expression, 1);
					}
				}

				$statement = self::_parseVar($expression);

				if($quiet){
					$replace  = "<?php $statement;?>";
				}else{
					$replace  = "<?php echo $statement;?>";
				}
				$search = self::L_DELIMITER2.$match.self::R_DELIMITER2;
				$content  =  str_replace($search, $replace, $content);
			}
		}

		if($useStack) $content = "<?php\n\$_stack_lv_=0;\n\$_stack_ = array();\n?>".$content;
		
		file_put_contents($toPath, $content);
	}
	
	/**
	 * 解析變數內容
	 * @param string $val 要解析的內容
	 * @return string
	 */
	private static function _parseVar($val){
		$val = trim($val);
		$vname = self::DEFAULT_VNAME;
		$v = str_split($val);
		$cnt = count($v);
		$s = $temp = '';
		$rtval = '';
		$first = true;
		$breaks = self::$_breaks;
		
		for($i=0;$i<$cnt;++$i){
			switch($v[$i]){
				case self::ARR:
					if(preg_match('/[a-zA-Z_]{1}/', $v[$i+1])){
						if(true === $first) $rtval .= $vname;
						$temp = self::_getZone($v,$i,$i+1,$cnt,self::$_breaks);
						if(is_numeric($temp)){
							$rtval .= "[$temp]";
						}else{
							$rtval .= "['$temp']";
						}
						$t = 0;
						while(true){
							$ii = $i+1;
							if($ii < $cnt){
								if(self::ARR !== $v[$ii]) break;
								$temp = self::_getZone($v,$i,$ii+1,$cnt,self::$_breaks);
								if(is_numeric($temp)){
									$rtval .= "[$temp]";
								}else{
									$rtval .= "['$temp']";
								}
							}else{
								break;
							}
						}
						$first = false;
					}else{
						$first = true;
						$rtval .= $v[$i];
					}
					break;
				case self::REF:
					if(preg_match('/[a-zA-Z_]{1}/', $v[$i+1])){
						if(true === $first) $rtval .= $vname;
						$rtval .= '->';
						$temp = self::_getZone($v,$i,$i+1,$cnt,self::$_breaks);
						$rtval .= $temp;
						$first = false;
					}else{
						$first = true;
						$rtval .= $v[$i];
					}
					break;
				case self::STATIC_REF:
					$rtval .= '::';
					$temp = self::_getZone($v,$i,$i+1,$cnt,self::$_breaks);
					$rtval .= $temp;
					$first = false;
					break;
				case self::VARIABLE:
					$temp = self::_getZone($v,$i,$i,$cnt,self::$_breaks);
					$rtval .= $temp;
					$first = false;
					break;
				case self::B_PARAM:
					$temp = self::_getParamZone($v,$i,$i+1,$cnt);
					$temp = self::_parseVar($temp);
					++$i;
					$rtval .= self::B_PARAM.$temp.self::E_PARAM;
					break;
				case self::STR:
					$temp = self::_getZone($v,$i,$i+1,$cnt,array(self::STR),self::ESCAPE);
					$rtval .= self::STR.$temp.self::STR;
					++$i;
					break;
				case self::CHAR:
					$temp = self::_getZone($v,$i,$i+1,$cnt,array(self::CHAR),self::ESCAPE);
					$rtval .= self::CHAR.$temp.self::CHAR;
					++$i;
					break;
				case self::LANG:
					$langName = '';
					if('(' !== $v[$i+1]){
						$langName = self::_getZone($v,$i,$i+1,$cnt,array(self::B_PARAM));
					}
					++$i;
					$temp = self::_getParamZone($v,$i,$i+1,$cnt);
					$temp = $temp = self::_parseVar($temp);
					$temp = ('' === $langName) ? "tt($temp)" : "t('$langName',$temp)";
					++$i;
					$rtval .= $temp;
					$first = true;
					break;
				case self::COMMA:
					$first = true;
				default:
					if(in_array($v[$i], self::$_operators)) $first = true;
					$rtval .= $v[$i];
					break;
			}
		}

		return $rtval;
	}

	private static function _getZone(&$v,&$index,$start,$end,$breaks,$escape = null){
		$rtval = '';
		for($i=$start;$i<$end;++$i){
			if($escape === $v[$i]){
				if($i+1 < $end) $rtval .= $v[$i].$v[$i+1];
				++$i;
				continue;
			}
			if(in_array($v[$i], $breaks)){
				$index = ($i !== $end-1) ? $i-1 : $i++;
				return $rtval;
			}
			$rtval .= $v[$i];
		}
		$index = $i;
		return $rtval;
	}

	private static function _getParamZone(&$v,&$index,$start,$end){
		$rtval = '';
		$lv = 0;
		for($i=$start;$i<$end;++$i){
			if(self::B_PARAM === $v[$i]) $lv++;

			if(self::E_PARAM === $v[$i]){
				$index = ($i !== $end-1) ? $i-1 : $i++;
				if(0 === $lv) return $rtval;
				$lv--;
			}
			$rtval .= $v[$i];
		}
		$index = $i;
		return $rtval;
	}
}