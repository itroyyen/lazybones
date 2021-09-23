<?php
/**************************************************************************
 * 資料庫啟用的連線模式
 **************************************************************************/
$config['DB_MOD'] = 'dev';

/**************************************************************************
 * 資料表附加前綴，將作用於所有模型的資料表名稱 e.g. LAZY_
 **************************************************************************/
$config['TABLE_PREFIX'] = '';

/**************************************************************************
 * 資料庫連線設定
 **************************************************************************/
//開發模式
$config['DB']['dev']['HOST']     = 'localhost';
$config['DB']['dev']['USER']     = 'ctust_meta';
$config['DB']['dev']['PASSWORD'] = 'ctust_meta74427';
$config['DB']['dev']['DBNAME']   = 'ctust_meta';
$config['DB']['dev']['ENCODING'] = 'utf8';
$config['DB']['dev']['DRIVER']   = 'MySQL'; // 目前僅支援 MySQL

//正式上線模式
$config['DB']['online']['HOST']     = 'localhost';
$config['DB']['online']['USER']     = 'DB_USER';
$config['DB']['online']['PASSWORD'] = 'PASSWORD';
$config['DB']['online']['DBNAME']   = 'DB_NAME';
$config['DB']['online']['ENCODING'] = 'utf8';
$config['DB']['online']['DRIVER']   = 'MySQL';

/*++++ 或更多 ++++
$config['DB']['other']['HOST']     = 'localhost';
$config['DB']['other']['USER']     = 'DB_USER';
$config['DB']['other']['PASSWORD'] = 'PASSWORD';
$config['DB']['other']['DBNAME']   = 'DB_NAME';
$config['DB']['other']['ENCODING'] = 'utf8';
$config['DB']['other']['DRIVER']   = 'MySQL';
*/
