<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\log\file;

use Y;
use y\log\Logger;
use y\helpers\FileHelper;

/**
 * 文件日志
 *
 * 'log' => [
 *      'targets' => [
 *          'file' => [
 *              'class' => 'y\log\file\Target',
 *              ...
 *          ]
 *      ]
 * ]
 *
 */
class Target extends \y\log\ImplTarget {
    
    public $logPath = '@runtime/logs';

    public $logFile = null;
    
    public function __construct($config) {
        $this->logPath = isset($config['logPath']) ? Y::getPathAlias($config['logPath']) :
            Y::getPathAlias($this->logPath);
        
        $this->logFile = $this->generateTimeLogFile();
        
        if(!is_dir($this->logPath)) {
            FileHelper::createDirectory($this->logPath);
        }
    }
    
    public function flush($messages) {
        $msg = $this->formatMessage($messages);
        $file = $this->logPath . '/' . $this->logFile;
        
        if(($fp = @fopen($file, 'a')) === false) {
            return;
        }
        
        @flock($fp, LOCK_EX);
        @fwrite($fp, $msg);
        @flock($fp, LOCK_UN);
        @fclose($fp);
    }
    
    public function generateTimeLogFile($format = 'Y-m-d') {
        return date($format) . '.log';
    }
    
    public function formatMessage(& $messages) {
        $msg = '';
        for($i=0, $len=count($messages); $i<$len; $i++) {
            $msg .= date('Y-m-d H:i:s', $messages[$i][2]) . ' -- '
                . Logger::getLevelName($messages[$i][1]) . ' -- '
                . $messages[$i][0] . "\n";
        }
        
        return $msg;
    }
}
