<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\cache\file;

use Y;
use y\helpers\FileHelper;

/**
 * 文件缓存
 *
 * 'cache' => [
 *      'file' => [
 *          'class' => 'y\cache\file\Cache',
 *          ...
 *      ]
 * ]
 *
 */
class Cache extends \y\cache\ImplCache {
    
    /**
     * @var string 缓存文件后缀
     */
    public $fileExtension = '.bin';
    
    /**
     * @var string 缓存目录
     */
    public $cachePath = '@runtime/cache';
    
    public function __construct(& $config) {
        if(isset($config['fileExtension'])) {
            $this->fileExtension = $config['fileExtension'];
        }
        
        $this->cachePath = isset($config['cachePath'])
            ? $config['cachePath']
            : Y::getPathAlias($this->cachePath);
    }
    
    private function getCacheFile($key) {
        return $this->cachePath . DIRECTORY_SEPARATOR . $key . $this->fileExtension;
    }
    
    /**
     * {@inheritDoc}
     */
    public function get($key) {
        $rs = null;
        $cacheFile = $this->getCacheFile($key);
        
        if(is_file($cacheFile) && filemtime($cacheFile) > time()) {
            $fp = @fopen($cacheFile, 'r');
            if(false !== $fp) {
                $rs = @stream_get_contents($fp);
                @fclose($fp);
            }
        }

        return $rs;
    }
    
    /**
     * {@inheritDoc}
     */
    public function set($key, $value, $duration = 31536000) {
        if(!is_dir($this->cachePath)) {
            FileHelper::createDirectory($this->cachePath);
        }
        
        $cacheFile = $this->getCacheFile($key);
        
        @file_put_contents($cacheFile, $value);
        
        @touch($cacheFile, $duration + time());
    }
    
    /**
     * {@inheritDoc}
     */
    public function delete($key) {
        $cacheFile = $this->getCacheFile($key);

        return @unlink($cacheFile);
    }
    
}
