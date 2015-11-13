<?php

namespace Application\Login\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterAwareInterface;

class Login implements InputFilterAwareInterface
{
    /*
     * Login input filter
     */
    protected $inputFilter;

    /*
     * 
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            // Login name filter
            $inputFilter->add(array(
                'name' => 'login_name',
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
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'APPLICATION_004'
                            ),
                        ),
                    ),
//                    array(
//                        'name' => 'Kdl\Validator\StringLength',
//                        'break_chain_on_failure' => true,
//                        'options' => array(
//                            'max' => 11,
//                            'subject' => 'メールアドレス'
//                        ),
//                    ),
//                    array(
//                        'name' => 'Callback',
//                        'options' => array(
//                            'callback' => function ($value) {
//                                $isValid = preg_match("/^[a-zA-Z0-9@]+$/u",$value);
//                                return $isValid;
//                        },
//                            'messages' => array(
//                                \Zend\Validator\Callback::INVALID_VALUE => 'APPLICATION_013',
//                            ),
//                        ),
//                    ),
                ),
            ));

            // Login password filter
            $inputFilter->add(array(
                'name' => 'login_password',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'APPLICATION_003'
                            ),
                        ),
                    ),
//                    array(
//                        'name' => 'Kdl\Validator\Alphanumeric',
//                        'break_chain_on_failure' => true,
//                    ),
//                    array(
//                        'name' => 'Kdl\Validator\StringLength',
//                        'options' => array(
//                            'min' => 3,
//                            'max' => 30,
//                            'subject' => 'パスワード'
//                        ),
//                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
}
