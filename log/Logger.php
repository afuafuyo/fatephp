<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\log;

use Y;

/**
 * 日志类
 *
 *  'log' => [
 *      'targets' => [
 *          'file' => [
 *              'class' => 'y\log\file\Target',
 *              'levels' => ['trace', 'info']
 *          ],
 *          ...
 *      ]
 *  ]
 *
 */
class Logger extends \y\core\Object {
    
    /**
     * Error message level
     */
    const LEVEL_ERROR = 0x01;
    
    /**
     * Warning message level
     */
    const LEVEL_WARNING = 0x02;
    
    /**
     * Informational message level
     */
    const LEVEL_INFO = 0x04;
    
    /**
     * Tracing message level
     */
    const LEVEL_TRACE = 0x08;
    
    /**
     * Profiling message level
     */
    const LEVEL_PROFILE = 0x40;
    
    /**
     * @var integer how much call stack information should be logged for each message
     */
    public $traceLevel = 0;
    
    /**
     * @var array logged messages
     *
     * Each log message is of the following structure:
     * <pre>
     * [
     *   [0] => string:message
     *   [1] => int:level
     *   [3] => float:timestamp
     *   [4] => array:traces
     * ]
     * </pre>
     */
    public $messages = [];
    
    /**
     * @var integer how many messages should be logged before they are flushed from memory
     */
    public $flushInterval = 10;
    
    /**
     * @var array the targets class
     */
    public $targets = [];
    
    public function __construct() {
        if(!isset(Y::$app->log['targets'])) {
            throw new InvalidConfigException('No log targets found');
        }
        
        foreach(Y::$app->log['targets'] as $config) {
            if(isset($config['class'])) {
                $clazz = $this->createObject($config['class']);
                $clazz->on($clazz::EVENT_FLUSH, $clazz->flush);
                
                $this->targets[] = $clazz;
            }
        }
    }
    
    /**
     * 记录日志
     *
     * @param string $message 消息
     * @param int $level 日志级别
     */
    public function log($message, $level) {
        $time = microtime(true);
        $traces = [];
        
        if($this->traceLevel > 0) {
            $count = 0;
            $ts = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            array_pop($ts);  // 去掉最后一个没太大作用的信息
            
            foreach($ts as $trace) {
                if(isset($trace['file'], $trace['line'])) {
                    unset($trace['object'], $trace['args']);
                    $traces[] = $trace;
                    if(++$count >= $this->traceLevel) {
                        break;
                    }
                }
            }
        }
        
        $this->messages[] = [$message, $level, $time, $traces];
        if($this->flushInterval > 0 && count($this->messages) >= $this->flushInterval) {
            $this->flush();
        }
    }
    
    /**
     * 清空 log 并写入目的地
     */
    public function flush() {
        $messages = $this->messages;
        $this->messages = [];
        
        $target = null;
        for($i=0,$len=count($this->targets); $i<$len; $i++) {
            $target = $this->targets[$i];
            $target->trigger($target::EVENT_FLUSH, $messages);
        }
    }
    
}