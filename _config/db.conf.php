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
$config['DB']['dev']['USER']     = 'lazybones';
$config['DB']['dev']['PASSWORD'] = 'lazybones1234';
$config['DB']['dev']['DBNAME']   = 'lazybones';
$config['DB']['dev']['ENCODING'] = 'utf8';
$config['DB']['dev']['DRIVER']   = 'MySQL';

//正式上線模式
$config['DB']['online']['HOST']     = 'localhost';
$config['DB']['online']['USER']     = 'DB_USER';
$config['DB']['online']['PASSWORD'] = 'PASSWORD';
$config['DB']['online']['ENCODING'] = 'utf8';
$config['DB']['online']['DRIVER']   = 'MySQL';

/*++++ 或更多 ++++
$config['DB']['????']['HOST'] = 'localhost';
$config['DB']['????']['USER'] = 'DB_USER';
$config['DB']['????']['PASSWORD'] = 'PASSWORD';
$config['DB']['????']['ENCODING'] = 'utf8';
$config['DB']['????']['DRIVER'] = 'MySQL';
*/
