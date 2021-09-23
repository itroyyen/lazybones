<?php
/*
	Database abstraction library for mysql.
	Author: Cezary Tomczak [www.gosu.pl]
*/

global $_db;
$_db = array(
	'link' => null,
	'dbname' => null,
	'transaction_level' => 0,
	'debug_file' => '',
	'debug_queries' => array(),
	'debug_count' => null,
	'debug_time' => null
);

if (!defined('DB_DETECT_MISSING_WHERE')) define('DB_DETECT_MISSING_WHERE',0);
if (!defined('DB_DETECT_INJECTION')) define('DB_DETECT_INJECTION',0);
if (!defined('DB_DEBUG')) define('DB_DEBUG',0);
if (!defined('DB_DEBUG_FILE')) define('DB_DEBUG_FILE',0);

if (!extension_loaded('mysql')) {
	trigger_error('mysql extension not loaded', E_USER_ERROR);
}
register_shutdown_function('db_cleanup');

if (ini_get('magic_quotes_gpc')) {
	ini_set('magic_quotes_runtime', 0);
	array_walk_recursive($_GET, 'db_magic_quotes_gpc');
	array_walk_recursive($_POST, 'db_magic_quotes_gpc');
	array_walk_recursive($_COOKIE, 'db_magic_quotes_gpc');
}
if (DB_DETECT_INJECTION) {
	array_walk_recursive($_GET, 'db_detect_injection_gpc');
	array_walk_recursive($_POST, 'db_detect_injection_gpc');
	array_walk_recursive($_COOKIE, 'db_detect_injection_gpc');
}

// -------- polaczenie + wybor bazy

function db_connect($options)
{
	global $_db;
	if ($_db['link']) trigger_error('db_connect(): already connected', E_USER_ERROR);
	$_db['dbname'] = $options['dbname'];
	if (DB_DEBUG) $microstart = microtime(true);
	$_db['link'] = mysql_connect($options['host'], $options['user'], $options['pass']);
	if (DB_DEBUG) {
		$time = microtime(true)-$microstart;
		$_db['debug_queries'][] = array('query'=>'connect()', 'time'=>$time);
		$_db['debug_count']++; $_db['debug_time'] += $time;
	}
	if (!$_db['link']) {
		trigger_error('db_connect(): '.mysql_error(), E_USER_ERROR);
	}
	if (!mysql_select_db($options['dbname'], $_db['link'])) {
		trigger_error('db_connect(): '.mysql_error($_db['link']), E_USER_ERROR);
	}
	db_query("SET NAMES {$options['charset']}");
}

// -------- podstawowe funkcje: db_query() db_one() db_row() db_list() db_assoc()

function db_query($query, $data=null, $offset=null, $limit=null, array $options=null)
{
	global $_db;
	if (!$_db['link']) {
		if (defined('DB_CONNECT') && DB_CONNECT) include DB_CONNECT;
		else trigger_error('Query call, but not connected', E_USER_ERROR);
	}
	if (isset($offset) || isset($limit)) $query = db_limit($query, $offset, $limit);
	if (DB_DETECT_MISSING_WHERE) db_detect_missing_where($query);
	$data = (array) $data;
	if ($data) $query = db_bind($query, $data);
	if (DB_DETECT_INJECTION) db_detect_injection($query);
	if (DB_DEBUG) $microstart = microtime(true);
	if (!empty($options['unbuffered'])) $result = mysql_unbuffered_query($query, $_db['link']);
	else $result = mysql_query($query, $_db['link']);
	if (DB_DEBUG) {
		$time = microtime(true)-$microstart;
		$_db['debug_queries'][] = array('query'=>$query, 'time'=>$time);
		$_db['debug_count']++; $_db['debug_time'] += $time;
	}
	if (false === $result) {
		if (!empty($options['try'])) {
			return false;
		} else {
			trigger_error(mysql_error($_db['link']).'. Query: '.$query, E_USER_ERROR);
		}
	}
	return $result;
}
function db_one($query, $data=null, $offset=null, $limit=null)
{
	$result = db_query($query, $data, $offset, $limit);
	$row = mysql_fetch_row($result);
	return $row ? $row[0] : false;
}
function db_row($query, $data=null, $offset=null, $limit=null)
{
	if (is_resource($query)) {
		return mysql_fetch_assoc($query);
	} else {
		return mysql_fetch_assoc(db_query($query, $data, $offset, $limit));
	}
}
function db_list($query, $data=null, $offset=null, $limit=null)
{
	$result = db_query($query, $data, $offset, $limit);
	$rows = array();
	while ($row = mysql_fetch_assoc($result)) {
		$rows[] = $row;
	}
	return $rows;
}
function db_assoc($query, $data=null, $offset=null, $limit=null)
{
	$result = db_query($query, $data, $offset, $limit);
	$both = mysql_fetch_array($result, MYSQL_BOTH);
	if (!$both) return array();
	$num = array();
	$row = array();
	$first_key = null;
	foreach ($both as $k => $v) {
		if (is_numeric($k)) { $num[$k] = $v; continue; }
		else { if (!isset($first_key)) { $first_key = $k; } $row[$k] = $v; }
	}
	$rows = array();
	$count_num = count($num);
	if (1 == $count_num) {
		$rows[] = $num[0];
		while ($num = mysql_fetch_row($result)) {
			$rows[] = $num[0];
		}
		return $rows;
	} else if (2 == $count_num) {
		$rows[$num[0]] = $num[1];
		while ($num = mysql_fetch_row($result)) {
			$rows[$num[0]] = $num[1];
		}
		return $rows;
	} else {
		if ($count_num > 2 && count($row) <= 2) {
			trigger_error('db_assoc(): if specified more than two fields, then each of them must have a unique name. Query: '.$query, E_USER_ERROR);
		}
		$rows[$row[$first_key]] = $row;
		while ($row = mysql_fetch_assoc($result)) {
			$rows[$row[$first_key]] = $row;
		}
		return $rows;
	}
}

