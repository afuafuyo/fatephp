<?php
/**
 * @author
 * @license MIT
 */
namespace fate\web;

/**
 * 前端请求
 */
class Request extends \fate\core\Request {

    /**
     * @var Object 实例
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
     * 获取当前请求是不是通过 https 请求的
     *
     * @return boolean
     */
    public function getIsSecureConnection() {
        return isset($_SERVER['HTTPS']) && (0 === strcasecmp($_SERVER['HTTPS'], 'on') || 1 === $_SERVER['HTTPS'])
            || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 0 === strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https');
    }
    
    /**
     * 获取服务器名
     *
     * @return string | null
     */
    public function getServerName() {
        return isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : null;
    }
    
    /**
     * 获取来源网址
     *
     * @return string | null
     */
    public function getReferrer() {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
    }
    
    /**
     * 获取 URI 协议和主机部分
     *
     * @return string | null
     */
    public function getHostInfo() {
        $host = null;
        $secure = $this->getIsSecureConnection();
        $protocol = $secure ? 'https' : 'http';
        
        if(isset($_SERVER['HTTP_HOST'])) {
            $host = $protocol . '://' . $_SERVER['HTTP_HOST'];
            
        } elseif(isset($_SERVER['SERVER_NAME'])) {
            $host = $protocol . '://' . $_SERVER['SERVER_NAME'];
        }

        return $host;
    }
    
    /**
     * 获取 ip
     *
     * @return string | null
     */
    public function getUserIP() {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
    }

    /**
     * 获取 get 参数
     *
     * @param string $routeParam 参数名
     * @return string | null
     */
    public function getQueryString($routeParam) {
        if( isset($_GET[$routeParam]) ) {
            return $this->filter($_GET[$routeParam]);
        }

        return null;
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

        return null;
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
