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
     *
     * @return Object
     */
    public static function getInstance() {
        if(null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * 获取 get 参数
     *
     * @param string $routeParam 参数名
     */
    public function getQueryString($routeParam) {
        if( isset($_GET[$routeParam]) ) {
            return $this->filter($_GET[$routeParam]);
        }

        return '';
    }
    
    /**
     * 获取 post 参数
     *
     * @param string $routeParam 参数名
     */
    public function getParameter($routeParam) {
        if( isset($_POST[$routeParam]) ) {
            return $this->filter($_POST[$routeParam]);
        }

        return '';
    }

    /**
     * 过滤
     *
     * @param string $string 待处理字符串
     */
    public function filter($string) {
        return htmlspecialchars($string, ENT_QUOTES);
    }
    
}
