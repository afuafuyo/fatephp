<?php
/**
 * @author yu
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace y\log\file;

class Target extends \y\log\ImplTarget {
    public function flush($messages) {
        var_dump($messages);
    }
}