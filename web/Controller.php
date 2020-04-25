<?php
/**
 * @author
 * @license MIT
 */
namespace fate\web;

use Fate;

/**
 * Web 控制器
 */
class Controller extends \fate\core\Controller {

    use ControllerTrait;

    /**
     * @property string 默认视图类命名空间
     */
    public $defaultView = 'fate\\web\\View';

    /**
     * @property Object 视图类实例
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
     * 设置 view 对象
     *
     * @param any $view
     */
    public function setView($view) {
        $this->view = $view;
    }

    /**
     * {@inheritdoc}
     */
    public function render($view, $parameters = [], $output = true) {
        $this->trigger(self::EVENT_BEFORE_RENDER);

        $data = $this->getView()->render($view, $parameters);

        $this->trigger(self::EVENT_AFTER_RENDER);

        if($output) {
            echo $data;
            return null;
        }

        return $data;
    }

}