// -------- automatyczne budowanie+wykonywanie zapytan insert/update/delete

function db_insert($table, array $data)
{
	$table = db_filter($table);
	$fields = '';
	$values = '';
	$first = true;
	foreach ($data as $k => $v) {
		if ($first) {
			$fields .= db_filter($k);
			$values .= db_quote($v);
			$first = false;
		} else {
			$fields .= ',' . db_filter($k);
			$values .= ',' . db_quote($v);
		}
	}
	$query = "INSERT INTO $table ($fields) VALUES ($values)";
	db_query($query);
}
function db_update($table, $data, array $where_array)
{
	$set = '';
	$first = true;
	foreach ($data as $k => $v) {
		if ($first) {
			$set   .= db_filter($k) . '=' . db_quote($v);
			$first  = false;
		} else {
			$set .= ', ' . db_filter($k) . '=' . db_quote($v);
		}
	}
	$table = db_filter($table);
	$where_sql = db_where($where_array);
	if (!$where_sql) trigger_error('db_update(): where is empty', E_USER_ERROR);
	db_query("UPDATE $table SET $set $where_sql");
}
function db_delete($table, array $where_array)
{
	$table = db_filter($table);
	$where_sql = db_where($where_array);
	if (!$where_sql) trigger_error('db_delete(): where is empty', E_USER_ERROR);
	db_query("DELETE FROM $table $where_sql");
}

// -------- warunki: WHERE, IS NULL, IN(), LIMIT

function db_where(array $where_array, $prefix = '')
{
	$prefix = str_replace('.', '', $prefix);
	if (!$where_array) return '';
	$where_sql = '';
	foreach ($where_array as $wh_key => $wh_val)
	{
		if (is_numeric($wh_key)) {
			if ($wh_val) {
				if ($prefix && !preg_match('/^\w+\./', $wh_val)) {
					$wh_val = $prefix.'.'.trim($wh_val);
				}
				if ($where_sql) $where_sql .= ' AND ';
				$where_sql .= $wh_val;
			}
		} else {
			if ($wh_key && $wh_val) {
				if ($prefix && !preg_match('/^\w+\./', $wh_key)) {
					$wh_key = $prefix.'.'.$wh_key;
				}
				$wh_val = db_condition($wh_key, $wh_val);
				if ($where_sql) $where_sql .= ' AND ';
				$where_sql .= $wh_val;
			}
		}
	}
	if ($where_sql) {
		$where_sql = ' WHERE '.$where_sql;
	}
	return $where_sql;
}
function db_condition($k, $v)
{
	if (is_null($v)) return (db_filter($k).' IS NULL');
	else return (db_filter($k).' = '.db_quote($v));
}
function db_in_array($arr)
{
	$in = '';
	foreach ($arr as $v) {
		if ($in) $in .= ',';
		$in .= db_quote($v);
	}
	return $in;
}
function db_limit($query, $offset, $limit)
{
	$offset = (int) $offset;
	$limit = (int) $limit;
	//if (stristr($query,'limit')) {
	//	$query = preg_replace('/^([\s\S]+)LIMIT\s+\d+\s+OFFSET\s+\d+\s*$/i', '$1', $query);
	//	$query = preg_replace('/^([\s\S]+)LIMIT\s+\d+\s*,\s*\d+\s*$/i', '$1', $query);
	//}
	return $query." LIMIT $offset, $limit"; // mysql 3.23 doesn't understand "LIMIT x OFFSET z"
}

// --------

function db_insert_id()
{
	global $_db;
	return mysql_insert_id($_db['link']);
}
function db_try($query, $data=null, $offset=null, $limit=null)
{
	return db_query($query, $data, $offset, $limit, array('try'=>1));
}
function db_free($result)
{
	return mysql_free_result($result);
}

// -------- escape / wiazanie / filtrowanie danych

