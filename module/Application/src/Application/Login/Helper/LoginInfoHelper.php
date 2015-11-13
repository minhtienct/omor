<?php

namespace Application\Login\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoginInfoHelper extends AbstractHelper
{

    /**
     * @var \Application\Login\Service\LoginInfoService 
     */
    protected $loginInfoSv;

    /**
     * @var \Omolink\Master\Service\MstSalesService 
     */
    protected $mstSalesSv;
    
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $svLocator;

    public function __construct(ServiceLocatorInterface $svLocator)
    {
        $this->svLocator = $svLocator;
    }

    /**
     * Get login info service
     * @return \Application\Login\Service\LoginInfoService
     */
    private function getLoginInfoSv()
    {
        if (!$this->loginInfoSv) {
            $this->loginInfoSv = $this->svLocator->get('LoginInfoService');
        }
        return $this->loginInfoSv;
    }
    
    /**
     * 
     * @return string
     */
    public function getLoggedUserRole()
    {
        return $this->getLoginInfoSv()->getLoggedUserRole();
    }
    
    /**
     * 
     * @return string
     */
    public function getUserName()
    {
        return $this->getLoginInfoSv()->getUserName();
    }

    /**
     * Get user permission 
     * @return int
     */
    public function getPermission()
    {
        return $this->getLoginInfoSv()->getPermission();
    }
    
    /**
     * Get user id 
     * @return string
     */
    public function getUserId()
    {
        return $this->getLoginInfoSv()->getUserId();
    }    
    /**
     * get logon sales
     * @return type
     */
    public function getLogoSales()
    {
        
        return null;
    }
    /**
     * Get login facility name
     * @return null|string
     */
    public function getFacilityName()
    {
        return $this->getLoginInfoSv()->getFacilityName();
    }
    
    /*
     * 
     */
    public function getChannelMenu()
    {
        return $this->getLoginInfoSv()->isRegisteChannelMenu();        
    }
    
    /**
     * Get login facility id
     * @return null|string
     */
    public function getFacilityId()
    {
        return $this->getLoginInfoSv()->getFacilityId();
    }
    
    /**
     * Get login facility weekday_valid_flg
     * @return type
     */
    public function getWeekdayValidFlg()
    {
        return $this->getLoginInfoSv()->getWeekdayValidFlg();
    }
    /**
     * Get login owner name
     * @return null|string
     */
    public function getOwnerName()
    {
        return $this->getLoginInfoSv()->getOwnerName();
    }
}
