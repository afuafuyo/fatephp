<?php
/**
 * @author
 * @license MIT
 */
namespace fate\http;

/**
 * 简单控制器
 */
class HttpHeaders extends \fate\core\FateObject {
    /**
     * @var array httpHeaders list
     */
    private $_headers = [];

    /**
     * 添加一个 http 头
     *
     * @param string $name the name of the header eg. Transfer-Encoding
     * @param string $value the value of the header
     */
    public function set($name, $value) {
        $name = strtolower($name);

        $this->_headers[$name] = $value;

        return $this;
    }

    /**
     * 获取一个 http 头
     *
     * @param string $name the name of the header
     * @return string | null
     */
    public function get($name) {
        $name = strtolower($name);

        if(isset($this->_headers[$name])) {
            return $this->_headers[$name];
        }

        return null;
    }

    /**
     * 删除一个 header
     *
     * @return string | null
     */
    public function delete($name) {
        $name = strtolower($name);

        if(isset($this->_headers[$name])) {
            $value = $this->_headers[$name];
            unset($this->_headers[$name]);

            return $value;
        }

        return null;
    }

    /**
     * 是否存在 header
     *
     * @return bool
     */
    public function has($name) {
        $name = strtolower($name);

        return isset($this->_headers[$name]);
    }
}
