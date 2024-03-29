<?php
/**
 * @author
 * @license MIT
 */
namespace fate\log;

use Fate;
use fate\core\InvalidConfigException;

/**
 * 日志
 */
final class Logger {

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
     * @var array logged messages
     *
     * Each log message is of the following structure:
     * [
     *   [0] => string:message
     *   [1] => int:level
     *   [2] => float:timestamp
     *   [3] => array:traces
     * ]
     */
    public $messages = [];

    /**
     * @var integer how much call stack information should be logged for each message
     */
    public $traceLevel = 0;

    /**
     * @var integer how many messages should be logged before they are flushed from memory
     */
    public $flushInterval = 10;

    /**
     * @var array the targets class
     */
    public $targets = [];

    /**
     * @var Logger the logger instance
     */
    private static $_logger = null;

    private function __construct() {
        $settings = Fate::$app->log;

        if(null === $settings || !isset($settings['targets'])) {
            throw new InvalidConfigException('The "targets" configuration of the log is missing');
        }

        if(isset($settings['traceLevel'])) {
            $this->traceLevel = $settings['traceLevel'];
        }

        if(isset($settings['flushInterval'])) {
            $this->flushInterval = $settings['flushInterval'];
        }

        foreach($settings['targets'] as $t) {
            if(isset($t['classPath'])) {
                $clazz = Fate::createObject($t['classPath'], [$t]);
                $clazz->on($clazz::EVENT_FLUSH, $clazz);
                $this->targets[] = $clazz;
            }
        }
    }

    /**
     * 获取日志类实例
     */
    public static function getLogger() {
        if(null === self::$_logger) {
            self::$_logger = new self();
        }

        return self::$_logger;
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

    /**
     * Logs a error message
     *
     * @param string $message the message to be logged
     */
    public function error($message) {
        $this->log($message, self::LEVEL_ERROR);
    }

    /**
     * Logs a warning message
     *
     * @param string $message the message to be logged
     */
    public function warning($message) {
        $this->log($message, self::LEVEL_WARNING);
    }

    /**
     * Logs a info message
     *
     * @param string $message the message to be logged
     */
    public function info($message) {
        $this->log($message, self::LEVEL_INFO);
    }

    /**
     * Logs a trace message
     *
     * @param string $message the message to be logged
     */
    public function trace($message) {
        $this->log($message, self::LEVEL_TRACE);
    }

    /**
     * 获取日志级别描述
     *
     * @param integer $level 级别
     */
    public static function getLevelName($level) {
        $name = 'unknown';

        switch($level) {
            case self::LEVEL_ERROR:
                $name = 'error';
                break;
            case self::LEVEL_WARNING:
                $name = 'warning';
                break;
            case self::LEVEL_INFO:
                $name = 'info';
                break;
            case self::LEVEL_TRACE:
                $name = 'trace';
                break;
            default:
                break;
        }

        return $name;
    }

}
