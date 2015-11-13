<?php

namespace Omolink\Content\Form;

use Kdl\Form\BaseUpdateForm;

class ContentForm extends BaseUpdateForm
{
    public function __construct($name = null)
    {
        parent::__construct('contentForm');

        /*
         * content id
         */
        $this->add(array(
            'type' => 'hidden',
            'name' => 'contentId'
        ));
        
        /*
         * file id
         */
        $this->add(array(
            'type' => 'hidden',
            'name' => 'fileId1'
        ));
        
        /*
         * file id
         */
        $this->add(array(
            'type' => 'hidden',
            'name' => 'fileId2'
        ));
        
        /*
         * file id
         */
        $this->add(array(
            'type' => 'hidden',
            'name' => 'fileId3'
        ));
        
        /*
         * file id
         */
        $this->add(array(
            'type' => 'hidden',
            'name' => 'fileId4'
        ));
        
        /*
         * file id
         */
        $this->add(array(
            'type' => 'hidden',
            'name' => 'fileId5'
        ));
        
        /*
         * 画像1
         */
        $this->add(array(
            'type' => 'file',
            'name' => 'imageContentFile1',
            'attributes' => array(
                'id' => 'imageContentFile1',
            ),
        ));

        $this->add(array(
            'type' => 'image',
            'name' => 'contentImage1',
            'attributes' => array(
                'id' => 'contentImage1',
                'src' => '/img/no_image.jpg',
                'class' => 'img-thumbnail',
            ),
        ));
        
        /*
         * 画像2
         */
        $this->add(array(
            'type' => 'file',
            'name' => 'imageContentFile2',
            'attributes' => array(
                'id' => 'imageContentFile2',
            ),
        ));
        
        $this->add(array(
            'type' => 'image',
            'name' => 'contentImage2',
            'attributes' => array(
                'id' => 'contentImage2',
                'src' => '/img/no_image.jpg',
                'class' => 'img-thumbnail',
            ),
        ));
        
        /*
         * 画像3
         */
        $this->add(array(
            'type' => 'file',
            'name' => 'imageContentFile3',
            'attributes' => array(
                'id' => 'imageContentFile3',
            ),
        ));
        
        $this->add(array(
            'type' => 'image',
            'name' => 'contentImage3',
            'attributes' => array(
                'id' => 'contentImage3',
                'src' => '/img/no_image.jpg',
                'class' => 'img-thumbnail',
            ),
        ));
        
        /*
         * 画像4
         */
        $this->add(array(
            'type' => 'file',
            'name' => 'imageContentFile4',
            'attributes' => array(
                'id' => 'imageContentFile4',
            ),
        ));
        
        $this->add(array(
            'type' => 'image',
            'name' => 'contentImage4',
            'attributes' => array(
                'id' => 'contentImage4',
                'src' => '/img/no_image.jpg',
                'class' => 'img-thumbnail',
            ),
        ));
        
        /*
         * 画像5
         */
        $this->add(array(
            'type' => 'file',
            'name' => 'imageContentFile5',
            'attributes' => array(
                'id' => 'imageContentFile5',
            ),
        ));
        
        $this->add(array(
            'type' => 'image',
            'name' => 'contentImage5',
            'attributes' => array(
                'id' => 'contentImage5',
                'src' => '/img/no_image.jpg',
                'class' => 'img-thumbnail',
            ),
        ));
        
        /*
         * 表示順位1
         */
        $this->add(array(
            'name' => 'displayOrder1',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'displayOrder1',
                'class' => 'form-control order-input',
                'value' => 1,
            ),
            'options' => array(
                //'label' => '表示順位',
                'value_options' => array(
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                ),
            )
        ));
        
        /*
         * 画像2
         */
        $this->add(array(
            'type' => 'file',
            'name' => 'imageContentFile2',
            'attributes' => array(
                'id' => 'imageContentFile2',
            ),
        ));
        
