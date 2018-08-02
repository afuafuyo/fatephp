<?php
/**
 * @author
 * @license MIT
 */
namespace fate\cache;

use Fate;
use fate\core\InvalidConfigException;
use fate\core\FileNotFoundException;

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

        if(!isset(Fate::$app->cache) || !isset(Fate::$app->cache[$cacheFlag])) {
            throw new InvalidConfigException('Unknow cache config: ' . $cacheFlag);
        }
        
        if(!isset(Fate::$app->cache[$cacheFlag]['classPath'])) {
            throw new InvalidConfigException('Lost `classPath` config item of the cache class');
        }
        
        if( !isset(static::$_caches[$cacheFlag]) || null === static::$_caches[$cacheFlag] ){
            $config = Fate::$app->cache[$cacheFlag];
            $cacheClass = $config['classPath'];
            $cacheFile = Fate::namespaceToNormal($cacheClass);
            
            if(!is_file($cacheFile)) {
                throw new FileNotFoundException('The cacheFile: ' . $cacheFile . ' not found');
            }
            
            static::$_caches[$cacheFlag] = new $cacheClass($config);
            
            static::$_caches[$cacheFlag]->init();
        }
        
        return static::$_caches[$cacheFlag];
    }
    
}
