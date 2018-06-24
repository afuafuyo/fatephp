<?php
/**
 * @author
 * @license MIT
 */
namespace fate\web;

use Fate;

class Controller extends \fate\core\Controller {
    
    /**
     * @var string 默认视图类命名空间
     */
    public $defaultView = 'fate\\web\\View';
    
    /**
     * @var Object 视图类实例
     */
    public $view = null;
    
    /**
     * 获取 View 对象
     */
    public function getView() {
        if(null === $this->view) {
            $this->view = Fate::createObject($this->defaultView);
        }
        
        return $this->view;
    }
    
    /**
     * {@inheritDoc}
     */
    public function render($view, $params = [], $output = true) {
        $this->trigger(self::EVENT_BEFORE_RENDER);
        
        $data = $this->getView()->render($view, $params);
        
        $this->trigger(self::EVENT_AFTER_RENDER);
        
        if($output) {
            echo $data;
            return null;
        }
        
        return $data;
    }
    
    /**
     * 输出 ajax 数据
     *
     * @param integer $status
     * @param array $data
     */
    public function ajaxReturn($status, & $data) {
        echo json_encode([
            'status' => $status,
            'data' => $data
        ]);
        exit;
    }
    
}
