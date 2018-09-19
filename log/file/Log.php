<?php
/**
 * @author
 * @license MIT
 */
namespace fate\log\file;

use Fate;
use fate\log\Logger;
use fate\helpers\FileHelper;

/**
 * 文件日志
 *
 * 'log' => [
 *      'targets' => [
 *          'file' => [
 *              'classPath' => 'fate\log\file\Target',
 *              'logPath' => '@runtime/logs',
 *              'logFile' => 'system.log'
 *          ]
 *      ],
 *      flushInterval: 10
 * ]
 *
 */
class Log extends \fate\log\AbstractLog {
    
    /**
     * @property string log file path
     */
    public $logPath = '@runtime/logs';
    
    /**
     * @property string log file name
     */
    public $logFile = 'system.log';
    
    public function __construct($config) {
        $this->logPath = isset($config['logPath'])
            ? $config['logPath']
            : Fate::getPathAlias($this->logPath);
        
        if(isset($config['logFile'])) {
            $this->logFile = $config['logFile'];
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function flush($messages) {
        $msg = $this->formatMessage($messages);
        $file = $this->logPath . DIRECTORY_SEPARATOR . $this->logFile;
        
        if(!is_dir($this->logPath)) {
            FileHelper::createDirectory($this->logPath);
        }
        
        if(false === ($fp = @fopen($file, 'a'))) {
            return;
        }
        
        @flock($fp, LOCK_EX);
        @fwrite($fp, $msg);
        @flock($fp, LOCK_UN);
        @fclose($fp);
    }
    
    /**
     * 格式化内容
     *
     * @param array $messages 内容
     */
    public function formatMessage(& $messages) {
        $msg = '';
        
        for($i=0, $len=count($messages); $i<$len; $i++) {
            $msg .= date('Y-m-d H:i:s', $messages[$i][2])
                . ' [ '
                . Logger::getLevelName($messages[$i][1])
                . ' ] '
                . $messages[$i][0] . "\n";
        }
        
        return $msg;
    }
    
}
