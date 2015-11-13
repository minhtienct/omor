<?php

namespace Kdl\Form;

use Zend\Form\Form;

class BaseUpdateForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct($name);
         /*
         * Security CSRF
         */
        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf',
            'options' => array(
                'csrf_options' => array(
                    'timeout' => 8*60*60 //hour * minute * second
                )
            )
        ));
    }
}
