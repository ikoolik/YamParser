<?php

namespace YamParser;

class YamParser {
    const BASE_URL = 'http://market.yandex.ru/';

    private static $hid = '';

    /**
     * @param integer $hid идентификатор, подставляемый в запросы к маркету. Можно выдернуть из url
     * @throws UserInputException
     */
    public function __construct($hid)
    {
        if(is_null($hid) || !is_integer($hid)) {
            throw new UserInputException('Bad HID set');
        }
        self::$hid = $hid;
    }

    /**
     * Парсит выдачу расширенного поиска парсит по категории (обязательно) и Брендам (опционально)
     *
     * @param integer $category id категории
     * @param array|string|null $subCategory
     * @return Category
     * @throws UserInputException
     */
    public function parseCategory($category, $subCategory = null)
    {
        if(!is_integer($category)) {
            throw new UserInputException('category identifier must be integer');
        }
        if(!is_null($subCategory) && !is_array($subCategory) && !is_string($subCategory)) {
            throw new UserInputException('bad subcategory identifier. must be array or string or null');
        }
        if(is_string($subCategory)) {
            $subCategory = [$subCategory];
        }

        return new Category($category, $subCategory);
    }

    /**
     * Парсим отзывы о модели
     * @param integer $modelId
     * @return Model
     */
    public function parseModelWithReviews($modelId)
    {
        $model = new Model(['id' => $modelId]);
        return $model->parseReviews();
    }

    /**
     * Парсим картинки модели
     * @param integer $modelId
     * @return Model
     */
    public function parseModelWithPictures($modelId)
    {
        $model = new Model(['id' => $modelId]);
        return $model->parsePictures();
    }

    /**
     * Возвращает предустановленный hid
     * нужно для подстановки в запросы при парсинге категорий и моделей
     * @return null|string hid
     */
    public static function getHid()
    {
        return self::$hid;
    }
}

spl_autoload_register(function ($className) {
    $path = __DIR__.'/../'.str_replace('\\', '/', $className).'.php';
    if(file_exists($path)) {
        require_once $path;
    }
});

class UserInputException extends \Exception{}
class ParsingException extends \Exception{}