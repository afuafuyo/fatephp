<?php
/**
 * @author
 * @license MIT
 */
namespace fate\core;

/**
 * 动作过滤器
 *
 * 过滤器会在控制器的动作执行之前执行
 */
class ActionFilter extends Behavior {

    public $callbacks = null;

    public function __construct() {
        $this->callbacks = [
            Controller::EVENT_BEFORE_ACTION => function($actionEvent) {
                if(!$actionEvent->valid) {
                    $this->unListen();
                }

                $this->beforeAction($actionEvent);
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
