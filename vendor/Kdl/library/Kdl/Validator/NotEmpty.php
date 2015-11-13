<?php
namespace Kdl\Validator;

class NotEmpty extends \Zend\Validator\NotEmpty
{
     /**
     * @var array
     */
    protected $messageVariables = array(
        'subject' => array('options' => 'subject'),
    );

    protected $options = array(
        'subject'      => ''
    );

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::IS_EMPTY => "%subject% is required and can't be empty",
        self::INVALID  => "Invalid type given. String, integer, float, boolean or array expected",
    );
}