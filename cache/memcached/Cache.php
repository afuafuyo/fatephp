<?php
/**
 * @author
 * @license MIT
 */
namespace fate\cache\memcached;

use Memcached;
use fate\core\InvalidConfigException;

/**
 * memcached 缓存
 *
 * 'cache' => [
 *      memcached' => [
 *          'classPath' => 'fate\cache\memcached\Cache',
 *          'servers' => [
 *              [
 *                  'host' => '127.0.0.1',
 *                  'port' => '11211'
 *              ]
 *          ]
 *      ]
 * ]
 *
 */
class Cache extends \fate\cache\AbstractCache {

    private $memcached = null;
    public $servers = null;

    public function init() {
        if(null === $this->servers) {
            throw new InvalidConfigException('The servers config of memcached is missing');
        }

        $this->memcached = new Memcached();
        foreach($this->servers as $server) {
            $this->memcached->addServer($server->host, $server->port);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($key) {
        return $this->memcached->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $duration = 0) {
        $this->memcached->set($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key) {
        return $this->memcached->delete($key);
    }

}
