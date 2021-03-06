<?php
// PHP debug tools - http://www.gosu.pl/debug/
// Author: Cezary Tomczak [cagret at gmail.com]

error_reporting(-1);
ini_set('display_errors', true);
ini_set('log_errors', 1);
ini_set('error_log', '!phpfatal.log');
ini_set('html_errors', 0);

$microstart = microtime(1);
$memorystart = memory_get_usage();

$show_headers = false;
$show_debug = false;

function _debug()
{
	global $microstart, $memorystart;
	echo '<div>Memory: '.(round((memory_get_usage()-$memorystart)/1024)).' KB</div>';
	echo '<div>Time: '.number_format(microtime(1)-$microstart,2).'</div>';
	exit();
}

$trace_xt = 'data/xdebug-trace.xt';
$group_nested = get('group-nested', 'bool');

$total_calls = 0;
$parsed_trace = array();
$final_trace = array();
if ($show_headers) {
	$rowtpl = array('call_id'=>null,'depth'=>0,'time'=>0,'time_nested'=>null,'is_nested'=>null,'memory'=>0,'memory_nested'=>0,'func'=>'','include'=>'','file'=>'','line'=>null, 'func_count'=>1,'nested_calls'=>null,'back_in'=>null,'parent_func'=>null);
} else {
	$rowtpl = array('call_id'=>null,'depth'=>0,'time'=>0,'memory'=>0,'func'=>'','include'=>'','file'=>'','line'=>null, 'func_count'=>1);
}
/*
$rowtpl = array('call_id'=>null,'depth'=>0,'time'=>0,'time_nested'=>null,'is_nested'=>null,'memory'=>0,'memory_nested'=>0,'func'=>'','include'=>'','file'=>'','line'=>null, 'func_count'=>1,'nested_calls'=>null,'back_in'=>null,'parent_func'=>null);
*/
$rowtpl_nonfunc = array('func'=>'','time'=>0, 'memory'=>0);
$prev = null;
$prev_func = array('depth'=>0,'func'=>'','include'=>'','file'=>'');
$row = null;
$next = null;
$start_depth = null;
$last_time = null;
$last_memory = null;
$first_row = true;
$parent_func = array(0=>''); // musi byc '', nie null, sprawdzanie back_in
$summary = array();
$real_total_time = 0;
$real_total_memory = 0;
$max_depth = 0;

$xthandle = fopen($trace_xt, 'r');
if (!$xthandle) exit('Nie znaleziono pliku: '.$xthandle);

$next_line = rtrim(fgets($xthandle,512));
$chunked_trace = array();

while (!feof($xthandle))
{
	$line = $next_line;
	$next_line = rtrim(fgets($xthandle,512));

	if (isset($next)) {
		$row = $next;
	} else {
		$row = parse_line($line);
	}

	if (!$row) continue;
	if ($first_row) { $first_row = false; $prev = $row; continue; }
	if ('xdebug_stop_trace' == $row['func']) break;

	if ($row['func'])
	{
		$total_calls++;
		$next = parse_line($next_line);

		$func = $row;
		$func['time'] = bcsub($row['time'], $prev['time'], 6);
		$func['memory'] = $row['memory'] - $prev['memory'];
		if ($func['memory'] < 0) $func['memory'] = 0;
		$func['is_nested'] = $next['depth'] > $func['depth'];

		if ($func['depth'] > $max_depth) $max_depth = $func['depth'];

		//show_headers:
		//$func['prev_depth'] = $prev_func['depth'];
		//$func['prev_func'] = $prev_func['func'];
		//$func['prev_include'] = $prev_func['include'];
		//$func['prev_file'] = $prev_func['file'];
		//$func['parent_func'] = $parent_func[$func['depth']];

		if ($prev_func['func'] && ($prev_func['depth'] != $row['depth'])) {
			foreach ($chunked_trace as $tmp_func) {
				$parsed_trace[] = $tmp_func;
			}
			$chunked_trace = array();
		}

		if ($func['is_nested'] || $func['include']) {
			$chunked_trace[] = $func;
		} else if (isset($chunked_trace[$func['func']])) {
			$chunked_trace[$func['func']]['func_count']++;
			$chunked_trace[$func['func']]['time'] = bcadd($chunked_trace[$func['func']]['time'], $func['time'], 6);
			$chunked_trace[$func['func']]['memory'] += $func['memory'];
		} else {
			$chunked_trace[$func['func']] = $func;
		}

		//show_headers:
		//if ($func['is_nested']) $parent_func[$next['depth']] = $func['func'];

		$prev_func = $func;
	} else {
		$next = null;
	}
	$prev = $row;
}
foreach ($chunked_trace as $tmp_func) {
	$parsed_trace[] = $tmp_func;
}
fclose($xthandle);

