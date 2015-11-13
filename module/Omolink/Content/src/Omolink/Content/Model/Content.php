<?php

namespace Omolink\Content\Model;

use Exception;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Csrf;

class Content
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
        throw new Exception("Not used");
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
                                Csrf::NOT_SAME => "CMN_002",
                            ),
                        ),
                    ),
                ),
            ));
            
            /*
            * 画像1
            */
            $inputFilter->add(array(
                'name' => 'imageContentFile1',
                'required' => true,
            ));
            
            /*
             * コンテンツ登録位置情報
             */
            $inputFilter->add(array(
                'name' => 'registerLocationLatitude',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Kdl\Validator\NotEmpty',
                        'options' => array(
                            'subject' => '緯度'
                        ),
                    ),
                    array(
                        'name' => 'Kdl\Validator\Numeric',
                        'options' => array(
                            'subject' => '緯度'
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'registerLocationLongitude',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Kdl\Validator\NotEmpty',
                        'options' => array(
                            'subject' => '経度'
                        ),
                    ),
                    array(
                        'name' => 'Kdl\Validator\Numeric',
                        'options' => array(
                            'subject' => '経度'
                        ),
                    ),
                ),
            ));
            
            /*
            * 予算ID
            */
            $inputFilter->add(array(
                'name' => 'countId',
                'required' => false,                
            ));

            /*
             * コンテンツ名
             */
            $inputFilter->add(array(
                'name' => 'contentName',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Kdl\Validator\NotEmpty',
                        'options' => array(
                            'subject' => 'コンテンツ名'
                        ),
                    ),
                    array(
                        'name' => 'Kdl\Validator\StringLength',
                        'options' => array(
                            'max' => 80,
                            'subject' => 'コンテンツ名'
                        ),
                    ),
                ),
            ));
            
            /*
             * ジャンルID
             */
            $inputFilter->add(array(
                'name' => 'genreID',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Kdl\Validator\NotEmpty',
                        'options' => array(
                            'subject' => 'ジャンルID'
                        ),
                    ),
                    array(
                        'name' => 'Kdl\Validator\StringLength',
                        'options' => array(
                            'max' => 80,
                            'subject' => 'ジャンルID'
                        ),
                    ),
                ),
            ));
            
            /*
            * サブジャンル名
            */
            $inputFilter->add(array(
                'name' => 'subGenreName',
                'required' => false,
                'options' => array(
                    'disable_inarray_validator' => true,
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'channel',
                'required' => true,
                'options' => array(
                    'disable_inarray_validator' => true,
                ),
            ));

            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }

}
