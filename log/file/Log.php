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
 *              'logFile' => 'system.log',
 *              'maxFileSize' => 10240
 *          ]
 *      ],
 *      flushInterval: 10
 * ]
 *
 */
class Log extends \fate\log\AbstractLog {

    /**
     * @var string absolute path of log file. default at runtime directory of the application
     */
    public $logPath = '@runtime/logs';

    /**
     * @var string log file name
     */
    public $logFile = 'system.log';

    /**
     * @var integer maxFileSize maximum log file size in KB
     */
    public $maxFileSize = 10240;

    public function __construct($config) {
        $this->logPath = isset($config['logPath'])
            ? $config['logPath']
            : Fate::getPathAlias($this->logPath);

        if(isset($config['logFile'])) {
            $this->logFile = $config['logFile'];
        }

        if(isset($config['maxFileSize'])) {
            $this->maxFileSize = $config['maxFileSize'];
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

        // here fopen() automatically create file when not exists
        if(false === ($fp = fopen($file, 'a'))) {
            return;
        }

        // write file
        flock($fp, LOCK_EX);

        clearstatcache();

        // file size too big
        if( filesize($file) > $this->maxFileSize * 1024 ) {
            flock($fp, LOCK_UN);
            fclose($fp);

            $newFile = $file . date('YmdHis');
            rename($file, $newFile);
            file_put_contents($file, $msg);

            return;
        }

        // file size normal
        fwrite($fp, $msg);

        flock($fp, LOCK_UN);
        fclose($fp);
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
