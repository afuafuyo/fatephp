<?php
/**
 * @author
 * @license MIT
 */
namespace fate\core;

/**
 * 动作切面
 */
class ActionAspect extends Behavior {

    public $callbacks = null;

    public function __construct() {
        $this->callbacks = [
            Controller::EVENT_BEFORE_ACTION => function($actionEvent) {
                // 如果前一个 valid 为 false 那么本次 filter 不再执行
                if(!$actionEvent->valid) {
                    $this->unListen();
                    return;
                }

                $this->beforeAction($actionEvent);

                if(!$actionEvent->valid) {
                    $this->unListen();
                }
            },
            Controller::EVENT_AFTER_ACTION => function($actionEvent) {
                $this->unListen();

                $this->afterAction($actionEvent);
            }
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function events() {
        return $this->callbacks;
    }

    /**
     * 前置过滤
     */
    public function beforeAction($actionEvent) {}

    /**
     * 后置过滤
     */
    public function afterAction($actionEvent) {}

}
