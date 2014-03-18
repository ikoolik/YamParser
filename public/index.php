<?php
use YamParser\YamParser;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../YamParser/YamParser.php';

$parser = new YamParser(90462);
echo '<pre>';
var_dump($parser->getGoods(8443229, '1801946~EQ~sel~8462081'));
