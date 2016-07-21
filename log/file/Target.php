<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\log\file;

use Y;
use y\log\Logger;
use y\helpers\FileHelper;

class Target extends \y\log\ImplTarget {
    
    /**
     * @var string log file path
     */
    public $logpath = null;
    
    /**
     * @var string log file name
     */
    public $logfile = null;
    
    public function __construct($config) {
        $this->logpath = isset($config['logpath']) ?
            Y::getPathAlias($config['logpath']) :
            Y::$app->getRuntimePath() . '/logs';
        
        $this->logpath = rtrim($this->logpath, '/') . '/';
        
        $this->logfile = date('Y-m-d') . '.log';
        
        if(!is_dir($this->logpath)) {
            FileHelper::createDirectory($this->logpath);
        }
    }
    
    public function flush($messages) {
        $msg = $this->formatMessage($messages);
        $file = $this->logpath . $this->logfile;
        
        if(($fp = @fopen($file, 'a')) === false) {
            return;
        }
        
        @flock($fp, LOCK_EX);
        @fwrite($fp, $msg);
        @flock($fp, LOCK_UN);
        @fclose($fp);
    }
    
    public function formatMessage(&$messages) {
        $msg = '';
        for($i=0, $len=count($messages); $i<$len; $i++) {
            $msg .= date('Y-m-d H:i:s', $messages[$i][2]) . ' -- ' . Logger::getLevelName($messages[$i][1]) . ' -- ' . $messages[$i][0] . "\n";
        }
        
        return $msg;
    }
}