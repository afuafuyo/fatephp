<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\cache;

use Y;
use y\db\CacheException;
use y\core\InvalidConfigException;
use y\core\FileNotFoundException;

/**
 * 缓存
 */
final class CacheFactory {
    
    /**
     * @var array 缓存对象
     */
    private static $_caches = [];
    
    public static function instance($cacheFlag = '') {
        if(empty($cacheFlag)) {
            throw new CacheException('Empty param: cacheFlag');
        }

        if(!isset(Y::$app->cache[$cacheFlag])) {
            throw new InvalidConfigException('Unknow cache config: ' . $cacheFlag);
        }
        
        if(!isset(Y::$app->cache[$cacheFlag]['class'])) {
            throw new InvalidConfigException('Lost `class` config item of the cache class');
        }
        
        if( !isset(static::$_caches[$cacheFlag]) || null === static::$_caches[$cacheFlag] ){
            $config = Y::$app->cache[$cacheFlag];
            $cacheClass = $config['class'];
            $cacheFile = Y::namespaceTranslate($cacheClass);
            
            if(!is_file($cacheFile)) {
                throw new FileNotFoundException('The cacheFile: ' . $cacheFile . ' not found');
            }
            
            static::$_caches[$cacheFlag] = new $cacheClass($config);
            
            static::$_caches[$cacheFlag]->init();
        }
        
        return static::$_caches[$cacheFlag];
    }
}
