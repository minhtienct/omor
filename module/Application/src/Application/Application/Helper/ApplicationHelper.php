<?php

namespace Application\Application\Helper;

use Application\Application\Constant\CommonConstant;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;

class ApplicationHelper extends AbstractHelper
{

    /**
     * @var ServiceLocatorInterface 
     */
    protected $svLocator;

    public function __construct(ServiceLocatorInterface $svLocator)
    {
        $this->svLocator = $svLocator;
    }

    /**
     * Get head title for page
     * @param string $requestName
     */
    public function getHeadTitle($requestName)
    {
        $config = $this->svLocator->get('Config');

        if (isset($config['head_titles'])) {
            if (isset($config['head_titles'][$requestName])) {
                return $config['head_titles'][$requestName];
            }
        }
        return null;
    }
    

}
