<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\cache;

/**
 * Cache 基类
 */
abstract class ImplCache extends \y\core\Object implements ICache {
    
    public function init() {}
}