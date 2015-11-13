<?php

namespace Omolink\Channel\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilterInterface;

class Channel
{
    // チャネル名
    public $channelName;

    // チャネル説明
    public $channelDescription;

    //インプットフィルタ
    protected $inputFilter;


    /*
     * 配列データの変更値の設定
     */
    public function exchangeArray($data)
    {
        $this->channelName = (isset($data['ChannelName'])) ? $data['ChannelName'] : null;
        $this->channelDescription = (isset($data['ChannelDescription'])) ? $data['ChannelDescription'] : null;
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

            /*
             * チャネル名
             */
            $inputFilter->add(array(
                'name' => 'channelName',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Kdl\Validator\NotEmpty',
                        'options' => array(
                            'subject' => 'チャネル名'
                        ),
                    ),
                    array(
                        'name' => 'Kdl\Validator\StringLength',
                        'options' => array(
                            'max' => 50,
                            'subject' => 'チャネル名'
                        ),
                    ),
                ),
            ));
            
            /*
             * チャネル説明
             */
            $inputFilter->add(array(
                'name' => 'channelDescription',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Kdl\Validator\NotEmpty',
                        'options' => array(
                            'subject' => 'チャネル説明'
                        ),
                    ),
                    array(
                        'name' => 'Kdl\Validator\StringLength',
                        'options' => array(
                            'max' => 80,
                            'subject' => 'チャネル説明'
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }

}
