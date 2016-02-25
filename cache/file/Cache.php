<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\cache\file;

use Y;

/**
 * 文件缓存类
 *
 * 配置
 *
 *  'cache' => [
 *      'file' => [
 *          'class' => 'y\cache\file\Cache'  // 单引号字符串不转译
 *      ]
 *  ]
 *
 */
class Cache extends \y\cache\ImplCache {
    
    /**
     * @var string 缓存目录
     */
    public $cachePath = '@runtime/cache';
    
    /**
     * @var int 目录权限
     */
    public $dirMode = 0775;
    
    /**
     * @var string 缓存文件后缀
     */
    public $cacheFileSuffix = '.bin';
    
    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        
        $this->cachePath = Y::getPathAlias($this->cachePath);
        
        if(!is_dir($this->cachePath)) {
            @mkdir($this->cachePath, $this->dirMode, true);
            @chmod($this->cachePath, $this->dirMode);
        }
    }
    
    private function getCacheFile($key) {
        return $this->cachePath . DIRECTORY_SEPARATOR . $key . $this->cacheFileSuffix;
    }
    
    /**
     * @inheritdoc
     */
    public function get($key) {
        $rs = null;
        $cacheFile = $this->getCacheFile($key);
        
        if (@filemtime($cacheFile) > time()) {
            $fp = @fopen($cacheFile, 'r');
            if (false !== $fp) {
                $rs = @stream_get_contents($fp);
                @fclose($fp);
            }
        }

        return $rs;
    }
    
    /**
     * @inheritdoc
     */
    public function set($key, $value, $duration = 31536000) {
        $cacheFile = $this->getCacheFile($key);
        
        @file_put_contents($cacheFile, $value);
        
        @touch($cacheFile, $duration + time());
    }
    
    /**
     * @inheritdoc
     */
    public function delete($key) {
        $cacheFile = $this->getCacheFile($key);

        return @unlink($cacheFile);
    }
}
