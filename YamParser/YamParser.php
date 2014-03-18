<?php
namespace YamParser;

use YamParser\Exception\Exception;
use YamParser\Supply\CategoryParser;

spl_autoload_register(function ($className) {
    $path = __DIR__.'/../'.str_replace('\\', '/', $className).'.php';
    if(file_exists($path)) {
        require_once $path;
    }
});


class YamParser {
    const BASE_URL = 'http://market.yandex.ru/';

    private static $hid = '';

    public function __construct($hid = null)
    {
        if(is_null($hid) || !is_integer($hid)) {
            throw new Exception('Bad HID set');
        }
        self::$hid = $hid;
    }

    public function getGoods($category, $subCategory = null)
    {
        if(!is_integer($category)) {
            throw new Exception('category identifier must be integer');
        }
        if(!is_null($subCategory) && !is_array($subCategory)) {
            $subCategory = [$subCategory];
        }

        $parser = new CategoryParser($category, $subCategory);
        return $parser->getGoods();
    }

    public static function getHid()
    {
        return self::$hid;
    }
}