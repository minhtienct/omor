<?php

namespace Application\Login\Form;

use Zend\Form\Form;

class LoginForm extends Form
{

    public function __construct()
    {
        parent::__construct('login');
        $this->setAttribute('method', 'post');

        /*
         * ID textbox
         */
        $this->add(array(
            'name' => 'login_name',
            'type' => 'text',
            'attributes' => array(
                'id' => 'loginName',
//                'maxlength' => 11,
                'autocomplete' => 'off',
                'placeholder' => 'メールアドレス',
            ),
        ));

        /*
         * パスワード textbox
         */
        $this->add(array(
            'name' => 'login_password',
            'type' => 'password',
            'attributes' => array(
                'id' => 'loginPw',
//                'maxlength' => 20,
                'autocomplete' => 'off',
                'placeholder' => 'パスワード',
            ),
        ));

        /*
         * ログインボタン
         */
        $this->add(array(
            'name' => 'btnLogin',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'ログイン',
                'id' => 'btn_login',
                'class' => 'btn btn-lg btn-primary',
            ),
        ));
        
        /*
         * 新規登録
         */
        $this->add(array(
            'name' => 'new_register',
            'type' => 'Submit',
            'attributes' => array(
                'value' => '新規登録',
                'id' => 'new_register_btn',
            ),
        ));

    }
}
