<?php

namespace Omolink\Report\Form;

use Kdl\Form\BaseUpdateForm;

class ReportForm extends BaseUpdateForm
{
    public function __construct($name = null)
    {
        parent::__construct('report');   
        
        /*
         * コンテンツID
         */
        $this->add(array(
            'type' => 'hidden',
            'name' => 'contentId'
        ));
        
        /*
         * コンテンツ名
         */
        $this->add(array(
            'name' => 'contentName',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'contentName',
                'maxlength' => 80,
                'placeholder' => 'コンテンツ名',
                'class' => 'form-control',
                'readonly' => true,
            ),
        ));
        
        /*
         * コンテンツオーナー名
         */
        $this->add(array(
            'name' => 'contentOwnerName',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'contentOwnerName',
                'maxlength' => 80,
                'placeholder' => 'コンテンツオーナー名',
                'class' => 'form-control',
                'readonly' => true,
            ),
        ));
        
        /*
         * うぃる数
         */
        $this->add(array(
            'name' => 'willCount',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'willCount',
                'maxlength' => 80,
                'placeholder' => 'うぃる数',
                'class' => 'form-control',
            ),
        ));
        
        /*
         * 思い出登録数
         */
        $this->add(array(
            'name' => 'omoCount',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'omoCount',
                'maxlength' => 80,
                'placeholder' => '思い出登録数',
                'class' => 'form-control',
            ),
        ));
        
        /*
         * 評価平均点
         */
        $this->add(array(
            'name' => 'rateAverage',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'rateAverage',
                'maxlength' => 80,
                'placeholder' => '評価平均点',
                'class' => 'form-control',
            ),
        ));
    
    }
}
