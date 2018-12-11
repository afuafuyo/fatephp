# 小型 php 框架 非线程安全

###### 与 [YNode](https://github.com/afuafuyo/ynode) 保持统一架构的 php 框架

###### php 版本

+ php5.4+
+ php7

###### 变更

+ v3.0.1 重构部分代码
+ v2.1.0 重构数据库部分
+ v2.0.0 修改框架名 yphp 更名为 fatephp 所有类路径 有 y\xxx 变为 fate\xxx
+ v1.3.0 更改 y\helpers\LinkedQueue 到 y\util\LinkedQueue
+ v1.3.0 修改 y\web\Request::getParam() 为 y\web\Request::getQueryString()
+ v1.3.0 修改 y\web\Request::postParam() 为 y\web\Request::getParameter()
+ v1.3.0 修改 y\web\Controller::view 的获取方式 $this->view 为 $this->getView()

###### 本程序特点

+ 采用控制器单一入口执行程序 解决单一文件过大不好维护问题
+ 以控制器目录分组的方式组织代码 结构清晰 支持无限级子目录 (模块控制器除外)

###### 系统内置别名

+ @fate  系统目录
+ @app  项目目录 由 appPath 指定
+ @runtime  缓存目录 指向 @app/runtime

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

/index.php?r=[route_prefix|moduleId]/[controllerId]
```

```php
index.php

<?php
require(__DIR__ . '/system/Fate.php');

$res = (new fate\web\Application([
    'id'=>1, 
    'appPath'=> __DIR__ . '/app',
    'modules' => [
        'reg' => 'app\\modules\\reg'
    ],
    'db' => [
        'main' => [
            'dsn' => 'mysql:host=localhost;dbname=xxx',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8'
        ]
    ],
    'cache' => [
        'file' => [
            'class' => 'fate\cache\file\Cache'
        ]
    ],
    'log' => [
        'targets' => [
            'file' => [
                'class' => 'fate\log\file\Log'
            ]
        ]
    ]

]))->run();
```

###### install

1. use composer

```shell
composer require afuafuyo/fatephp
```

2. github source

Download source code from github and place it under the project

###### Db

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
