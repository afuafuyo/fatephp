<?php
defined('Y_DEBUG') or define('Y_DEBUG', false);

include __DIR__ . '/YBase.php';

class Y extends \y\YBase {}

spl_autoload_register(['Y', 'autoload'], true, true);
