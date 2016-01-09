<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\cache;

use Memcached;
use y\core\InvalidConfigException;

/**
 * 配置
 *
 * [
 *      'class' => 'y\cache\MemcachedCache'
 *      ,'servers' => [
 *          [
 *               'host' => '127.0.0.1'
 *               ,'port' => '11211'
 *          ]
 *      ]
 * ]
 *
 */
class MemcachedCache extends ImplCache {
    
    private $memcached = null;
    
    /**
     * 实例化 
     */
    public function __construct(& $config) {
        parent::__construct();
        
        if(!isset($config['servers']) || empty($config['servers'])) {
            throw new InvalidConfigException('The "servers" property must be specified');
        }
        
        $this->memcached = new Memcached();
        foreach($config['servers'] as $server) {
            $this->memcached->addServer($server->host, $server->port);
        }
    }
    
    public function get($key) {
        return $this->memcached->get($key);
    }
    
    public function set($key, $value, $duration = 0) {
        $this->memcached->set($key, $value);
    }
    
    public function delete($key) {
        return $this->memcached->delete($key);
    }
    
}
