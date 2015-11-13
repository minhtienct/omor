<?php

namespace Application\Login\Form;

use Application\Login\Form\BrowseInputFilter;
use Kdl\Form\BaseUpdateForm;

class BrowseForm extends BaseUpdateForm
{

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->setInputFilter(new BrowseInputFilter());

        /*
         * 現在の閲覧パスワード
         */
        $this->add(array(
            'name' => 'oldPassword',
            'type' => 'Zend\Form\Element\Password',
            'attributes' => array(
                'maxlength' => 20,
                'autocomplete' => 'off',
                'class' => 'form-control',
            ),
        ));

        /*
         * 新パスワードの入力
         */
        $this->add(array(
            'name' => 'newPassword',
            'type' => 'Zend\Form\Element\Password',
            'attributes' => array(
                'minlength' => 8,
                'maxlength' => 20,
                'autocomplete' => 'off',
                'class' => 'form-control',
            ),
        ));

        /*
         * 新パスワードの確認
         */
        $this->add(array(
            'name' => 'confirmPassword',
            'type' => 'Zend\Form\Element\Password',
            'attributes' => array(
                'minlength' => 8,
                'maxlength' => 20,
                'autocomplete' => 'off',
                'class' => 'form-control',
            ),
        ));

        /*
         * 閲覧パスワード有効時間(分)
         */
        $this->add(array(
            'name' => 'browseValidMinute',
            'type' => 'Text',
            'attributes' => array(
                'maxlength' => 4,
                'class' => 'form-control',
            ),
        ));
        /*
         * 更新 button
         */
        $this->add(array(
            'name' => 'btnUpdate',
            'attributes' => array(
                'type' => 'submit',
                'value' => '更新',
                'class' => 'btn btn-lg btn-primary',
            ),
        ));

    }
}
