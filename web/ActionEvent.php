<?php
namespace y\web;

/**
 * Action 事件
 */
class ActionEvent extends \y\core\Event {

    const EVENT_BEFORE_ACTION = 'beforeAction';
    const EVENT_AFTER_ACTION = 'afterAction';

}