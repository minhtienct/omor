<?php

namespace Omolink\Channel\Model;

use Zend\Paginator\Adapter\AdapterInterface;

class ChannelTable implements AdapterInterface
{
    /*
     * チャネル一覧情報配列
     */
    protected $dataArray = array();
           
    /*
     * チャネル一覧データの設定
     */
    public function setChannelData($arrayChannelData) {
        $this->dataArray = array();
        if (count($arrayChannelData) > 0) {
            foreach ($arrayChannelData as $value)
            {
                $this->dataArray[] = $value;
            }
        }
        
        return $this->dataArray;
    }

    /*
     * コンテンツ一覧情報配列のカウント
     */
    public function count() {
        return count($this->dataArray);
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
        return array_slice($this->dataArray, $offset, $itemCountPerPage);
    }
    
    /**
     * うぃる数
     * @param type $index
     * @param type $value
     */
    public function setWill($index, $value) {
        if (!is_null($this->dataArray) && count($this->dataArray) > $index) {
            $this->dataArray[$index]['will_count'] = $value;
        }
    }
    
    /**
     * 思い出総数
     * @param type $index
     * @param type $value
     */
    public function setMemory($index, $value) {
        if (!is_null($this->dataArray) && count($this->dataArray) > $index) {
            $this->dataArray[$index]['memory_count'] = $value;
        }
    }
    
    public function getChannelIds() {
        if (!is_null($this->dataArray) && count($this->dataArray) > 0) {
            $ids = array();
            foreach ($this->dataArray as $row) {
                $ids[] = $row['channel_id'];
            }
            return $ids;
        }
        return array();
    }
}
