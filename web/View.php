<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\web;

use Y;
use y\core\FileNotFoundException;

/**
 * 视图类
 */
class View extends \y\core\Object {
    
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
    public function render($view, $params = []) {
        $file = $this->findViewFile($view);
        
        return $this->renderFile($file, $params);
    }
    
    /**
     * 得到视图所在路径
     *
     * @return string 视图路径
     */
    public function findViewFile($view) {
        $app = Y::$app;
        $path = '';
        
        if('' !== $app->moduleId) {
            $path = Y::namespaceTranslate($app->modules[$app->moduleId], '');
            
        } else {
            $path = Y::namespaceTranslate('app', '');
        }
        
        return $path . '/views/' . strtolower($app->controllerId) . '/' . $view . $this->defaultExtension;
    }
    
    /**
     * 渲染 php 文件
     *
     * @param string $file 文件
     * @param array $params 参数
     */
    public function renderFile($file, $params) {
        if(!is_file($file)) {
            throw new FileNotFoundException('The view file: ' . $file . ' not found.');
        }
        
        ob_start();
        ob_implicit_flush(false);
        extract($params, EXTR_OVERWRITE);
        include($file);

        return ob_get_clean();
    }
}