function db_quote($value)
{
	global $_db;
	if (!$_db['link']) {
		if (defined('DB_CONNECT') && DB_CONNECT) include DB_CONNECT;
		else trigger_error('Query call, but not connected', E_USER_ERROR);
	}
	if (is_string($value)) return "'".mysql_real_escape_string($value, $_db['link'])."'";
	if (is_int($value)) return $value;
	if (is_float($value)) return $value;
	if (is_bool($value)) return (int) $value;
	if (is_null($value)) return 'NULL';
	trigger_error('db_quote(): invalid data type: '.gettype($value), E_USER_ERROR);
}
function db_escape($string)
{
	global $_db;
	if (!$_db['link']) {
		if (defined('DB_CONNECT') && DB_CONNECT) include DB_CONNECT;
		else trigger_error('Query call, but not connected', E_USER_ERROR);
	}
	return mysql_real_escape_string($string, $_db['link']);
}
function db_escape_like($string)
{
	global $_db;
	$string = db_escape($string);
	return str_replace(array('%',  '_'), array('\%', '\_'), $string);
}
function db_like($field, $value)
{
	if ($field && $value) {
		return sprintf("%s LIKE '%s'", db_filter($field), '%'.db_escape_like($value).'%');
	}
	return '';
}
function db_filter($string)
{
	$string = substr($string,0,50);
	$string = preg_replace('/[^a-z0-9\_\.]/i', '', $string);
	$string = preg_replace('/^\d+/', '', $string);
	return $string;
}
function db_bind($query, $data)
{
	$data = (array) $data;
	$like_search = array("'%", "%'"); // special case: field LIKE '%asd%', need to ignore that
	$like_replace = array("'@@@@@", "@@@@@'");
	$query = str_replace($like_search, $like_replace, $query);
	foreach ($data as $k => $v) {
		$query = str_replace('%'.$k, db_quote($v), $query, $replaced);
		if (!$replaced && is_numeric($k)) {
			trigger_error("db_bind(): %$k key not found. Query: $query", E_USER_ERROR);
		}
	}
	return str_replace($like_replace, $like_search, $query);
}

// -------- transakcje

function db_begin()
{
	global $_db;
	if (0 == $_db['transaction_level']) {
		db_query('START TRANSACTION');
	}
	$_db['transaction_level']++;
}
function db_commit()
{
	global $_db;
	if ($_db['transaction_level'] < 1) {
		trigger_error('Transaction not yet started, but commit called', E_USER_ERROR);
	}
	if (1 == $_db['transaction_level']) {
		db_query('COMMIT');
	}
	$_db['transaction_level']--;
}
function db_rollback($cleanup_call = false)
{
	global $_db;
	if ($_db['transaction_level'] > 1 && !$cleanup_call) {
		trigger_error('Nested transaction cannot be rollbacked', E_USER_ERROR);
	}
	db_query('ROLLBACK');
	$_db['transaction_level'] = 0;
}

// -------- wykrywanie: brakujacego where w update/delete, sql injection

function db_detect_missing_where($query)
{
	if (preg_match('/^\s*(update|delete)/i',$query) && !stristr($query,'where')) {
		trigger_error("Detected missing 'where' condition. Query: $query", E_USER_ERROR);
	}
}
function db_detect_injection($query)
{
	$inside_quote = false;
	$query = '';
	$query = str_replace('\\\\', '', $query);
	$query_len = strlen($query);
	for ($i = 0; $i < $query_len; $i++) {
		$prev_char = isset($query{$i-1}) ? $query{$i-1} : null;
		$char = $query{$i};
		if ($char == "'") {
			if ($inside_quote) {
				if ($prev_char != '\\') {
					$inside_quote = false;
					continue;
				}
			} else {
				$inside_quote = true;
			}
		}
		if (!$inside_quote) $query .= $char;
	}
	if (strstr($query, '--') || strstr($query, '#')
		|| strstr($query, '/*') || strstr($query, '0x')) {
		trigger_error("Detected sql injection. Query: $query", E_USER_ERROR);
	}
}
function db_detect_injection_gpc($val)
{
	if (stristr($val,'union') && stristr($val,'select')) {
		trigger_error("Detected sql injection. GPC: $val", E_USER_ERROR);
	}
}

// -------- magic quotes

function db_magic_quotes_gpc(&$val)
{
	$val = stripslashes($val);
}

// -------- inicjalizacja / porzadki po zakonczeniu wykonywania skryptu

function db_close()
{
	global $_db;
	if ($_db['link']) {
		mysql_close($_db['link']);
	}
}
function db_cleanup()
{
	static $called = false;
	if ($called) return;
	else $called = true;

	global $_db;
	if (0 != $_db['transaction_level']) db_rollback(true);
	db_close();
	if (DB_DEBUG && DB_DEBUG_FILE) {
		file_put_contents(DB_DEBUG_FILE, serialize($_db['debug_queries']));
	}
}

?>