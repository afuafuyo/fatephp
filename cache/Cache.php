<?php
/**
 * @author
 * @license MIT
 */
namespace y\cache;

use Y;
use y\core\InvalidConfigException;
use y\core\FileNotFoundException;

/**
 * 缓存
 */
final class Cache {
    
    /**
     * @var array 缓存对象
     */
    private static $_caches = [];
    
    /**
     * 获取缓存实例
     *
     * @param string $cacheFlag
     * @return Object
     */
    public static function getCache($cacheFlag = '') {
        if(empty($cacheFlag)) {
            throw new CacheException('Empty param: cacheFlag');
        }

        if(!isset(Y::$app->cache) || !isset(Y::$app->cache[$cacheFlag])) {
            throw new InvalidConfigException('Unknow cache config: ' . $cacheFlag);
        }
        
        if(!isset(Y::$app->cache[$cacheFlag]['class'])) {
            throw new InvalidConfigException('Lost `class` config item of the cache class');
        }
        
        if( !isset(static::$_caches[$cacheFlag]) || null === static::$_caches[$cacheFlag] ){
            $config = Y::$app->cache[$cacheFlag];
            $cacheClass = $config['class'];
            $cacheFile = Y::namespaceToNormal($cacheClass);
            
            if(!is_file($cacheFile)) {
                throw new FileNotFoundException('The cacheFile: ' . $cacheFile . ' not found');
            }
            
            static::$_caches[$cacheFlag] = new $cacheClass($config);
            
            static::$_caches[$cacheFlag]->init();
        }
        
        return static::$_caches[$cacheFlag];
    }
}
