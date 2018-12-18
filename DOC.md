# 入门

### 安装程序

可以通过两种方式安装 fatephp

1. 使用 composer
2. 下载源码

###### 使用 composer 安装

`composer require afuafuyo/fatephp`

###### 下载源码

通过 [GitHub](https://github.com/afuafuyo/fatephp) 下载源码放置到任意可访问位置即可

# 第一次运行程序

以下示例包含三部分

- 入口文件 `index.php`
- 控制器文件 `IndexController.php`
- 模板文件 `index.php`

1. 在 d:/www 目录（或其他位置）创建如下项目结构

```text
MY_FIRST_APP
|
|- index.php
|
|- app
|   |
|   |-- controllers
|   |   |
|   |   |-- index
|   |       |
|   |       |-- IndexController.php
|   |   
|   |
|   |-- views
|       |
|       |-- index
|           |
|           |-- index.php
```

2. 入口文件 index.php 文件内容如下

```php
<?php
define('FATE_DEBUG', true);
require(__DIR__ . '/somepath/fatephp/Fate.php');

// init
$res = (new fate\web\Application([
    'id'=>'main',
    'appPath'=> __DIR__ . '/app'
]))->run();

if(is_array($res)) {
    echo json_encode($res);
}
```

3. 控制器文件 IndexController.php 文件内容如下

```php
<?php
namespace app\controllers\index;

class IndexController extends \fate\web\Controller {
    public function run() {
        $this->render('index');
    }
}
```

4. 模板文件 index.php 文件内容如下

```text
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>hello</title>
</head>
<body>
<?php echo 'hello fatephp'; ?>
</body>
</html>
```

5. 将 MY_FIRST_APP 部署到服务器，比如 nginx

```shell
server {
    listen       8080;
    server_name  localhost;

    charset utf-8;
    index  index.html index.php;
    root d:/www/MY_FIRST_APP;
    
    location ~ \.php$ {
        fastcgi_param YUBA_ENVIRONMENT 'development';
    
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }
}
```

6. 访问

`http://localhost:8080/` 就可以看到结果

# 应用结构

一个比较完整的应用目录结构如下

```text
PROJECT_NAME
|
|- index.php
|
|- app 项目目录
|   |
|   |-- controllers 普通控制器目录
|       |
|       |-- user 用户目录
|       |   |
|       |   |-- IndexController.php  - host:port/user/index 可以访问到该类
|       |   |-- OtherController.php  - host:port/user/other 可以访问到该类
|       |
|       |-- goods 商品组目录
|       |   |
|       |   |-- IndexController.php  - host:port/goods/index 可以访问到该类
|       |   |-- OtherController.php  - host:port/goods/other 可以访问到该类
|       |
|       |
|   -- views 普通控制器模板目录
|       |
|       |-- user 用户组模板 对应上面用户组
|       |   |
|       |   |-- index.php
|       |   |-- other.php
|       |
|       |
|   -- goods 商品组模板
|       |   |
|       |   |-- index.php
|       |   |-- other.php
|       |
|       |
|   -- modules 模块
|       |
|       |-- reg
|       |   |
|       |   |-- controllers 模块控制器目录 其下无子目录
|       |   |   |
|       |   |   |-- IndexController.php
|       |   |
|       |   |-- views 模块模板目录
|       |   |   |
|       |   |   |-- index.php
|       |   |
|       |   |-- 其他目录
|       |
|       |
|   -- runtime 缓存目录
|
```

### 入口脚本 index.php

入口脚本是应用启动流程中的第一环，一个应用只有一个入口脚本。入口脚本包含启动脚本，程序启动后就会监听客户端的连接

入口脚本主要完成以下工作

- 加载应用配置
- 启动应用
- 注册各种需要组件

```php
<?php
require(__DIR__ . '/somepath/fatephp/Fate.php');

$res = (new fate\web\Application([
    'id'=>1, 
    'appPath'=> __DIR__ . '/app',
    
    // 注册模块
    'modules' => [
        'reg' => 'app\\modules\\reg'
    ],
    
    // 数据库配置
    'db' => [
        'main' => [
            'dsn' => 'mysql:host=localhost;dbname=xxx',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
            'prefix'=> ''
        ]
    ],
    
    // 缓存配置
    'cache' => [
        'file' => [
            'classPath' => 'fate\cache\file\Cache'
        ]
    ],
    
    // 日志配置
    'log' => [
        'targets' => [
            'file' => [
                'classPath' => 'fate\log\file\Log',
                'maxFileSize' => 10240  // KB
            ],

        ]
    ]

]))->run();
```

### 应用

在这里就是指 web 应用，框架表示为 `fate\web\Application` 的实例

### 应用属性

在入口文件中可以传入各种参数，这些参数最终会被赋值到应用对象上

###### 必要属性

- `id` 该属性用来标识唯一应用
- `appPath` 该属性用于指明应用所在的目录

###### 重要属性

- `interceptAll` 用于拦截所有路由

一下配置会使得所有请求都交由 app\Deny 类处理

```php
[
    'interceptAll' = 'app\Deny'
]
```

- `routesMap` 用于自定义路由

以下配置使得， account 路由和 u 路由使用 `app\controllers\user\IndexController` 类做处理， account 路由同时还向 `app\controllers\user\IndexController` 类传入了一个参数

```
[
    'routesMap' = [
        'u' => 'app\controllers\user\IndexController',
        'account' => [
            'classPath' => 'app\controllers\user\IndexController',
            'property' => 'value'
        ]
    ]
]
```

###### 自定义属性

其他在入口文件中传入的参数都会作为自定义参数传入应用对象

# 应用控制器

控制器是 MVC 模式中的一部分，是继承 `fate\web\Controller` 类的对象，负责处理请求和生成响应

### 动作

控制器由动作组成，它是执行终端用户请求的最基础的单元。在 fatephp 中，一个控制器有且只有一个入口动作叫做 `run`

```php
namespace app\controllers\index;

class IndexController extends \fate\web\Controller {
    // 动作
    public function run() {
        $this->render('index');
    }
}
```

# 路由与控制器

一般一个路由对应一个控制器。路由格式如下：

`[route_prefix]/[controllerId]`

如果属于模块下的控制器 那么路由格式如下

`[moduleId]/[controllerId]`

如果用户的请求地址为 `http://hostname/index` 会执行 `index` 控制器的 `run` 入口动作

如果用户的请求地址为 `http://hostname/bbs/index` 会优先执行 `bbs` 模块的 `index` 控制器的 `run` 入口动作，如果未找到就执行普通控制器的 `bbs` 目录下的 `index` 控制器的 `run` 入口动作

控制器查找顺序：优先查找模块下的控制器。`模块控制器 --> 普通控制器`

# 模型

模型是 MVC 模式中的一部分，是代表业务数据的对象

# 视图

视图是 MVC 模式中的一部分，它用于给终端用户展示页面

# 模块

模块是独立的软件单元，由模型、视图、控制器和其他组件组成。终端用户可以访问在应用主体中已注册的模块的控制器， fatephp 在解析路由时优先查找模块中的控制器

==注意：和普通项目目录不同的是，模块中的控制器和视图没有子目录==

# 别名系统

为了方便类的管理，实现自动加载，初始化等。 fatephp 提供了一套别名系统

别名是一个以 `@` 符号开头的字符串，每一个别名对应一个真实的物理路径。

### 系统内置别名

- `@fate` 指向 fatephp 目录
- `@app` 项目目录，由入口文件 appPath 属性指定
- `@runtime` 缓存目录，默认指向 `@app/runtime`

### 自定义别名

用户可以自定义别名

```
// 注册别名
Fate::setPathAlias('@lib', '/home/www/library');

// 加载并创建 /home/www/library/MyClass 类
$obj = Fate::createObject('lib\\MyClass');
```

# 数据库操作

```php
$db = Db::instance('main');

// 1. 使用 sql 操作数据库

// 增加
$db->prepareSql('INSERT INTO xxx(id, age) VALUES(1, 20)')->execute();
$insertId = $db->getLastInsertId('id');

// 删除
$db->prepareSql('DELETE FROM xxx WHERE id = 1')->execute();

// 修改
$db->prepareSql('UPDATE xxx SET age = 22 WHERE id = 1')->execute();

// 查询所有
$data = $db->prepareSql('SELECT age FROM xxx')->queryAll();

// 查询一条
$data = $db->prepareSql('SELECT age FROM xxx WHERE id = 1')->queryOne();

// 查询单列
$n = $db->prepareSql('SELECT count(id) FROM xxx')->queryColumn();


// 2. 使用预处理语句

// 增加
$db->prepareStatement('INSERT INTO xxx(id, age) VALUES(:id, :age)')
    ->bindValues([':id' => 1, ':age' => 20])->execute();

$db->prepareStatement('INSERT INTO xxx(id, age) VALUES(?, ?)')
    ->bindValues([1, 20])->execute();

// 删除
$db->prepareStatement('DELETE FROM xxx WHERE id = ?')->bindValues([1])->execute();

// 修改
$db->prepareStatement('UPDATE xxx SET age = ? WHERE id = ?')->bindValues([22, 1])->execute();

// 查询一条
$data = $db->prepareStatement('SELECT age FROM xxx WHERE id = :id')->bindValue(':id', 1)->queryOne();


// 3. 使用查询生成器 只能执行查询操作

// 查询所有
$data = $db->createQuery()->select('id, age')->from('xxx')->getAll();

// 查询一条
$data = $db->createQuery()->select('id, age')->from('xxx')->where('id = ?', [1])->getOne();
$data = $db->createQuery()->select('id, age')->from('xxx')->where('id = :id', [':id' => 1])->getOne();

// 查询单列
$data = $db->createQuery()->select('age')->from('xxx')->where('id = 1')->getColumn();

// 统计
$n = $db->createQuery()->from('xxx')->where('id > 2')->count('id');
```
