<?php

namespace Application\Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class BackEndController extends AbstractActionController
{

    /**
     * @var \Application\Login\Service\LoginInfoService
     */
    protected $loginInfoSv;

    /**
     *
     * @var Application\Application\Service\SystemLog;
     */
    protected $logSv;

    /**
     * @var \Application\Login\Service\LoginService
     */
    protected $loginSv;

    /**
     * Get login info service
     * @return \Application\Login\Service\LoginInfoService
     */
    public function getLoginInfoSv()
    {
        if (!isset($this->loginInfoSv)) {
            $this->loginInfoSv = $this->getServiceLocator()->get('LoginInfoService');
        }
        return $this->loginInfoSv;
    }

    protected function getViewHelper($helperName)
    {
        return $this->getServiceLocator()->get('viewhelpermanager')->get($helperName);
    }

    /**
     * 
     * @return \Application\Application\Service\SystemLog
     */
    public function getLogService()
    {
        if (!$this->logSv) {
            $this->logSv = $this->getServiceLocator()->get('SystemLogService');
        }
        return $this->logSv;
    }

    /**
     * Get login service
     * @return \Application\Login\Service\LoginService
     */
    public function getLoginService()
    {
        if (!$this->loginSv) {
            $this->loginSv = $this->getServiceLocator()->get('LoginService');
        }
        return $this->loginSv;
    }
}
