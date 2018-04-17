<?php
/**
 * @author yu
 * @license MIT
 */
namespace y\log;

/**
 * 日志接口
 */
interface ILog {
    
    /**
     * flush log
     *
     * @param array $message the message to be logged
     */
    public function flush($messages);
    
}
