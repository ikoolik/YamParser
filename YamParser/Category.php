<?php

namespace YamParser;

/**
 * Class Category категория товаров
 *
 * @package YamParser\Supply
 */
class Category {

    const SEARCH_URL = '/guru.xml?CMD=CAT_ID=:category::subcat:&hid=:hid:';

    private $category;
    private $subCategories;
    private $goods;

    /**
     * @param integer $category идентификатор категории Маркета
     * @param array|null $subCategories массив строковых идентификаторов брендов
     */
    public function __construct($category, $subCategories = null) {
        $this->category = $category;
        $this->subCategories = $subCategories;
    }

    /**
     * @return array объектов \YamParser\Containers\Base\Model
     */
    public function getGoods() {
        if(is_null($this->goods)) {
            $this->goods = $this->loadGoods();
        }
        return $this->goods;
    }

    /**
     * Парсит все страницы выдачи
     * @return array объектов \YamParser\Containers\Base\Model
     */
    private function loadGoods()
    {
        $subCat = '';
        if(is_array($this->subCategories)) {
            foreach($this->subCategories as $subCategory) {
                $subCat .= '-PF='.$subCategory;
            }
        }
        $url = str_replace(
            [':category:', ':hid:', ':subcat:'],
            [$this->category, YamParser::getHid(), $subCat],
            self::SEARCH_URL
        );
        return $this->recursiveLoad(YamParser::BASE_URL.$url);
    }

    /**
     * Рекурсивно парсит страницу за страницей выдачи
     *
     * @param string $url страница, с которой начинаем
     * @return array объектов \YamParser\Containers\Base\Model
     */
    private function recursiveLoad($url)
    {
        $models = [];

        $html = file_get_contents($url);
        \phpQuery::newDocument($html);

        // капелька черной магии парсинга
        foreach(pq('div.b-offers.b-offers_type_guru') as $block) {
            $pq_block = pq($block);

            $id = $pq_block->attr('id');
            if(!$id) continue;

            $priceString = str_replace(chr(0xC2).chr(0xA0), "", $pq_block->find('.b-prices__num')->text());
            $data = [
                'id' => intval($id),
                'name' => $pq_block->find('.b-offers__name')->text(),
                'price' => intval(ceil($priceString * 100))
            ];
            $models[] = new Model($data);
        }
        $url = pq('.b-pager__current + .b-pager__page')->attr('href');

        if(!$url) {
            return $models;
        }

        $models = array_merge($models, $this->recursiveLoad(YamParser::BASE_URL . $url));
        return $models;
    }
} 