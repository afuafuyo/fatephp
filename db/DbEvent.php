<?php
namespace y\db;

/**
 * 数据库事件
 */
class DbEvent extends \y\core\Event {

    const EVENT_BEFORE_QUERY = 'beforeQuery';
    const EVENT_AFTER_QUERY = 'afterQuery';

}