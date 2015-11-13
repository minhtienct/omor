<?php

namespace Omolink\Report\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class Report
{
    //インプットフィルタ
    protected $inputFilter;


    /*
     * 配列データの変更値の設定
     */
    public function exchangeArray($data)
    {
        
    }

    /*
     * コピー配列データの取得
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /*
     * インプットフィルタデータの設定
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /*
     * インプットフィルタデータの取得
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            /*
             * Security CSRF
             */
            $inputFilter->add(array(
                'name' => 'csrf',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\Csrf',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\Csrf::NOT_SAME => "CMN_002",
                            ),
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }

}
