<?php

namespace Kdl\Validator;

class UploadFile extends \Zend\Validator\File\UploadFile
{
    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INI_SIZE   => "Please upload the image of less than file size %size%",
    );

    /**
     * @var array
     */
    protected $messageVariables = array(
        'size' => array('options' => 'size'),
    );

    /**
     * @var array 
     */
    protected $options = array(
        'size' => '',
    );
}