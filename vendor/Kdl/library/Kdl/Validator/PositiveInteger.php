<?php

namespace Kdl\Validator;

use Traversable;
use Zend\Validator\Regex;

class PositiveInteger extends Regex
{

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::INVALID => "Invalid type given. String, integer or float expected",
        self::NOT_MATCH => '１以上の整数を入力してください',
        self::ERROROUS => "There was an internal error while using the pattern '%pattern%'",
    ];

    /**
     * Regular expression pattern alphanumeric
     *
     * @var string
     */
    protected $pattern = '/^[\-0-9]+$/u';

    /**
     * Sets validator options
     *
     * @param  string|Traversable                 $option
     * @throws Exception\InvalidArgumentException On missing 'pattern' parameter
     */
    public function __construct($option = [])
    {

        if (!is_array($option)) {
            $option = [];
        }
        $option['pattern'] = $this->pattern;

        parent::__construct($option);
    }
}
