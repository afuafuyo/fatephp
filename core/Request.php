<?php
/**
 * @author
 * @license MIT
 */
namespace fate\core;

abstract class Request extends FateObject {
    /**
     * @var string 入口文件名
     */
    private $_scriptFile;

    /**
     * 返回入口文件名
     *
     * @return string
     */
    public function getScriptFile() {
        if(null === $this->_scriptFile) {
            if(isset($_SERVER['SCRIPT_FILENAME'])) {
                $this->_scriptFile = $_SERVER['SCRIPT_FILENAME'];
            }
        }

        return $this->_scriptFile;
    }
}
