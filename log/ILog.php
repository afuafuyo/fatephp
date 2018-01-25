<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
