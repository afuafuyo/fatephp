<?php
include __DIR__ . '/YBase.php';

class Y extends \y\YBase {}

spl_autoload_register(['Y', 'autoload'], true, true);