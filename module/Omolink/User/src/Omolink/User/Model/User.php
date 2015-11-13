<?php

namespace Omolink\User\Model;

use Application\Application\Constant\CommonConstant;
use Exception;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Csrf;
use Zend\Validator\Digits;
use Zend\Validator\Identical;
use Zend\Validator\Regex;

class User
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
    public function getInputFilterInsert()
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
             * 企業・団体名
             */
            $inputFilter->add(array(
                'name' => 'companyName',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Kdl\Validator\NotEmpty',
                        'options' => array(
                            'subject' => '企業・団体名'
                        ),
                    ),
                    array(
                        'name' => 'Kdl\Validator\StringLength',
                        'options' => array(
                            'max' => 50,
                            'subject' => '企業・団体名'
                        ),
                    ),
                ),
            ));

            /*
             * 企業・団体名（カナ）
             */
            $inputFilter->add(array(
                'name' => 'companyNameKana',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Kdl\Validator\NotEmpty',
                        'options' => array(
                            'subject' => '企業・団体名（カナ）'
                        ),
                    ),
                    array(
                        'name' => 'Kdl\Validator\StringLength',
                        'options' => array(
                            'max' => 50,
                            'subject' => '企業・団体名（カナ）'
                        ),
                    ),
                ),
            ));

            /*
             * 郵便番号
             */
            $inputFilter->add(array(
                'name' => 'postalCode',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Kdl\Validator\NotEmpty',
                        'options' => array(
                            'subject' => '郵便番号'
                        ),
                    ),
                    array(
                        'name' => 'Kdl\Validator\StringLength',
                        'options' => array(
                            'max' => 13,
                            'subject' => '郵便番号'
                        ),
                    ),
                ),
            ));

            /*
             * 住所
             */
            $inputFilter->add(array(
                'name' => 'address',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Kdl\Validator\NotEmpty',
                        'options' => array(
                            'subject' => '住所'
                        ),
                    ),
                    array(
                        'name' => 'Kdl\Validator\StringLength',
                        'options' => array(
                            'max' => 50,
                            'subject' => '住所'
                        ),
                    ),
                ),
            ));            

            /*
             * 電話番号
             */
            $inputFilter->add(array(
                'name' => 'telNumber',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Kdl\Validator\NotEmpty',
                        'options' => array(
                            'subject' => '電話番号'
                        ),
                    ),
                    array(
                        'name' => 'Kdl\Validator\StringLength',
                        'options' => array(
                            'max' => 50,
                            'subject' => '電話番号'
                        ),
                    ),
                ),
            ));        

            /*
             * 担当者氏名
             */
            $inputFilter->add(array(
                'name' => 'staffName',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Kdl\Validator\NotEmpty',
                        'options' => array(
                            'subject' => '担当者氏名'
                        ),
                    ),
                    array(
                        'name' => 'Kdl\Validator\StringLength',
                        'options' => array(
                            'max' => 50,
                            'subject' => '担当者氏名'
                        ),
                    ),
                ),
            ));                 

            /*
             * 担当者メールアドレス
             */
            $inputFilter->add(array(
                'name' => 'staffMail',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Kdl\Validator\NotEmpty',
                        'options' => array(
                            'subject' => '担当者メールアドレス'
                        ),
                       'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Kdl\Validator\StringLength',
                        'options' => array(
                            'max' => 50,
                            'subject' => '担当者メールアドレス'
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/',
                            'messages' => array(
                                Regex::NOT_MATCH => 'APPLICATION_013',
                            ),
                        ),
                    ),                    
                ),
            ));                 

            /*
             * パスワード
             */
            $inputFilter->add(array(
                'name' => 'password',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Kdl\Validator\NotEmpty',
                        'options' => array(
                            'subject' => 'パスワード'
                        ),
                    ),
                    array(
                        'name' => 'Kdl\Validator\StringLength',
                        'options' => array(
                            'min' => 8,
                            'subject' => 'パスワード'
                        ),
                    ),                   
                ),
            ));
            
            /*
             * パスワード
             */
            $inputFilter->add(array(
                'name' => 'passwordRepeat',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Kdl\Validator\NotEmpty',
                        'options' => array(
                            'subject' => 'パスワード'
                        ),
                    ),
                    array(
                        'name' => 'Kdl\Validator\StringLength',
                        'options' => array(
                            'min' => 8,
                            'subject' => 'パスワード（確認用）'
                        ),
                    ),
                    array(
                        'name' => 'Identical',
                        'options' => array(
                            'token' => 'password'
                        )
                    )                   
                ),
            ));
            
            /*
             * オーナー名
             */
            $inputFilter->add(array(
                'name' => 'ownerName',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Kdl\Validator\NotEmpty',
                        'options' => array(
                            'subject' => 'オーナー名'
                        ),
                    ),
                    array(
                        'name' => 'Kdl\Validator\StringLength',
                        'options' => array(
                            'max' => 50,
                            'subject' => 'オーナー名'
                        ),
                    ),                    
                ),
            )); 
            
            /*
            * 権限
            */
           $inputFilter->add(array(
               'name' => 'roles',
               'required' => false,
           ));
            
            /*
             * 個人情報保護方針に同意する
             */
            $inputFilter->add(array(
                'name' => 'chkPrivacyPolicy',
                'required' => true,
//                'validators' => array(
//                    array(
//                        'name' => 'Identical',//'Digits',
//                        'options' => array(
//                            'token' => '1',
//                            'messages' => array(
//                                //Digits::NOT_DIGITS => 'チェックしてください',
//                                Identical::NOT_SAME => 'チェックしてください',
//                            ),
//                        ),
//                    ),
//                ),
            
            ));  

            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }

    /*
     * インプットフィルタデータの取得
     */
    public function getInputFilter($permission, $loginPermission)
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

            if ($loginPermission == CommonConstant::ROLE_SYSTEM)
            {
                /*
                 * 権限
                 */
                $inputFilter->add(array(
                    'name' => 'roles',
                    'required' => true,
                ));                                
            }
            
            if ($permission == CommonConstant::ROLE_REGISTER_USER ||
                $permission == CommonConstant::ROLE_GENERAL_USER)
            {
                /*
                 * ユーザー名
                 */
                $inputFilter->add(array(
                    'name' => 'userName',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'Kdl\Validator\NotEmpty',
                            'options' => array(
                                'subject' => 'ユーザー名'
                            ),
                        ),
                        array(
                            'name' => 'Kdl\Validator\StringLength',
                            'options' => array(
                                'max' => 80,
                                'subject' => 'ユーザー名'
                            ),
                        ),
                    ),
                ));                 

                /*
                 * メールアドレス
                 */
                $inputFilter->add(array(
                    'name' => 'mailAddress',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'Kdl\Validator\NotEmpty',
                            'options' => array(
                                'subject' => 'メールアドレス'
                            ),
                           'break_chain_on_failure' => true,
                        ),
                        array(
                            'name' => 'Kdl\Validator\StringLength',
                            'options' => array(
                                'max' => 80,
                                'subject' => 'メールアドレス'
                            ),
                            'break_chain_on_failure' => true,
                        ),
                        array(
                            'name' => 'Regex',
                            'options' => array(
                                'pattern' => '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/',
                                'messages' => array(
                                    Regex::NOT_MATCH => 'APPLICATION_013',
                                ),
                            ),
                        ),                    
                    ),
                ));
            }
            
            if ($permission == CommonConstant::ROLE_CHANNEL_OWNER ||
                $permission == CommonConstant::ROLE_CONTENT_OWNER ||
                $permission == CommonConstant::ROLE_SYSTEM)
            {
                /*
                 * 企業・団体名
                 */
                $inputFilter->add(array(
                    'name' => 'companyName',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'Kdl\Validator\NotEmpty',
                            'options' => array(
                                'subject' => '企業・団体名'
                            ),
                        ),
                        array(
                            'name' => 'Kdl\Validator\StringLength',
                            'options' => array(
                                'max' => 50,
                                'subject' => '企業・団体名'
                            ),
                        ),
                    ),
                ));

                /*
                 * 企業・団体名（カナ）
                 */
                $inputFilter->add(array(
                    'name' => 'companyNameKana',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'Kdl\Validator\NotEmpty',
                            'options' => array(
                                'subject' => '企業・団体名（カナ）'
                            ),
                        ),
                        array(
                            'name' => 'Kdl\Validator\StringLength',
                            'options' => array(
                                'max' => 50,
                                'subject' => '企業・団体名（カナ）'
                            ),
                        ),
                    ),
                ));

                /*
                 * 郵便番号
                 */
                $inputFilter->add(array(
                    'name' => 'postalCode',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'Kdl\Validator\NotEmpty',
                            'options' => array(
                                'subject' => '郵便番号'
                            ),
                        ),
                        array(
                            'name' => 'Kdl\Validator\StringLength',
                            'options' => array(
                                'max' => 13,
                                'subject' => '郵便番号'
                            ),
                        ),
                    ),
                ));

                /*
                 * 住所
                 */
                $inputFilter->add(array(
                    'name' => 'address',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'Kdl\Validator\NotEmpty',
                            'options' => array(
                                'subject' => '住所'
                            ),
                        ),
                        array(
                            'name' => 'Kdl\Validator\StringLength',
                            'options' => array(
                                'max' => 50,
                                'subject' => '住所'
                            ),
                        ),
                    ),
                ));            

                /*
                 * 電話番号
                 */
                $inputFilter->add(array(
                    'name' => 'telNumber',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'Kdl\Validator\NotEmpty',
                            'options' => array(
                                'subject' => '電話番号'
                            ),
                        ),
                        array(
                            'name' => 'Kdl\Validator\StringLength',
                            'options' => array(
                                'max' => 50,
                                'subject' => '電話番号'
                            ),
                        ),
                    ),
                ));        

                /*
                 * 担当者氏名
                 */
                $inputFilter->add(array(
                    'name' => 'staffName',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'Kdl\Validator\NotEmpty',
                            'options' => array(
                                'subject' => '担当者氏名'
                            ),
                        ),
                        array(
                            'name' => 'Kdl\Validator\StringLength',
                            'options' => array(
                                'max' => 50,
                                'subject' => '担当者氏名'
                            ),
                        ),
                    ),
                ));                 

                /*
                 * 担当者メールアドレス
                 */
                $inputFilter->add(array(
                    'name' => 'staffMail',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'Kdl\Validator\NotEmpty',
                            'options' => array(
                                'subject' => '担当者メールアドレス'
                            ),
                           'break_chain_on_failure' => true,
                        ),
                        array(
                            'name' => 'Kdl\Validator\StringLength',
                            'options' => array(
                                'max' => 50,
                                'subject' => '担当者メールアドレス'
                            ),
                            'break_chain_on_failure' => true,
                        ),
                        array(
                            'name' => 'Regex',
                            'options' => array(
                                'pattern' => '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/',
                                'messages' => array(
                                    Regex::NOT_MATCH => 'APPLICATION_013',
                                ),
                            ),
                        ),                    
                    ),
                ));                 

                /*
                 * パスワード
                 */
                /*$inputFilter->add(array(
                    'name' => 'password',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'Kdl\Validator\NotEmpty',
                            'options' => array(
                                'subject' => 'パスワード'
                            ),
                        ),
                        array(
                            'name' => 'Kdl\Validator\StringLength',
                            'options' => array(
                                'min' => 8,
                                'subject' => 'パスワード'
                            ),
                        ),                   
                    ),
                ));*/

                /*
                 * オーナー名
                 */
                $inputFilter->add(array(
                    'name' => 'ownerName',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'Kdl\Validator\NotEmpty',
                            'options' => array(
                                'subject' => 'オーナー名'
                            ),
                        ),
                        array(
                            'name' => 'Kdl\Validator\StringLength',
                            'options' => array(
                                'max' => 50,
                                'subject' => 'オーナー名'
                            ),
                        ),                    
                    ),
                ));                 
                
            } 
            
            /*
             * 個人情報保護方針に同意する
             */
            $inputFilter->add(array(
                'name' => 'chkPrivacyPolicy',
                'required' => false,            
            ));  

            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }

}
