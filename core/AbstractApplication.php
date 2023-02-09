<?php
/**
 * @author
 * @license MIT
 */
namespace fate\core;

use Fate;

/**
 * 应用前端控制器
 */
class AbstractApplication extends FateObject {

    /**
     * @var string 异常处理类
     */
    public $exceptionHandler = '';

    /**
     * constructor
     */
    public function __construct($config = []) {
        Fate::$app = $this;

        $this->handlerError();

        $this->init($config);

        Fate::configure($this, $config);
    }

    /**
     * 异常处理
     */
    public abstract function handlerError();

    /**
     * 运行应用
     */
    public abstract function run();

    /**
     * 初始化应用
     *
     * @param array $config 应用配置
     * @throws InvalidConfigException 当丢失必要配置项目时
     */
    public function init(& $config) {
        if(!isset($config['id'])) {
            throw new InvalidConfigException('The "id" configuration is required');
        }

        if(isset($config['appPath'])) {
            $this->setAppPath($config['appPath']);
            unset($config['appPath']);

        } else {
            throw new InvalidConfigException('The "appPath" configuration is required');
        }

        if(isset($config['runtimePath'])) {
            $this->setRuntimePath($config['runtimePath']);
            unset($config['runtimePath']);

        } else {
            // set as "app/runtime"
            $this->setRuntimePath( $this->getAppPath() . '/runtime');
        }
    }

    /**
     * 设置应用路径
     *
     * @param string $path 应用路径
     */
    public function setAppPath($path) {
        Fate::setPathAlias('@app', $path);
    }

    /**
     * 得到应用目录
     */
    public function getAppPath(){
        return Fate::getPathAlias('@app');
    }

    /**
     * 设置 runtime 路径
     *
     * @param string $path 路径
     */
    public function setRuntimePath($path) {
        Fate::setPathAlias('@runtime', $path);
    }

    /**
     * 得到 runtime 目录
     */
    public function getRuntimePath() {
        return Fate::getPathAlias('@runtime');
    }

}
