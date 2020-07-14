<?php
/**
 * @author
 * @license MIT
 */
namespace fate\http;

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
     * @var HttpHeaders
     */
    private $_httpHeaders = null;

    /**
     * @var array
     */
    public $ipHeaders = [
        'X-Forwarded-For'
    ];

    /**
     * @var array
     */
    public $secureProtocolHeaders = [
        'X-Forwarded-Proto' => 'https'
    ];

    /**
     * 获取 http 头
     *
     * @return HttpHeaders
     */
    public function getHttpHeaders() {
        if(null === $this->_httpHeaders) {
            $this->_httpHeaders = new HttpHeaders();

            if(function_exists('getallheaders')) {
                $headers = getallheaders();
                foreach($headers as $name => $value) {
                    $this->_httpHeaders->set($name, $value);
                }

            } else {
                foreach($_SERVER as $name => $value) {
                    if(0 === strncmp($name, 'HTTP_', 5)) {
                        $name = str_replace( '_', '-', strtolower(substr($name, 5)) );
                        $this->_httpHeaders->set($name, $value);
                    }
                }
            }
        }

        return $this->_httpHeaders;
    }

    /**
     * 获取当前请求是不是通过 https 请求的
     *
     * @return boolean
     */
    public function getIsSecureConnection() {
        if( isset($_SERVER['HTTPS']) && (0 === strcasecmp($_SERVER['HTTPS'], 'on') || 1 === $_SERVER['HTTPS']) ) {
            return true;
        }

        foreach($this->secureProtocolHeaders as $header => $value) {
            if( null !== ($v = $this->getHttpHeaders()->get($header)) ) {
                if(0 === strcasecmp($v, $value)) {
                    return true;
                }
            }
        }

        return false;
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
        // return $this->httpHeaders->get('Referer');
        return $this->getHttpHeaders()->get('Referer');
    }

    /**
     * 获取 URI 协议和主机部分
     *
     * @return string | null
     */
    public function getHostInfo() {
        $secure = $this->getIsSecureConnection();
        $protocol = $secure ? 'https' : 'http';

        if( isset($_SERVER['HTTP_HOST']) ) {
            return $protocol . '://' . $_SERVER['HTTP_HOST'];
        }

        if( isset($_SERVER['SERVER_NAME']) ) {
            return $protocol . '://' . $_SERVER['SERVER_NAME'];
        }

        return null;
    }

    /**
     * 获取 ip
     *
     * @return string | null
     */
    public function getUserIP() {
        $httpHeaders = $this->getHttpHeaders();

        foreach($this->ipHeaders as $header) {
            if($httpHeaders->has($header)) {
                return trim( explode(',', $httpHeaders->get($header))[0] );
            }
        }

        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
    }

    /**
     * 获取当前请求使用的方法
     *
     * @return string
     */
    public function getMethod() {
        if(isset($_SERVER['REQUEST_METHOD'])) {
            return strtoupper($_SERVER['REQUEST_METHOD']);
        }

        return 'GET';
    }

    /**
     * 获取 get 参数
     *
     * @param string $routeParam 参数名
     * @param mixed $defaultValue 默认值
     * @return string | null
     */
    public function getQueryString($routeParam, $defaultValue = null) {
        return isset($_GET[$routeParam]) ? $_GET[$routeParam] : $defaultValue;
    }

    /**
     * 获取 post 参数
     *
     * @param string $routeParam 参数名
     * @param mixed $defaultValue 默认值
     * @return string | null
     */
    public function getParameter($routeParam, $defaultValue = null) {
        return isset($_POST[$routeParam]) ? $_POST[$routeParam] : $defaultValue;
    }

}
