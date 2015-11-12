<?php
namespace y\web;

/**
 * Action 事件
 */
class ActionEvent extends \y\core\Event {

    const EVENT_BEFORE_RENDER = 'beforeRender';
    const EVENT_AFTER_RENDER = 'afterRender';

}