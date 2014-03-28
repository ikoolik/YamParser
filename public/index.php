<?php
use YamParser\YamParser;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../YamParser/YamParser.php';

$parser = new YamParser(6269371);
echo '<pre>';
echo 'HELLO, DUDE!';
//$category = $parser->parseCategory(7156311, '1801946~EQ~sel~7291067');
//foreach($category->getGoods() as $model) {
//    var_dump($model->parsePictures());
//}
