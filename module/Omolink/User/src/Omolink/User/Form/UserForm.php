<?php

namespace Omolink\User\Form;

use Kdl\Form\BaseUpdateForm;
use \Application\Application\Constant\CommonConstant;

class UserForm extends BaseUpdateForm
{
    public function __construct($name = null)
    {
        parent::__construct('userRegisterForm');

        /*
         * ユーザーID
         */
        $this->add(array(
            'type' => 'hidden',
            'name' => 'userId'
        ));
        
        /*
         * 企業・団体名
         */
        $this->add(array(
            'name' => 'companyName',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'companyName',
                'maxlength' => 80,
                'placeholder' => '企業・団体名',
                'class' => 'form-control',                
            ),
        ));

        /*
         * 企業・団体名（カナ）
         */
        $this->add(array(
            'name' => 'companyNameKana',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'companyNameKana',
                'maxlength' => 80,
                'placeholder' => '企業・団体名（カナ）',
                'class' => 'form-control',
            ),
        ));
        
        /*
         * 郵便番号
         */
        $this->add(array(
            'name' => 'postalCode',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'postalCode',
                'maxlength' => 13,
                'placeholder' => '郵便番号',
                'class' => 'form-control',
            ),
        ));
                
        /*
         * 住所
         */
        $this->add(array(
            'name' => 'address',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'address',
                'maxlength' => 80,
                'placeholder' => '住所',
                'class' => 'form-control',
            ),
        ));

        /*
         * 電話番号
         */
        $this->add(array(
            'name' => 'telNumber',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'telNumber',
                'maxlength' => 50,
                'placeholder' => '電話番号',
                'class' => 'form-control',
            ),
        ));

        /*
         * 担当者氏名
         */
        $this->add(array(
            'name' => 'staffName',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'staffName',
                'maxlength' => 80,
                'placeholder' => '担当者氏名',
                'class' => 'form-control',
            ),
        ));

         /*
         * 担当者メールアドレス
         */
        $this->add(array(
            'name' => 'staffMail',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'staffMail',
                'maxlength' => 80,
                'placeholder' => '担当者メールアドレス',
                'class' => 'form-control',
            ),
        ));

        /*
         * パスワード
         */
         $this->add(array(
            'name' => 'password',
            'type' => 'Password',
            'attributes' => array(
                'id' => 'password',
                'placeholder' => 'パスワード',
                'class' => 'form-control',
            ),
        ));
         
        /*
         * パスワード（確認用）
         */
         $this->add(array(
            'name' => 'passwordRepeat',
            'type' => 'Password',
            'attributes' => array(
                'id' => 'passwordRepeat',
                'placeholder' => 'パスワード（確認用）',
                'class' => 'form-control',
            ),
        ));

        /*
         * WEBサイトURL
         */
         $this->add(array(
            'name' => 'webUrl',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'webUrl',
                'maxlength' => 150,
                'placeholder' => 'http://',
                'class' => 'form-control',
            ),
        ));

        /*
         * オーナー名
         */
         $this->add(array(
            'name' => 'ownerName',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'ownerName',
                'maxlength' => 80,
                'placeholder' => 'オーナー名',
                'class' => 'form-control',
            ),
        ));  
         
        /*
         * 権限(システム管理者のみ）
         */
         $this->add(array(
            'name' => 'roleName',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'roleName',
                'maxlength' => 80,
                'placeholder' => '権限(システム管理者のみ）',
                'class' => 'form-control',
            ),
        ));   
         
        /*
         * ユーザー名
         */
         $this->add(array(
            'name' => 'userName',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'userName',
                'maxlength' => 80,
                'placeholder' => 'ユーザー名',
                'class' => 'form-control',
            ),
        )); 
        
        /*
         * ユーザーID
         */
         $this->add(array(
            'name' => 'userID',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'userID',
                'maxlength' => 80,
                'placeholder' => 'ユーザーID',
                'class' => 'form-control',
            ),
        ));    
         
         /*
         * メールアドレス
         */
        $this->add(array(
            'name' => 'mailAddress',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'mailAddress',
                'maxlength' => 80,
                'placeholder' => 'メールアドレス',
                'class' => 'form-control',
            ),
        )); 
        
        /*
         * 権限
         */
         $this->add(array(
            'name' => 'roles',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'roles',
                'class' => 'form-control',
            ),
            'options' => array(
                //'label' => '表示順位',
                'value_options' => array(
                    ''  => '権限を選択してください',
                    CommonConstant::ROLE_GENERAL_USER => '一般ユーザー',
                    CommonConstant::ROLE_CONTENT_OWNER => 'コンテンツオーナー',
                    CommonConstant::ROLE_CHANNEL_OWNER => 'チャネルオーナー',
                    CommonConstant::ROLE_SYSTEM => 'システム管理者',
                ),
            )
        ));   

        //個人情報保護方針
        $this->add(array(
            'name' => 'privacyPolicy',
            'type' => 'TextArea',
            'attributes' => array(
                'id' => 'privacyPolicy',
                'col' => 200,
                'row' => 50,
                'maxlength' => 1000,
                'class' => 'form-control area-policy',
            ),
        ));
        
        /*
         * 個人情報保護方針に同意する
         */
        $this->add(array(
            'name' => 'chkPrivacyPolicy',
            'type' => 'Zend\Form\Element\Checkbox',           
            'options' => array(
                'id' => 'chkPrivacyPolicy',
                //'use_hidden_element' => true,
                'checked_value' => 'checked',
                'unchecked_value' => 'uncheck',
            ),
            'attributes' => array(
                'value' => 'checked',
            ),
        ));
        
        /*
         * 登録ボタン
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