//_debug();

// summary & real total time
foreach ($parsed_trace as $func)
{
	$func_name = $func['func'];
	if ($func['include']) $func_name = 'include';

	if (isset($summary[$func_name])) {
		$summary[$func_name]['count'] += $func['func_count'];
		$summary[$func_name]['time'] = bcadd($summary[$func_name]['time'], $func['time'], 6);
	} else {
		$summary[$func_name] = array(
			'func'=>$func_name, 'count' => $func['func_count'], 'time' => $func['time']
		);
	}

	$real_total_time = bcadd($real_total_time, $func['time'], 6);
	$real_total_memory += $func['memory'];
}

ob_start('ob_gzhandler');

if ($show_debug) echo '<div>Time: '.number_format(microtime(1)-$microstart,3).'</div>';

// wykorzystana zmienna $row z petli, nie przenosic tych linijek
//$total_memory = $row['memory'];

function parse_line($line)
{
	global $rowtpl;

	$tabs = explode("\t", $line);
	$count_tabs = count($tabs);

	if ($count_tabs < 5) return false;

	static $start_depth;
	if (null === $start_depth) {
		if (5 == $count_tabs) return array('func'=>false, 'time'=>$tabs[3], 'memory'=>$tabs[4]);
		if ('debug.php' == basename($tabs[8])) return false;
		if ('{main}' == $tabs[5]) return false;
		$start_depth = $tabs[0];
	}

	static $debug_console;
	if ($count_tabs > 5 && 'debug_console' == $tabs[5]) {
		$debug_console = $tabs[0];
		return false;
	}

	if (isset($debug_console) && $tabs[0] > $debug_console) {
		return false;
	} else {
		$debug_console = null;
	}

	if (5 == $count_tabs) return array('func'=>false, 'depth'=>$tabs[0]-$start_depth, 'time'=>$tabs[3], 'memory'=>$tabs[4]);

	if ('{main}' == $tabs[5]) return false;

	static $finished = false;
	if ($finished) return false;
	if ('debug_stop' == $tabs[5]) {
		$finished = true;
		return false;
	}

	$row = $rowtpl;

	static $call_id = 0;
	$call_id++;

	$row['call_id'] = $call_id;
	$row['depth'] = $tabs[0] - $start_depth;
	$row['time'] = $tabs[3];
	$row['memory'] = $tabs[4];
	$row['func'] = $tabs[5];
	$row['include'] = $tabs[7];
	$row['file'] = $tabs[8];
	$row['line'] = $tabs[9];

	return $row;
}

// sumujemy wywolania na tym samym poziomie zagniezdzenia
/*
$summed_trace = array(); // func[func_count]
$prev_depth = 0;
$chunked_trace = array();
for ($i = 0; $i < $total_calls; $i++)
{
	if ($prev_depth != $parsed_trace[$i]['depth']) {
		$summed_trace = array_merge($summed_trace, sum_chunked_trace($chunked_trace));
		$chunked_trace = array();
	}

	$chunked_trace[] = $parsed_trace[$i];
	$prev_depth = $parsed_trace[$i]['depth'];
}
$summed_trace = array_merge($summed_trace, sum_chunked_trace($chunked_trace));
$parsed_trace = $summed_trace;

function sum_chunked_trace($chunked)
{
	$ret = array();
	foreach ($chunked as $func) {
		if ($func['is_nested']) {
			$ret[] = $func;
			continue;
		}
		if (isset($ret[$func['func']])) {
			$ret[$func['func']]['func_count'] += 1;
		} else {
			$ret[$func['func']] = $func;
		}
	}
	return array_values($ret);
}
*/

