<?php
/**
 * @author
 * @license MIT
 */
namespace fate\cache\file;

use Fate;
use fate\helpers\FileHelper;

/**
 * 文件缓存
 *
 * 'cache' => [
 *      'file' => [
 *          'classPath' => 'fate\cache\file\Cache',
 *          ...
 *      ]
 * ]
 *
 */
class Cache extends \fate\cache\AbstractCache {

    /**
     * @var string 缓存文件后缀
     */
    public $fileExtension = '.bin';

    /**
     * @var string 缓存目录
     */
    public $cachePath = '';

    public function __construct() {
        $this->cachePath = Fate::getPathAlias('@runtime/caches');
    }

    private function getCacheFile($key) {
        return $this->cachePath . DIRECTORY_SEPARATOR . $key . $this->fileExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key) {
        $rs = '';
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function delete($key) {
        $cacheFile = $this->getCacheFile($key);

        return @unlink($cacheFile);
    }

}
