<?php

namespace Omolink\Content\Model;

use Zend\Paginator\Adapter\AdapterInterface;

class ContentTable implements AdapterInterface
{
    /*
     * コンテンツ一覧情報配列
     */
    protected $dataArray, $count;
    
    /*
     * コンストラクタ
     */
    public function __construct() {
    
    }

    /*
     * コンテンツ一覧データの設定
     */
    public function setContentTable($arrayContentData, $totalCount) {
        $this->dataArray = array();
        if (count($arrayContentData) > 0) {
            foreach ($arrayContentData as $value)
            {
                $this->dataArray[] = $value;
            }
        }
        
        $this->count = $totalCount;
        
        return $this->dataArray;        
    }
    
    /*
     * コンテンツ一覧情報配列のカウント
     */
    public function count() {
        return $this->count;
    }

    /**
     * Returns a collection of items for a page.
     *
     * @param  int $offset Page offset
     * @param  int $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        if (is_null($this->dataArray)) {
            return array();
        }
        return array_slice($this->dataArray, $offset, $itemCountPerPage);
    }
    
    public function getAllItems() {
        if (is_null($this->dataArray)) {
            return array();
        }
        return $this->dataArray;
    }
    
    public function isEmpty() {
        return is_null($this->dataArray) || count($this->dataArray) == 0;
    }
}
