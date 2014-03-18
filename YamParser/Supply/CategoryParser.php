<?php

namespace YamParser\Supply;


use YamParser\YamParser;

class CategoryParser {

    const SEARCH_URL = '/guru.xml?CMD=CAT_ID=:category::subcat:&hid=:hid:';

    private $category;
    private $subCategory;

    public function __construct($category, $subCategory = null) {
        $this->category = $category;
        $this->subCategory = $subCategory;
    }

    public function getGoods() {
        $subCat = '';
        if(is_array($this->subCategory)) {
            foreach($this->subCategory as $subCategory) {
                $subCat .= '-PF='.$subCategory;

            }
        }
        $url = str_replace(
            [':category:', ':hid:', ':subcat:'],
            [$this->category, YamParser::getHid(), $subCat],
            self::SEARCH_URL
        );
        return $this->recurciveLoad(YamParser::BASE_URL.$url);
    }

    private function recurciveLoad($url)
    {
        $goods = [];

        $html = file_get_contents($url);
        \phpQuery::newDocument($html);

        foreach(pq('div.b-offers.b-offers_type_guru') as $block) {
            $pq_block = pq($block);

            $id = $pq_block->attr('id');
            if(!$id) continue;

            $data = [
                'id' => $id,
                'name' => $pq_block->find('.b-offers__name')->text(),
                'price' => str_replace(chr(0xC2).chr(0xA0), "", $pq_block->find('.b-prices__num')->text())
            ];
            $goods[] = $data;
        }
        $url = pq('.b-pager__current + .b-pager__page')->attr('href');

        if(!$url) {
            return $goods;
        }
        $goods = array_merge($goods, $this->recurciveLoad(YamParser::BASE_URL . $url));
        return $goods;
    }
} 