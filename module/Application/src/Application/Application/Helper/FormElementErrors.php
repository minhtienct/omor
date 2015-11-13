<?php

namespace Application\Application\Helper;

use Zend\Form\View\Helper\FormElementErrors as OriginalFormElementErrors;

class FormElementErrors extends OriginalFormElementErrors
{

    protected $messageCloseString = '</span></div>';
    protected $messageOpenFormat = '<div class="messages_form_error"><span>';
    protected $messageSeparatorString = '</span><span>';
}