$parsed_count = count($parsed_trace);
// liczymy time_nested
$f1 = 0;
$f2 = 0;
for ($i = 0; $i < $parsed_count; $i++)
{
	$f1++;
	if (!$parsed_trace[$i]['is_nested']) continue;
	$parent =& $parsed_trace[$i];
	$parent['time_nested'] = $parent['time'];
	$parent['memory_nested'] = $parent['memory'];
	$parent['nested_calls'] = 0;
	for ($k = $i+1; $k < $parsed_count; $k++) {
		$f2++;
		$parent['time_nested'] = bcadd($parent['time_nested'], $parsed_trace[$k]['time'], 6);
		$parent['memory_nested'] += $parsed_trace[$k]['memory'];
		$parent['nested_calls'] += $parsed_trace[$k]['func_count'];
		$is_last = (($k + 1) == $parsed_count);
		if ($is_last) { break; }
		if ($parsed_trace[$k+1]['depth'] <= $parent['depth']) break;
		if ($parsed_trace[$k+1]['depth'] == $parent['depth']) {
			$parsed_trace[$k+1]['back_in'] = $parent['parent_func'];
			break;
		}
	}
}

if ($show_debug) printf('<div>f1: %d , f2: %d</div>',$f1,$f2);

// wyswietlaj: grupuj po zagniezdzeniach
if ($group_nested) {
	$nested_trace = array();
}

function func_time($time)
{
	if (!isset($time)) return;
	//return $time;
	if (preg_match('/0\.00(0+)/', $time, $match)) {
		return '';
	} else {
		return number_format($time, 3);
	}
}
function func_memory($memory)
{
	if ($memory > 1024) {
		return round($memory / 1024).' KB';
	}
}

$internal = get_defined_functions();
$internal = $internal['internal'];

