<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\cache;

/**
 * Cache 基类
 */
abstract class ImplCache implements ICache {
    /**
     * 进行初始化
     */
    public function init() {}
}
