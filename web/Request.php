<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\web;

/**
 * 前端请求
 */
class Request extends \y\core\Object {
	
    /**
     * @var Object $instance 实例
     */
    private static $_instance = null;

    /**
     * 私有构造方法
     */
    private function __construct() {}

    /**
     * 过滤
     */
    private function filter($string) {
        return htmlspecialchars($string, ENT_QUOTES);
    }
    
    /**
     * 获得单例对象
     */
    public static function getInstance() {
        if(null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
	
    /**
     * 解析路由
     */
    public function parseUrl($routeParam) {
        if( isset($_GET[$routeParam]) ) {
            return $this->filter($_GET[$routeParam]);
        }

        return '';
    }
}

