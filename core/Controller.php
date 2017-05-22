<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\core;

use Y;

class Controller {
    
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
    
}
