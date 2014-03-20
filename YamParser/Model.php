<?php

namespace YamParser;

/**
 * Class Model конкретно взятый товар в маркете
 * @package YamParser
 */
class Model {
    const MAIN_PAGE_URL = 'http://market.yandex.ru/model.xml?modelid=:id:&hid=:hid:';
    protected $id;
    protected $name;
    protected $price;
    protected $picture;
    protected $pictures = [];

    public function __construct($data)
    {
        if(!isset($data['id']) || !is_integer($data['id'])) {
            throw new ParsingException("Некорректное значение id товара > {$data['id']}");
        }
        $this->id = $data['id'];

        if(isset($data['name']) && is_string($data['name'])) {
            $this->name = $data['name'];
        }
        if(isset($data['price']) && is_integer($data['price'])) {
            $this->price = $data['price'];
        }
    }
    public function parseReviews()
    {

    }

    public function parsePictures()
    {
        $url = str_replace([':id:', ':hid:'], [$this->id, YamParser::getHid()], self::MAIN_PAGE_URL);

        $html = file_get_contents($url);
        \phpQuery::newDocument($html);

        $this->pictures = [];
        foreach(pq('#model-pictures')->find('a') as $link) {
            $this->pictures[] = pq($link)->attr('href');
        }
        foreach(pq('#model-pictures')->find('img:not(.b-model-pictures__zoom)') as $img) {
            $src = pq($img)->attr('src');
            if(!strstr($src, 'size=1')) {
                $this->picture = $src;
            }
        }
        return $this;
    }
} 