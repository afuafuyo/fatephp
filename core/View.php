<?php
/**
 * @author
 * @license MIT
 */
namespace fate\core;

use Fate;

/**
 * 视图类
 */
abstract class View extends Object {
    
    /**
     * @property string 默认视图文件后缀
     */
    public $defaultExtension = '.php';
    
    /**
     * 查找视图文件路径
     *
     * @param string $view 视图名
     * @return string
     */
    public function findViewFile($view) {
        if('@' === $view[0]) {
            return Fate::getPathAlias($view) . $this->defaultExtension;
        }
        
        $app = Fate::$app;
        
        // 模块无子目录 普通控制器有子目录
        // 注意转换 namespace path 为目录路径
        if('' !== $app->moduleId) {
            return Fate::namespaceToNormal($app->modules[$app->moduleId], '')
                .'/views/'
                . $view . $this->defaultExtension;
        }
        
        return Fate::namespaceToNormal('app', '')
            . '/views/'
            . str_replace('\\', '/', $app->viewPath)
            . '/' . $view . $this->defaultExtension;
    }
    
    /**
     * 渲染视图文件
     *
     * @param string $view 视图名
     * @param array $params 参数
     */
    public function render($view, $params = []) {
        $file = $this->findViewFile($view);
        
        return $this->renderFile($file, $params);
    }
    
    /**
     * 渲染文件
     *
     * @param string $file 文件路径
     * @param array $params 参数
     */
    public abstract function renderFile($file, $params);
    
}
