<?php
defined('FATE_DEBUG') or define('FATE_DEBUG', false);

include __DIR__ . '/FateBase.php';

class Fate extends \fate\FateBase {}

spl_autoload_register(['Fate', 'autoload'], true, true);
