<?php
/**
 * @author
 * @license MIT
 */
namespace y\cache\memcached;

use Memcached;
use y\core\InvalidConfigException;

/**
 * memcached 缓存
 *
 * 'cache' => [
 *      memcached' => [
 *          'class' => 'y\cache\memcached\Cache'
 *          ,'servers' => [
 *              [
 *                  'host' => '127.0.0.1'
 *                  ,'port' => '11211'
 *              ]
 *          ]
 *      ]
 * ]
 *
 */
class Cache extends \y\cache\ImplCache {
    
    private $_memcached = null;
    
    /**
     * 实例化 
     */
    public function __construct(& $config) {        
        if(!isset($config['servers']) || empty($config['servers'])) {
            throw new InvalidConfigException('The "servers" property must be specified');
        }
        
        $this->_memcached = new Memcached();
        foreach($config['servers'] as $server) {
            $this->_memcached->addServer($server->host, $server->port);
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function get($key) {
        return $this->_memcached->get($key);
    }
    
    /**
     * {@inheritDoc}
     */
    public function set($key, $value, $duration = 0) {
        $this->_memcached->set($key, $value);
    }
    
    /**
     * {@inheritDoc}
     */
    public function delete($key) {
        return $this->_memcached->delete($key);
    }
    
}