        /*
         * 表示順位2
         */
        $this->add(array(
            'name' => 'displayOrder2',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'displayOrder2',
                'class' => 'form-control order-input',
                'value' => 2,
            ),
            'options' => array(
                //'label' => '表示順位',
                'value_options' => array(
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                ),
            )
        ));

        /*
         * 画像3
         */
        $this->add(array(
            'type' => 'file',
            'name' => 'imageContentFile3',
            'attributes' => array(
                'id' => 'imageContentFile3',
            ),
        ));
        
        /*
         * 表示順位3
         */
        $this->add(array(
            'name' => 'displayOrder3',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'displayOrder3',
                'class' => 'form-control order-input',
                'value' => 3,
            ),
            'options' => array(
                //'label' => '表示順位',
                'value_options' => array(
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                ),
            )
        ));
        
        /*
         * 画像4
         */
        $this->add(array(
            'type' => 'file',
            'name' => 'imageContentFile4',
            'attributes' => array(
                'id' => 'imageContentFile4',
            ),
        ));
        
        /*
         * 表示順位4
         */
        $this->add(array(
            'name' => 'displayOrder4',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'displayOrder4',
                'class' => 'form-control order-input',
                'value' => 4,
            ),
            'options' => array(
                //'label' => '表示順位',
                'value_options' => array(
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                ),
            )
        ));    
        
        /*
         * 画像5
         */
        $this->add(array(
            'type' => 'file',
            'name' => 'imageContentFile5',
            'attributes' => array(
                'id' => 'imageContentFile5',
            ),
        ));
        
        /*
         * 表示順位5
         */
        $this->add(array(
            'name' => 'displayOrder5',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'displayOrder5',
                'class' => 'form-control order-input',
                'value' => 5,
            ),
            'options' => array(
                //'label' => '表示順位',
                'value_options' => array(
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                ),
            )
        ));    
        
        /*
         * コンテンツ有効期限（開始）
         */
        $this->add(array(
            'type' => 'Text',
            'name' => 'startExpirationDate',
            'attributes' => array(
                'placeholder' => 'yyyy/mm/dd',
                'class' => 'form-control datepicker input-yyyymmdd',
            ),
        ));
        
        /*
         * コンテンツ有効期限（終了）
         */
        $this->add(array(
            'type' => 'Text',
            'name' => 'endExpirationDate',
            'attributes' => array(
                'placeholder' => 'yyyy/mm/dd',
                'class' => 'form-control datepicker input-yyyymmdd',
            ),
        ));

        /*
         * コンテンツ登録位置情報(緯度)
         */
        $this->add(array(
            'name' => 'registerLocationLatitude',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'registerLocationLatitude',
                'placeholder' => '緯度',
                'class' => 'form-control',
            ),
        ));
        
        /*
         * コンテンツ登録位置情報(経度)
         */
        $this->add(array(
            'name' => 'registerLocationLongitude',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'registerLocationLongitude',
                'placeholder' => '経度',
                'class' => 'form-control',
            ),
        ));
        
        /*
         * コンテンツ名
         */
        $this->add(array(
            'name' => 'contentName',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'contentName',
                'placeholder' => 'コンテンツ名',
                'class' => 'form-control',
            ),
        ));
        
        /*
         * ジャンルID
         */
        $this->add(array(
            'name' => 'genreID',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'genreID',
                'class' => 'form-control',
            ),
            'options' => array(
                'value_options' => array(
                    ''  => '選択してください',
                    '1' => '飲食店',
                    '2' => 'カフェ',
                    '3' => 'イベント',
                    '4' => 'スポット',
                    '5' => 'その他',
                ),
            )
        ));
        
        /*
         * サブジャンル名
         */
        $this->add(array(
            'name' => 'subGenreName',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'subGenreName',
                'class' => 'form-control',
            ),
            'options' => array('disable_inarray_validator' => true)
        ));
        
        /*
         * 住所
         */
        $this->add(array(
            'name' => 'address',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'Address',
                'placeholder' => '住所',
                'class' => 'form-control',
            ),
        ));
        
        /*
         * 電話
         */
        $this->add(array(
            'name' => 'phone',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'phone',
                'placeholder' => '電話',
                'class' => 'form-control',
            ),
        ));
        
        /*
         * 有効時間
         */
        $this->add(array(
            'name' => 'dateEffective',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'dateEffective',
                'placeholder' => '9時〜18時',
                'class' => 'form-control',
            ),
        ));
        
        /*
         * 予算ID
         */
        $this->add(array(
            'name' => 'countId',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'countId',
//                'placeholder' => '予算ID',
                'class' => 'form-control',
            ),
            'options' => array(
                'value_options' => array(
                    ''  => '選択してください',
                    '0' => '無料',
                    '1' => '￥１～￥999',
                    '2' => '￥1,000～￥1,999',
                    '3' => '￥2,000～￥2,999',
                    '4' => '￥3,000～￥3,999',
                    '5' => '￥4,000～￥4,999',
                    '6' => '￥5,000～￥5,999',
                    '7' => '￥6,000～￥7,999',
                    '8' => '￥8,000～￥9,999',
                    '9' => '￥10,000～￥14,999',
                    '10' => '￥15,000～￥19,999',
                    '11' => '￥20,000～￥29,999',
                    '12' => '￥30,000～',
                ),
            ),
        ));     
	
        /*
         * 席数
         */
        $this->add(array(
            'name' => 'numberSeat',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'numberSeat',
                'placeholder' => '席数',
                'class' => 'form-control',
            ),
        ));
        
        /*
         * WEBサイト
         */
        $this->add(array(
            'name' => 'website',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'website',
                'placeholder' => 'http://',
                'class' => 'form-control',
            ),
        ));
        
        /*
         * 概要
         */
        $this->add(array(
            'name' => 'overview',
            'type' => 'Textarea',
            'attributes' => array(
                'id' => 'overview',
                'placeholder' => '概要',
                'class' => 'form-control',
                'rows' => '3',
            ),
        ));
        
        /*
         * 備考
         */
        $this->add(array(
            'name' => 'remark',
            'type' => 'Textarea',
            'attributes' => array(
                'id' => 'remark',
                'placeholder' => '備考',
                'class' => 'form-control',
                'rows' => '3',
            ),
        ));
        
        /*
         * 著作権
         */
        $this->add(array(
            'name' => 'copyright',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'copyright',
                'placeholder' => '著作権',
                'class' => 'form-control',
            ),
        ));
        
//        /*
//         * チャネル
//         */
//        $this->add(array(
//            'name' => 'channel',
//            'type' => 'Text',
//            'attributes' => array(
//                'id' => 'channel',
//                'placeholder' => 'チャネル',
//                'class' => 'form-control',
//            ),
//        ));
        
        /*
         * 申請するチャネル
         */
        $this->add(array(
            'name' => 'channel',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'channel',
//                'placeholder' => 'コンテンツ登録を申請するチャネルを選択してください',
                'class' => 'form-control',
            )
        ));

        /*
         * Facebook連携
         */
        /*$this->add(array(
            'name' => 'rdFacebook',
            'type' => 'Zend\Form\Element\Radio',
            'options' => array(
                'value_options' => array(
                    '1' => 'する',
                    '0' => 'しない',
                ),
            ),
            'attributes' => array(
                'value' => '0'
            )
        ));*/
        
        /*
         * 登録
         */
        $this->add(array(
            'name' => 'btnUpdate',
            'type' => 'Button',
            'options' => array (
                'label' => '登録'
            ),
            'attributes' => array(
                'value' => '登録',
                'id' => 'btnUpdate',
                'class' => 'btn btn-lg btn-primary btn-size'
            ),
        ));
        
    }
}