function get($key, $type=null)
{
	if (is_string($key)) {
		$_GET[$key] = isset($_GET[$key]) ? $_GET[$key] : null;
		if ('float' == $type) $_GET[$key] = str_replace(',','.',$_GET[$key]);
		settype($_GET[$key], $type);
		if ('string' == $type) $_GET[$key] = trim($_GET[$key]);
		return $_GET[$key];
	}
	$vars = $key;
	foreach ($vars as $key => $type) {
		$_GET[$key] = isset($_GET[$key]) ? $_GET[$key] : null;
		if ('float' == $type) $_GET[$key] = str_replace(',','.',$_GET[$key]);
		settype($_GET[$key], $type);
		if ('string' == $type) $_GET[$key] = trim($_GET[$key]);
		$vars[$key] = $_GET[$key];
	}
	return $vars;
}
function fsize($bytes)
{
	// bytes|filename, human readable file size
	if (!is_numeric($bytes)) $bytes = filesize($bytes);
	if ($bytes < 1024) return number_format($bytes/1024, 2).' KB';
	if ($bytes < 1024 * 1024) return round($bytes / 1024).' KB';
	return round($bytes / (1024*1024)).' MB';
}
function fpath($file)
{
	global $common_path;
	// human readable file path, cutting document_root prefix
	$root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
	$file = str_replace('\\', '/', $file);
	if ($file == $common_path) {
		return '/'.basename($file);
	}
	if ('/' == substr($root,-1) && strlen($root) > 1) $root = substr($root,0,-1);
	$file = str_replace($root, '', $file);
	if ($common_path) $file = str_replace($common_path, '', $file);
	$file = preg_replace('/[a-z]:/i', '', $file);
	return $file;
}
function fdate($file)
{
	static $timezone;
	if (!isset($timezone)) $timezone = ini_get('date.timezone');
	$time = filemtime($file);
	return $timezone ? date('Y-m-d H:i:s', $time) : gmdate('Y-m-d H:i:s', $time);
}
function ftime($file)
{
	return filemtime($file);
}
function common_path($parsed_trace)
{
	$common = '';
	$i = 0;
	while (true) {
		$prev_char = '';
		foreach ($parsed_trace as $trow) {
			if (!isset($trow['file'][$i])) return $common;
			$char = $trow['file'][$i];
			if ($prev_char && $char != $prev_char) return $common;
			$prev_char = $char;
		}
		$common .= $char;
		$i++;
	}
	return $common;
}
global $common_path;
$common_path = common_path($parsed_trace);
$common_path = str_replace('\\', '/', $common_path);
if ('/' == substr($common_path,-1) && strlen($common_path) > 1) {
	$common_path = substr($common_path,0,-1);
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2">
    <title>xdebug-trace</title>
	<style type="text/css">
	body,table { font: 13px arial; }
	h1 { font-size: 125%; margin: 0.5em 0em; }
	h2 { font-size: 110%; margin: 0.5em 0em; }
	p { margin: 0.5em 0em; }
	th { background: #ddd; padding: 0.1em 0.5em; }
	td { padding: 0.1em 0.5em; }
	tr { background: #f5f5f5; }
	a.internal { color:#000; }
	a.include { color:blue; }
	a.user-defined { color:blue; }
	a.expanded { text-decoration: none; }
	a.collapsed { text-decoration: underline; }
	span.user-defined { color:#2B2B99; }
	</style>
</head>
<body>

<? if ($show_debug): ?>
	<div>Time: <?=number_format(microtime(1)-$microstart,3);?></div>
<? endif; ?>

<? if (!extension_loaded('xdebug')): ?>
	<div>* Php extensions "xdebug" is not installed. This is only cache, data was generated on: <?=fdate($trace_xt);?></div>
<? endif; ?>
<? $ftime = ftime($trace_xt); $gtime = get('time','int'); ?>
<? if ($ftime > $gtime+1 || $ftime < $gtime-1): ?>
	<div>* .xt file date is differs from 'time' parameter passed from console. Data was generated on: <?=fdate($trace_xt);?></div>
<? endif; ?>

<div id="help" style="display:none;">
# - call no
{} - nesting level
() - func calls sum
</div>

<h1>xdebug-trace</h1>

<p>
	Func calls: <b><?=$total_calls;?></b>
	-
	Time: <b><?=number_format($real_total_time,3);?></b>
	-
	Memory: <b><?=fsize($real_total_memory);?></b>
	-
	<a href="javascript:scroll('summary')">Summary</a>
	-
	<a href="javascript:void(0)" onclick="alert($('help').innerHTML.trim());">Help</a>
	<!--
	-
	Wy?wietlaj: <b>sekwencyjnie</b> | <a href="javascript:void(0)" onclick="alert('todo')">grupuj po zagnie?d?eniach</a>
	-->
</p>
<p>
	<a href="javascript:collapse_all()" id="collapse-all">Collapse all</a>
	-
	<a href="javascript:expand_all()" id="expand-all">Expand all</a>
	-
	Expand to level:
	<? for ($i = 1; $i <= $max_depth; $i++): ?>
		<a href="javascript:expand_depth(<?=$i;?>)" id="expand-depth-<?=$i;?>">[<?=$i;?>]</a>
	<? endfor; ?>
</p>

<script>
function $(id) { return document.getElementById(id); }
String.prototype.trim = function() { return this.replace(/^\s*|\s*$/g, ""); };
var reg_expanded = new RegExp('expanded$');
var reg_collapsed = new RegExp('collapsed$');
function collapse_all()
{
	var table = document.getElementById('mtable');
	for (var r = 1; r < table.rows.length; r++)
	{
		if (table.rows[r].getAttribute('depth') > 0) {
			table.rows[r].style.display = 'none';
		} else {
			table.rows[r].style.display = '';
			var call_id = table.rows[r].getAttribute('call_id');
			var a = $('a_'+call_id);
			if (a) a.className = a.className.replace(/expanded$/, 'collapsed');
		}
	}
	$('collapse-all').blur();
}
function expand_all(link)
{
	var table = document.getElementById('mtable');
	for (var r = 1; r < table.rows.length; r++)
	{
		table.rows[r].style.display = '';
		var a = $('a_'+parseInt(table.rows[r].getAttribute('call_id')));
		if (a) a.className = a.className.replace(reg_collapsed, 'expanded');
	}
	$('expand-all').blur();
}
function expand_depth(base_depth)
{
	var table = document.getElementById('mtable');
	for (var r = 1; r < table.rows.length; r++)
	{
		var depth = parseInt(table.rows[r].getAttribute('depth'));
		if (depth > base_depth) table.rows[r].style.display = 'none';
		else {
			table.rows[r].style.display = '';
			var a = $('a_'+parseInt(table.rows[r].getAttribute('call_id')));
			if (depth < base_depth) {
				if (a) a.className = a.className.replace(reg_collapsed, 'expanded');
			}
			else {
				if (a) a.className = a.className.replace(reg_expanded, 'collapsed');
			}
		}
	}
	$('expand-depth-'+base_depth).blur();
}
function expand_func(base_call_id)
{
	var base_tr = $('tr_'+base_call_id);
	var base_a = $('a_'+base_call_id);
	var is_expanded = reg_expanded.test(base_a.className);
	if (is_expanded) base_a.className = base_a.className.replace(reg_expanded, 'collapsed');
	else base_a.className = base_a.className.replace(reg_collapsed, 'expanded');
	var base_depth = parseInt(base_tr.getAttribute('depth'));
	var tr = base_tr.nextSibling.nextSibling;
	while (tr) {
		var depth = parseInt(tr.getAttribute('depth'));
		if (is_expanded && depth > base_depth+1) {
			tr.style.display = 'none';
		}
		else if (depth == base_depth+1) {
			tr.style.display = is_expanded ? 'none' : '';
			var a = $('a_'+parseInt(tr.getAttribute('call_id')));
			if (a) {
				if (!is_expanded) a.className = a.className.replace(reg_expanded, 'collapsed');
			}
		}
		else if (depth <= base_depth) break;
		tr = tr.nextSibling.nextSibling;
	}
	base_a.blur();
}
function scroll(dest, ignore_when_top)
{
	dest = $(dest);
	var desty = dest.offsetTop;
	var thisNode = dest;
	while (thisNode.offsetParent &&	(thisNode.offsetParent != document.body)) {
		thisNode = thisNode.offsetParent;
		desty += thisNode.offsetTop;
	}
	if (ignore_when_top) {
		var y = scroll_current_pos();
		if (y < desty) {
			return;
		}
	}
	window.scrollTo(0,desty);
}
function scroll_current_pos()
{
	if (document.body && document.body.scrollTop)
		return document.body.scrollTop;
	if (document.documentElement && document.documentElement.scrollTop)
		return document.documentElement.scrollTop;
	if (window.pageYOffset)
		return window.pageYOffset;
	return 0;
}
</script>

<table cellspacing="1" id="mtable">
<tr>
	<th>#</th>
	<th>{}</th>
	<th>Time</th>
	<th>Function</th>
	<th>()</th>
	<th>File</th>
	<th>Memory</th>
</tr>
<? $last_depth = null; $last_file = null; $indent = array(); $last_include = ''; $tr_style = ''; $last_tr = ''; $last_tr_depth = 0; $tr_count = 0; $prev_row = null; ?>
<? foreach ($parsed_trace as $k => $trace): ?>
<?

	$func_class = '';
	if ($trace['include']) $func_class = 'include internal';
	else {
		if (in_array($trace['func'], $internal)) $func_class='internal';
		else $func_class='user-defined';
	}
	$tr_style = '';

	if ($show_headers){
		$back_in = false;
		if ($trace['file'] == $trace['prev_file'] && $trace['depth'] < $trace['prev_depth'] && isset($trace['back_in']) && $trace['depth'] < $last_tr_depth) {
			$back_in = $trace['back_in'];
			$tr_style = 'background:#eee;';
			$tr_count++;
		}

		if (!$tr_style && $k < $parsed_count && ($trace['file'] != $trace['prev_file'])) {
			$tr_style = 'background:#eee;';
			$tr_count++;
		}

		$last_tr = $tr_style;
		$last_file = $trace['file'];
	}
?>
<? if ($show_headers && $tr_style): ?>
<tr>
	<th><?=$tr_count;?></th>
	<th><?=$trace['depth'];?></th>
	<th></th>
	<th colspan="6" style="text-align: left;">
		<?
			$close_include = false;
			if ($back_in) echo 'back in: '.$back_in.'()';
			else if ($k && $trace['prev_include']) { echo $trace['prev_func'].': '.fpath($trace['file']); $close_include = true; }
			else if ($k && !$trace['prev_include'] && $trace['prev_depth'] < $trace['depth']) echo $trace['prev_func'].'() in '.fpath($trace['file']);
			else if ($k && !$trace['prev_include'] && $trace['prev_depth'] > $trace['depth'] && $trace['parent_func'] && !in_array($trace['parent_func'],array('include','include_once','require','require_once'))) echo 'back in: '.$trace['parent_func'].'()';
			else if ($k) echo 'back in: '.fpath($trace['file']);
			else echo fpath($trace['file']);
		?>
	</th>
</tr>
<? $last_tr_depth = $trace['depth']; ?>
<? endif; ?>
<?
	$depth = $trace['depth'] - $last_tr_depth;
	if ($depth < 0) $depth = 0;
	if (!isset($indent[$depth])) $indent[$depth] = str_repeat('&nbsp;',$depth*4);
?>
<tr call_id="<?=$trace['call_id'];?>" depth="<?=$trace['depth'];?>" id="tr_<?=$trace['call_id'];?>" style="<?=$trace['depth']>0?'display:none;':'';?>">
	<td nowrap align="center"><?=$trace['call_id'];?></td>
	<td nowrap align="center"><?=$trace['depth'];?></td>
	<td nowrap><?=func_time(isset($trace['time_nested']) ? $trace['time_nested'] : $trace['time']);?></td>
	<td nowrap><?=$indent[$depth];?><?if ($trace['is_nested']):?><a id="a_<?=$trace['call_id'];?>" class="<?=$func_class;?> collapsed" href="javascript:expand_func(<?=$trace['call_id'];?>)"><?else:?><span class="<?=$func_class;?>"><?endif;?><?=$trace['func'];?><?=$trace['is_nested']?'</a>':'</span>';?><? if ($trace['include']): ?>: <?=fpath($trace['include']);?><? endif; ?>
	</td>
	<td nowrap align="center"><?if (isset($trace['nested_calls'])):?><?=$trace['nested_calls']+$trace['func_count'];?><?else:?><?=$trace['func_count']>1?$trace['func_count']:'';?><?endif;?></td>
	<td nowrap><?=fpath($trace['file']);?>:<?=$trace['line'];?></td>
	<td nowrap align="right"><?=func_memory(isset($trace['memory_nested']) ? $trace['memory_nested'] : $trace['memory']);?></td>
</tr>
<? $prev_row = $trace; ?>
<? endforeach; ?>
</table>

<? if ($show_debug): ?>
	<div>Time: <?=number_format(microtime(1)-$microstart,3);?></div>
<? endif; ?>

<?
	foreach ($summary as $k => $func) {
		$summary[$k]['time'] = func_time($func['time']);
	}
	$summary = array_msort($summary, array('count'=>SORT_DESC, 'func'=>SORT_ASC));
?>

<a id="summary" name="summary"></a>
<h2 style="float:left;">Summary</h2>
<a style="float:left;margin-left: 0.5em;margin-top:0.5em;" href="javascript:void(0)" onclick="history.go(-1);window.scrollTo(0,0)">Up</a>
<div style="clear:both;"></div>

<table cellspacing="1">
<tr>
	<th>#</th>
	<th>Function</th>
	<th>Calls</th>
	<th>Time</th>
</tr>
<? $test_count=0; $k=0; foreach ($summary as $func): $test_count+=$func['count']; $k++; ?>
<? $func_style=''; if (!in_array($func['func'], $internal) && $func['func'] != 'include') $func_style='color:#2B2B99'; else $func_style='color:#000;'; ?>
<tr>
	<td><?=$k;?></td>
	<td style="<?=$func_style;?>"><?=$func['func'];?></td>
	<td><?=$func['count'];?></td>
	<td><?=$func['time'];?></td>
</tr>
<? endforeach; ?>
</table>

<? if ($show_debug): ?>
	<div>Time: <?=number_format(microtime(1)-$microstart,3);?></div>
<? endif; ?>

</body>
</html>
<? ob_end_flush(); ?>
<?php
function array_msort($array, $cols)
{
    $colarr = array();
    foreach ($cols as $col => $order) {
        $colarr[$col] = array();
        foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
    }
    $params = array();
    foreach ($cols as $col => $order) {
        $params[] =& $colarr[$col];
        $params = array_merge($params, (array)$order);
    }
    call_user_func_array('array_multisort', $params);
    $ret = array();
    $keys = array();
    $first = true;
    foreach ($colarr as $col => $arr) {
        foreach ($arr as $k => $v) {
            if ($first) { $keys[$k] = substr($k,1); }
            $k = $keys[$k];
            if (!isset($ret[$k])) $ret[$k] = $array[$k];
            $ret[$k][$col] = $array[$k][$col];
        }
        $first = false;
    }
    return $ret;

}
?>