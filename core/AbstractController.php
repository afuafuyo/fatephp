<?php
/**
 * @author
 * @license MIT
 */
namespace fate\core;

use Fate;

/**
 * 核心控制器类
 */
abstract class AbstractController extends Component {

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
     * the filter collection
     */
    public $filterChain = FilterFactory::createFilterChain($this);

    /**
     * 声明过滤器列表
     *
     * ```
     * [
     *      filterInstance,
     *      'filterClassPath'
     *      ['classPath' => 'filterClassPath', otherProps => xxx]
     * ]
     * ```
     *
     */
    public function filters() {
        return null;
    }

    /**
     * 控制器方法执行前
     */
    public function beforeAction($actionEvent) {
        $this->trigger(self::EVENT_BEFORE_ACTION, $actionEvent);
    }

    /**
     * 控制器方法执行后
     */
    public function afterAction($actionEvent) {
        $this->trigger(self::EVENT_AFTER_ACTION, $actionEvent);
    }

    /**
     * 执行控制器
     */
    public function runControllerAction() {
        $actionEvent = new ActionEvent();

        $this->beforeAction($actionEvent);
        if(false === $actionEvent->valid) {
            return null;
        }

        $this->filterChain->doFilter();
        $this->afterAction($actionEvent);

        return $actionEvent->data;
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
