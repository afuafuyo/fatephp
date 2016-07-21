<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\web;

use Y;

class Controller extends ActionEvent {
    
    /**
     * @var string 默认视图类命名空间
     */
    public $defaultView = 'y\\web\\View';
    
    /**
     * @var Object 视图类实例
     */
    public $view = null;
    
    public function __construct() {
        $this->view = Y::createObject($this->defaultView);
    }
    
    /**
     * 渲染文件
     *
     * @param string $view 视图名
     * @param array $params 参数
     * @param boolean $output 是否直接输出
     * @return string | null
     */
    public function render($view, $params = [], $output = true) {
        $this->trigger(self::EVENT_BEFORE_RENDER);
        $data = $this->view->render($view, $params);
        $this->trigger(self::EVENT_AFTER_RENDER);
        
        if($output) {
            echo $data;
            return null;
        }
        
        return $data;
    }
    
}
