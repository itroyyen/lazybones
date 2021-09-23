<?php

/*
	Debugging sql queries.
	(c) Cezary Tomczak [www.gosu.pl]
*/

exit('ANALYZE todo');

if (0 == 1) {
	trigger_error('permission denied', E_USER_ERROR);
}

$get = req_get(array(
	'id' => REQ_STR
));
$post = req_post(array(
	'query' => REQ_STR
));

if (!preg_match('#^\d+\.\d+$#', $get['id'])) {
	trigger_error('Invalid dbg id', E_USER_ERROR);
}
$file = sprintf('data/dbg/%s.ser', $get['id']);
if (!file_exists($file)) {
	trigger_error('Dbg file not found', E_USER_ERROR);
}

$post['query'] = html_undo($post['query']);

if (!preg_match('#^\s*SELECT#i', $post['query'])) {
	trigger_error('Invalid query', E_USER_ERROR);
}

$rows = db_list('EXPLAIN '.$post['query']);

function query_color($query)
{
	$words = array('SELECT', 'UPDATE', 'DELETE', 'FROM', 'LIMIT', 'OFFSET', 'AND', 'LEFT JOIN', 'WHERE', 'SET',
		'ORDER BY', 'GROUP BY', 'GROUP', 'DISTINCT', 'COUNT', 'COUNT\(\*\)', 'IS', 'NULL', 'IS NULL', 'AS', 'ON', 'INSERT INTO', 'VALUES', 'BEGIN', 'COMMIT', 'CASE', 'WHEN', 'THEN', 'END', 'ELSE', 'IN', 'NOT');
	$words = implode('|', $words);

	$query = preg_replace("#^({$words})(\s)#i", '<font color="blue">$1</font>$2', $query);
	$query = preg_replace("#(\s)({$words})$#i", '$1<font color="blue">$2</font>', $query);
	// replace twice, some words when preceding other are not replaced
	$query = preg_replace("#(\s)({$words})(\s)#i", '$1<font color="blue">$2</font>$3', $query);
	$query = preg_replace("#(\s)({$words})(\s)#i", '$1<font color="blue">$2</font>$3', $query);
	$query = preg_replace("#^($words)$#i", '<font color="blue">$1</font>', $query);

	return $query;
}

?>
<? ui_type('popup'); ?>
<? ui_header(); ?>
<h1>Analiza zapytania</h1>

<div class="row1" style="padding: 0.5em;"><?=nl2br(query_color(html_once($post['query'])));?></div>

<h2>Wynik</h2>

<table class="ls2">
<?
	if (db_driver() == 'mysql') {
		$rows = $rows[0];
	}
?>
<? foreach ($rows as $k => $v):
	if (is_array($v)) {
		if (count($v)>1) {
			trigger_error('count(v)>1', E_USER_ERROR);
		}
		foreach ($v as $k2 => $v2) {
			$k = $k2;
			$v = $v2;
		}
	}
?>
	<tr>
		<th><?=html_once($k);?></th>
		<td><?=html_once($v);?></td>
	</tr>
<? endforeach; ?>
</table>

<p style="margin-bottom: 1em;">
	<a href="javascript:history.go(-1)">&lt;&lt; powrót</a>
</p>
<? ui_footer(); ?>