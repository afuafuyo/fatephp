<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\web;

/**
 * 前端请求
 */
class Request extends \y\core\Request {

    /**
     * @var Object $_instance 实例
     */
    private static $_instance = null;
    
    /**
     * 获得单例对象
     * @return Object
     */
    public static function getInstance() {
        if(null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function getParam($routeParam) {
        if( isset($_GET[$routeParam]) ) {
            return $this->filter($_GET[$routeParam]);
        }

        return '';
    }
    
    public function postParam($routeParam) {
        if( isset($_POST[$routeParam]) ) {
            return $this->filter($_POST[$routeParam]);
        }

        return '';
    }

    /**
     * 过滤
     * @return string
     */
    public function filter($string) {
        return htmlspecialchars($string, ENT_QUOTES);
    }
    
}
