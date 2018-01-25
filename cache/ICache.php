<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\cache;

/**
 * 缓存接口
 */
interface ICache {
    
    /**
     * 写入缓存
     *
     * @param string $key 缓存键
     * @param string $value 缓存值
     * @param int $duration 过期时间 秒
     */
    public function set($key, $value, $duration);
    
    /**
     * 读取缓存
     *
     * @param string $key 缓存键
     */
    public function get($key);
    
    /**
     * 删除缓存
     *
     * @param string $key 缓存键
     */
    public function delete($key);
    
}
