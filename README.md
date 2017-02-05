# 小型 php 框架

###### php 版本

* php5.4+
* php7

###### 本程序特点

* 采用控制器单一入口执行程序 解决单一文件过大不好维护问题
* 以控制器目录分组的方式组织代码 结构清晰 支持无限级子目录 (模块控制器除外)

###### 系统内置别名

* @y  系统目录
* @app  项目目录 由 appPath 指定
* @runtime  缓存目录 指向 @app/runtime

###### 项目目录结构

<pre>
|- index.php
|
|- public 目录
|
|- app 项目目录
|  |
|  |-- controllers 普通控制器目录
|      |
|      |-- user 用户组目录
|      |   |
|      |   |-- IndexController.php 用户组下的控制器
|      |   |-- OtherController.php
|      |
|      |-- goods 商品组目录
|      |   |
|      |   |-- IndexController.php
|      |   |-- OtherController.php
|      |
|   -- views 普通控制器模板目录
|      |
|      |-- user 用户组模板 对应上面用户组
|      |   |
|      |   |-- index.php
|      |   |-- other.php
|      |
|   -- goods 商品组模板
|      |   |
|      |   |-- index.php
|      |   |-- other.php
|      |
|   -- modules 模块
|      |
|      |-- reg
|      |   |
|      |   |-- controllers 模块控制器目录 其下无子目录
|      |   |   |
|      |   |   |-- IndexController.php
|      |   |
|      |   |-- views 模块模板目录
|      |   |   |
|      |   |   |-- index.php
|      |   |
|      |   |-- 其他目录
|      |
|   -- runtime 缓存目录
|
</pre>

```php
路由格式

/index.php?r=[:route_prefix|:moduleId]/[:controllerId]
```

```php
index.php

<?php
require(__DIR__ . '/system/Y.php');

$res = (new y\web\Application([
	'id'=>1, 
	'appPath'=> __DIR__ . '/app',
	'modules' => [
		'reg' => 'app\\modules\\reg'
	],
    'routes' => [
        // 把下面这个模式 路由到 IndexController 并且参数的键定义为 id
        '/post/(\d+)' => ['controllerId' => 'index', 'params' => ['key' => 'id', 'segment'=>1]],
        // 把下面这个模式 路由到 user 目录下的 IndexController 并有两个参数 id uid
        '/show/(\d+)/(\d+)' => ['prefix' => 'user', 'controllerId'=>'index', 'params' => ['key' => ['id', 'uid'], 'segment'=>[1, 2]]]
    ],
	'db' => [
		'main' => [
			'dsn' => 'mysql:host=localhost;dbname=xxx',
			'username' => 'root',
			'password' => 'root',
			'charset' => 'utf8',
			'prefix'=> ''
		]
	],
    'cache' => [
        'file' => [
            'class' => 'y\cache\file\Cache'
        ]
    ],
    'log' => [
        'targets' => [
            'file' => [
                'class' => 'y\log\file\Target'
            ]
        ]
    ]
	
]))->run();
```

```php
app\controllers\index\IndexController.php

<?php
namespace app\controllers\index;

use y\db\DbFactory;
use y\cache\CacheFactory;
use y\log\Logger;

class IndexController extends \y\web\Controller {
	
    // 控制器单入口
	public function run() {
        $this->render('index', []);
        
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
        //    ['name'=>'zhangsan', 'age'=>20, 'b'=>'xxx'],
        //    ['name'=>'wangwu', 'age'=>'20', 'b'=>'xxx'],
        //    ['name'=>'lisu', 'age'=>20, 'b'=>'xxx']
        //];
        // $data = ['name'=>'zhangsan', 'age'=>20];
        //$c = $db->table('users')->insert($data);
        
        //$c = $db->table('users')->where('id=1')->delete();
        
        //$data = ['name'=>'abc', 'age'=>1];
        //$db->table('users')->where('id=1')->update($data);
        
        //echo $db->table('users')->count();
        
        //$data = $db->table('users')->getAll();
        //$data = $db->fields('id,name')->table('users')->where('id=1')->getOne();
        //var_dump($data);
        
        //$c = CacheFactory::instance('file');
        //$c->set('key', 'the value');
        //echo $c->get('key');
        //$c->delete('key');
        
        //$log = Logger::getLogger();
        //$log->trace('trace info');
        //$log->error('error info');
        //$log->flush();  // 手动 flush log
	}
}
```

###### 系统扩展规范

系统路径除了类名外一律小写

* 数据库

目前只提供了 mysql 支持 扩展参照 mysql 的实现 需要继承 \y\db\ImplDb 类并实现其中的方法

* 缓存

目前提供了 file 和 memcache 缓存 扩展参照这两个实现 需要继承 \y\cache\ImplCache 类并实现其中的方法

* 日志

目前提供了 file 日志 扩展参照 file 日志的实现 需要继承 \y\log\ImplTarget 类并实现其中的方法