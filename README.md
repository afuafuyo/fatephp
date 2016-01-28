#一个自己用的php框架
## 本程序采用单一入口 一个控制器只有一个入口方法的理念开发
##未完成状态

```php
<?php
require(__DIR__ . '/system/Y.php');

$res = (new y\web\Application([
	'id'=>1, 
	'appPath'=> __DIR__ . '/app',
	'modules' => [
		'reg' => 'app\modules\reg'
	],
	'db' => [
		'main' => [
			'dsn' => 'mysql:host=localhost;dbname=binguan',
			'username' => 'root',
			'password' => 'root',
			'charset' => 'utf8',
			'prefix'=> ''
		]
	],
    'cache' => [
        'file' => [
            'class' => 'y\cache\FileCache'
        ]
    ]
	
]))->run();
```

```php
<?php
namespace app\controllers;

use y\db\DbFactory;

class IndexController extends \y\web\Controller {
	
	public function execute($request) {
        //$this->render('index');
        
        //$db = DbFactory::instance('main');
        //$db->on($db::EVENT_BEFORE_QUERY, function(){
        //    echo 'beforeQuery<br>';
        //});
        
        //$sql = 'SELECT * FROM users';
        //foreach ($db->querySql($sql) as $row) {
        //   var_dump($row);
        //}
        
        //$res = $db->table('users')->where('1=1')->orderBy('id desc')->getAll();
        //$res2 = $db->table('users')->orderBy('id desc')->limit('2')->getAll();
        
        //$data = [
        //    ['name'=>'zhangsan', 'age'=>20, 'b'=>'ajdsfafj'],
        //    ['name'=>'wangwu', 'age'=>'20', 'b'=>'111'],
        //    ['name'=>'lisu', 'age'=>20, 'b'=>'ggggg']
        //];
        //$c = $db->table('users')->insert($data);
        
        //$c = $db->table('users')->where('id=1')->delete();
        
        //$data = ['name'=>'abc', 'age'=>1];
        //$db->table('users')->where('id=1')->update($data);
        
        //echo $db->table('users')->count();
        
        // 缓存
        $c = CacheFactory::instance('file');
        $c->set('key', '123123');
        echo $c->get('key');
        $c->delete('key');
	}
}
```
