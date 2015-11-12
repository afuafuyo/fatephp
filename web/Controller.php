<?php
namespace y\web;

class Controller extends ActionEvent {
    
    public $view = null;
    
    public function __construct() {
        $this->view = $this->createObject('y\\web\\View');
    }
    
    /**
     * 渲染文件
     *
     * @param string $view 视图名
     * @param array $params 参数
     * @param boolean $output 是否直接输出
     */
    public function render($view, $params = [], $output = true) {
        $this->trigger(self::EVENT_BEFORE_RENDER);
        $data = $this->view->render($view, $params);
        $this->trigger(self::EVENT_AFTER_RENDER);
        
        if($output) {
            echo $data;
            return;
        }
        
        return $data;
    }
    
}