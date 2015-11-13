<?php

namespace Omolink\User\Model;

use Zend\Paginator\Adapter\AdapterInterface;

class UserTable implements AdapterInterface
{
    /*
     * ユーザー一覧情報配列
     */
    protected $dataArray = array();
    protected $count = 0;

    /*
     * ユーザー一覧データの設定
     */
    public function setUserData($arrayUserData, $count)
    {
        $this->dataArray = array();
        if (count($arrayUserData) > 0) {
            foreach ($arrayUserData as $value)
            {
                $this->dataArray[] = $value;
            }
        }
        
        $this->count = $count;

        return $this->dataArray;
    }
    
    /*
     * @return int The custom count as an integer.
     * The return value is cast to an integer.
     */   
    public function count()
    {
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
        return array_slice($this->dataArray, $offset, $itemCountPerPage);
    }    
}
