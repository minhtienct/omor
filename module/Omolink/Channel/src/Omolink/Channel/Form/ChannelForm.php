<?php

namespace Omolink\Channel\Form;

use Kdl\Form\BaseUpdateForm;

class ChannelForm extends BaseUpdateForm
{
    public function __construct($name = null)
    {
        parent::__construct('channelRegisterForm');

        /*
         * チャネルID
         */
        $this->add(array(
            'type' => 'hidden',
            'name' => 'channelId'
        ));
        
        /*
         * チャネル名
         */
        $this->add(array(
            'name' => 'channelName',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'channelName',
                'maxlength' => 80,
                'placeholder' => 'チャネル名',
                'class' => 'form-control',
            ),
        ));

        /*
         * チャネル説明
         */
        $this->add(array(
            'name' => 'channelDescription',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'channelDescription',
                'maxlength' => 100,
                'placeholder' => 'チャネル説明',
                'class' => 'form-control',
            ),
        ));
       
        /*
         * バナー画像
         */
        $this->add(array(
            'name' => 'txtBannerImage',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'txtBannerImage',
                'maxlength' => 50,
                'class' => 'form-control',                
            ),
        ));
        
        /*
         * バナー画像ボタン
         */
        $fileBannerImgPhoto = new \Zend\Form\Element\File('fileBannerImgPhoto');
        $fileBannerImgPhoto->setAttribute('id', 'fileBannerImgPhoto');
        $fileBannerImgPhoto->setAttribute('style', 'display:none;');
        $this->add($fileBannerImgPhoto);

        /*
         * コンテンツフレーム上画像
         */
        $this->add(array(
            'name' => 'txtFrameOnImage',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'txtFrameOnImage',
                'maxlength' => 50,
                'class' => 'form-control',
            ),
        ));
        
        /*
         * コンテンツフレーム上画像ボタン
         */
        $fileFrameOnImagePhoto = new \Zend\Form\Element\File('fileFrameOnImagePhoto');
        $fileFrameOnImagePhoto->setAttribute('id', 'fileFrameOnImagePhoto');
        $fileFrameOnImagePhoto->setAttribute('style', 'display:none;');
        $this->add($fileFrameOnImagePhoto);       
        
        /*
         * コンテンツフレーム下画像
         */
        $this->add(array(
            'name' => 'txtFrameUnderImage',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'txtFrameUnderImage',
                'maxlength' => 50,
                'class' => 'form-control',
            ),
        ));

        /*
         * コンテンツフレーム下画像ボタン
         */
        $fileFrameUnderImagePhoto = new \Zend\Form\Element\File('fileFrameUnderImagePhoto');
        $fileFrameUnderImagePhoto->setAttribute('id', 'fileFrameUnderImagePhoto');
        $fileFrameUnderImagePhoto->setAttribute('style', 'display:none;');
        $this->add($fileFrameUnderImagePhoto);       
        
        /*
         * コンテンツアイコン画像
         */
        $this->add(array(
            'name' => 'txtIconImage',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'txtIconImage',
                'maxlength' => 50,
                'class' => 'form-control',
            ),
        ));
             
        /*
         * コンテンツアイコン画像ボタン
         */
        $fileFrameIconImagePhoto = new \Zend\Form\Element\File('fileFrameIconImagePhoto');
        $fileFrameIconImagePhoto->setAttribute('id', 'fileFrameIconImagePhoto');
        $fileFrameIconImagePhoto->setAttribute('style', 'display:none;');
        $this->add($fileFrameIconImagePhoto);       
                
        /*
         * 登録
         */
        $this->add(array(
            'name' => 'btnRegister',
            'type' => 'Button',
            'options' => array (
                'label' => '登録'
            ),
            'attributes' => array(
                'value' => '登録',
                'id' => 'btnRegister',
                'class' => 'btn btn-lg btn-primary btn-size',
            ),
        ));       
    }
}
