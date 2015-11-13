<?php

namespace Kdl\Validator;

use Traversable;
use Zend\Validator\Regex;

class Numeric extends Regex
{

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::INVALID => "Invalid type given. String, integer or float expected",
        self::NOT_MATCH => '半角数値で入力してください',
        self::ERROROUS => "There was an internal error while using the pattern '%pattern%'",
    ];

    /**
     * Regular expression pattern alphanumeric
     *
     * @var string
     */
    protected $pattern = '/^[\-0-9.]+$/u';

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
