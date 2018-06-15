<?php
/**
 * @author
 * @license MIT
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
class Log extends \y\log\ImplLog {
    
    /**
     * @var string log file extension
     */
    public $fileExtension = '.log';
    
    /**
     * @var string log file path
     */
    public $logPath = '@runtime/logs';

    /**
     * @var string log file name
     */
    public $logFile = null;
    
    public function __construct($config) {
        if(isset($config['fileExtension'])) {
            $this->fileExtension = $config['fileExtension'];
        }
        
        $this->logPath = isset($config['logPath'])
            ? $config['logPath']
            : Y::getPathAlias($this->logPath);
        
        $this->logFile = $this->generateTimeLogFile();
    }
    
    /**
     * {@inheritDoc}
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
     * 生成日志文件名
     *
     * @param string $format 格式
     */
    public function generateTimeLogFile($format = 'Y-m-d') {
        return date($format) . $this->fileExtension;
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
                . ' -- '
                . Logger::getLevelName($messages[$i][1])
                . ' -- '
                . $messages[$i][0] . "\n";
        }
        
        return $msg;
    }
    
}
