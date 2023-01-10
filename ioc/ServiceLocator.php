<?php
use Fate;
use fate\core\InvalidConfigException;

/**
 * 服务定位器 [service locator](//en.wikipedia.org/wiki/Service_locator_pattern)
 *
 * ```
 * eg.
 * $serviceLocator = new ServiceLocator();
 * $serviceLocator->setServiceAsDefinition('service1', [
 *      'classPath' => 'somePath/Service1',
 *      'property1' => 'value1',
 *      'property2' => 'value2'
 * ]);
 *
 * $instanceService1 = $serviceLocator->getService('service1');
 * ```
 */
class ServiceLocator {
    /**
     * service 缓存
     */
    public $services = [];

    /**
     * 服务配置
     */
    public $definitions = [];

    /**
     * 设置服务
     *
     * @param string $key
     * @param mixed $service
     */
    public function setService($key, $service) {
        if(null === $service) {
            unset($this->services[$key]);

            return;
        }

        $this->services[$key] = $service;
    }

    /**
     * 以定义方式设置服务
     *
     * @param array $definition
     *
     * ```
     * [
     *     'classPath' => 'path to classfile',
     *     'otherProperty' => value
     * }
     * ```
     *
     */
    public function setServiceAsDefinition($id, $definition) {
        if(null === $definition) {
            unset($this->definitions[$id]);
            return;
        }

        if(!isset($this->definition['classPath'])) {
            throw new InvalidConfigException('The "classPath" configuration of the "'. $key .'" service is missing');
        }

        $this->definitions[$id] = $definition;
    }

    /**
     * 检查服务是否存在
     *
     * @param string $key
     * @return bool
     */
    public function hasService($key) {
        return isset($this->services[$key]) || isset($this->definitions[$key]);
    }

    /**
     * 获取服务
     *
     * @param string $key
     * @return mixed
     */
    public function getService($key) {
        if($this->services[$key]) {
            return $this->services[$key];
        }

        if($this->definitions[$key]) {
            $this->services[$key] = Fate::createObject($this->definitions[$key]);
            return $this->services[$key];
        }

        return null;
    }

    /**
     * 清空服务
     */
    public function clear() {
        $this->services = [];
        $this->definitions = [];
    }

}
