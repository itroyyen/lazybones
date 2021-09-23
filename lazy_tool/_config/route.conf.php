<?php
// 首頁
$route['*']['/'] = array('Lazy');


$route['*']['/create_model/*'] = array('Lazy','CreateModel');