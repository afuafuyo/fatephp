<?php
/**
 * @author
 * @license MIT
 */
namespace fate\core;

/**
 * 核心控制器类
 */
abstract class Controller extends Component {

    /**
     * @var string 事件名
     */
    const EVENT_BEFORE_ACTION = 'beforeAction';

    /**
     * @var string 事件名
     */
    const EVENT_AFTER_ACTION = 'afterAction';

    /**
     * @var string 事件名
     */
    const EVENT_BEFORE_RENDER = 'beforeRender';

    /**
     * @var string 事件名
     */
    const EVENT_AFTER_RENDER = 'afterRender';

    /**
     * 控制器方法执行前
     */
    public function beforeAction() {
        $event = new ActionEvent();

        $this->trigger(self::EVENT_BEFORE_ACTION, $event);

        return $event->valid;
    }

    /**
     * 控制器方法执行后
     */
    public function afterAction() {
        $this->trigger(self::EVENT_AFTER_ACTION);
    }

    /**
     * 执行控制器的方法
     *
     * @param {Object} request
     * @param {Object} response
     */
    public function runControllerAction() {
        if(true !== $this->beforeAction()) {
            return;
        }

        $data = $this->run();
        $this->afterAction();

        return $data;
    }

    /**
     * 渲染文件
     *
     * @param string $view 视图名
     * @param array $parameters 参数
     * @param boolean $output 是否直接输出
     * @return string | null
     */
    public abstract function render($view, $parameters = [], $output = true);

}
