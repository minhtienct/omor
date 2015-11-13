<?php

namespace Application\Login\Form;

use Zend\InputFilter\InputFilter;

class BrowseInputFilter extends InputFilter
{

    public function __construct()
    {
        /*
         * Security CSRF
         */
        $this->add(array(
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

        /**
         * 現在の閲覧パスワード
         */
        $this->add(array(
            'name' => 'oldPassword',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'Kdl\Validator\NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => array(
                        'subject' => '現在の閲覧パスワード',
                    ),
                ),
                array(
                    'name' => 'Kdl\Validator\Alphanumeric',
                    'break_chain_on_failure' => true,
                ),
                array(
                    'name' => 'Kdl\Validator\StringLength',
                    'options' => array(
                        'min' => 8,
                        'max' => 20,
                        'subject' => '現在の閲覧パスワード'
                    ),
                ),
            ),
        ));

        /**
         * 新パスワードの入力
         */
        $this->add(array(
            'name' => 'newPassword',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'Kdl\Validator\NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => array(
                        'subject' => '新パスワードの入力',
                    ),
                ),
                array(
                    'name' => 'Kdl\Validator\Alphanumeric',
                    'break_chain_on_failure' => true,
                ),
                array(
                    'name' => 'Kdl\Validator\StringLength',
                    'options' => array(
                        'min' => 8,
                        'max' => 20,
                        'subject' => '新パスワードの入力'
                    ),
                ),
            ),
        ));

        /**
         * 新パスワードの確認
         */
        $this->add(array(
            'name' => 'confirmPassword',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'Kdl\Validator\NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => array(
                        'subject' => '新パスワードの確認',
                    ),
                ),
                array(
                    'name' => 'Callback',
                    'options' => array(
                        'callback' => function ($value, $context) {
                            $isValid = strcmp($value, $context['newPassword']) == 0;
                            return $isValid;
                        },
                        'messages' => array(
                            \Zend\Validator\Callback::INVALID_VALUE => 'APPLICATION_007',
                        ),
                    ),
                ),
            ),
        ));
        
        /**
         * 閲覧パスワード有効時間(分)
         */
        $this->add(array(
            'name' => 'browseValidMinute',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'Kdl\Validator\NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => array(
                        'subject' => '閲覧パスワード有効時間(分)',
                    ),
                ),
                array(
                    'name' => 'Kdl\Validator\PositiveInteger',
                    'break_chain_on_failure' => true,
                ),
                array(
                    'name' => 'Kdl\Validator\StringLength',
                    'options' => array(
                        'min' => 0,
                        'max' => 4,
                        'subject' => '閲覧パスワード有効時間(分)'
                    ),
                ),
                array(
                    'name' => 'Callback',
                    'options' => array(
                        'callback' => function ($value) {
                            $isValid = $value > 0;
                            return $isValid;
                        },
                        'messages' => array(
                            \Zend\Validator\Callback::INVALID_VALUE => 'APPLICATION_014',
                        ),
                    ),
                ),
            ),
        ));
    }
}
