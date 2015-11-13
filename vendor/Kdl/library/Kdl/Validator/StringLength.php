<?php

namespace Kdl\Validator;

class StringLength extends \Zend\Validator\StringLength
{
    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID   => "Invalid type given. String expected",
        self::TOO_SHORT => "%subject%は%min%桁以上で入力してください",
        self::TOO_LONG  => "%subject%は%max%桁以内で入力してください",
    );

    /**
     * @var array
     */
    protected $messageVariables = array(
        'min' => array('options' => 'min'),
        'max' => array('options' => 'max'),
        'subject' => array('options' => 'subject')
    );

    protected $options = array(
        'min'      => 0,       // Minimum length
        'max'      => null,    // Maximum length, null if there is no length limitation
        'encoding' => 'UTF-8', // Encoding to use
        'subject'  => null
    );
}
