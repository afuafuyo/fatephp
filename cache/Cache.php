<?php
/**
 * @author
 * @license MIT
 */
namespace fate\cache;

use Fate;
use fate\ioc\ServiceLocator;
use fate\core\InvalidConfigException;
use fate\core\FileNotFoundException;

/**
 * 缓存
 */
final class Cache {

    /**
     * 实例
     */
    private static $serviceLocator = null;

    /**
     * 获取缓存实例
     *
     * @param string $type
     * @return mixed
     */
    public static function getCache($type) {
        if(!isset(Fate::$app->cache) || !isset(Fate::$app->cache[$type])) {
            throw new InvalidConfigException('The cache configuration is not found');
        }

        if(null === Cache::$serviceLocator) {
            Cache::$serviceLocator = new ServiceLocator();
        }

        if( !Cache::$serviceLocator->hasService($type) ){
            Cache::$serviceLocator->setService(
                $type,
                Fate::createObjectAsDefinition(Fate::$app->cache[$type], null)
            );

            Cache::$serviceLocator->getService($type)->init();
        }

        return Cache::$serviceLocator->getService($type);
    }

}
