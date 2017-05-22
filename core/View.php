<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\core;

/**
 * 视图类
 */
abstract class View extends Object {

    /**
     * @var string 默认视图文件后缀
     */
    public $defaultExtension = '.php';

    /**
     * 渲染文件
     *
     * @param string $view 视图名
     * @param array $params 参数
     */
    public abstract function render($view, $params = []);
    
}
