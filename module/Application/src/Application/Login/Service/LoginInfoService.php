<?php

namespace Application\Login\Service;

use Application\Application\Constant\CommonConstant;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\ServiceLocatorInterface;
use Kdl\Api\Run as apiRun;

class LoginInfoService
{

    /**
     * @var ServiceLocatorInterface 
     */
    protected $svLocator;

    /**
     * @var AuthenticationService 
     */
    protected $authService;

    public function __construct(ServiceLocatorInterface $svLocator)
    {
        $this->svLocator = $svLocator;
    }

    /**
     * Get authentication service
     * @return AuthenticationService
     */
    private function getAuthService()
    {
        if (!$this->authService) {
            $this->authService = $this->svLocator->get('AuthService');
        }
        return $this->authService;
    }

    /**
     * Get logged in info value
     * @param string $key
     * @param string $subKey
     * @return type
     */
    public function getLoginInfoValue($key, $subKey = null)
    {
        if ($this->getAuthService()->hasIdentity()) {
            $idStorage = $this->authService->getIdentity();
            if (is_null($subKey)) {
                if (isset($idStorage['ACCOUNT'][$key])) {
                    return $idStorage['ACCOUNT'][$key];
                }
            } else if (isset($idStorage['ACCOUNT'][$key][$subKey])) {
                return $idStorage['ACCOUNT'][$key][$subKey];
            }
        }
        return null;
    }
    
    /**
     * Get logged user role
     * @return string|null
     */
    public function getLoggedUserRole()
    {
        $userAclPermission = $this->getLoginInfoValue('permission');
        switch ($userAclPermission) {
            case CommonConstant::ROLE_REGISTER_USER;
                return CommonConstant::ROLE_REGISTER_USER;
            case CommonConstant::ROLE_GENERAL_USER;
                return CommonConstant::ROLE_GENERAL_USER;
            case CommonConstant::ROLE_CONTENT_OWNER;
                return CommonConstant::ROLE_CONTENT_OWNER;
            case CommonConstant::ROLE_CHANNEL_OWNER;
                return CommonConstant::ROLE_CHANNEL_OWNER;
            case CommonConstant::ROLE_SYSTEM;
                return CommonConstant::ROLE_SYSTEM;
            default:
                break;
        }
      
        return null;
    }

    /**
     * Get string user permission
     * @return string|null
     */
    public function getStringUserPermission()
    {
        $userAclPermission = $this->getLoginInfoValue('permission');
        switch ($userAclPermission) {
            case CommonConstant::ROLE_REGISTER_USER;
                return CommonConstant::ROLE_REGISTER_USER;
            case CommonConstant::ROLE_GENERAL_USER;
                return CommonConstant::ROLE_GENERAL_USER;
            case CommonConstant::ROLE_CONTENT_OWNER;
                return CommonConstant::ROLE_CONTENT_OWNER;
            case CommonConstant::ROLE_CHANNEL_OWNER;
                return CommonConstant::ROLE_CHANNEL_OWNER;
            case CommonConstant::ROLE_SYSTEM;
                return CommonConstant::ROLE_SYSTEM;
            default:
                break;
        }
      
        return null;
    }
    
    /*
     * Get user name
     * @return string
     */
    public function getUserName() {
        return $this->getLoginInfoValue('user_name');
    }
    
    /*
     * Get email address
     * @return string
     */
    public function getEmailAddress() {
        return $this->getLoginInfoValue('mail_address');
    }

    /*
     * Get user id
     * @return string
     */
    public function getUserId() {
        return $this->getLoginInfoValue('user_id');
    }    
    /**
     * Get token
     * @return string
     */
    public function getToken()
    {
        return $this->getLoginInfoValue('token');
    }
    
    /**
     * Get permission
     * @return int
     */
    public function getPermission()
    {
        return $this->getLoginInfoValue('permission');
    }
 
   /**
    * 
    * @param type $pass
    */
    public function getEncryptPassword($pass)
    {
        return md5($pass);
    }
    
    /*
     * チャネルメニューが表示/非表示の判断
     */
    public function isRegisteChannelMenu()
    {
        $channelId = 0;
        $token = $this->getToken();
        $userId = $this->getUserId();

        $params = array();
        $params['token'] = $token;
        $params['channel_owner_id'] = $userId;
        
        //チャネル検索のURL取得
        $urlChannelSearch = apiRun::getApiUrl(CommonConstant::URL_CHANNEL_SEARCH);
        //API CH012と連結し、実装する
        $resApiCH012 = apiRun::runURL($urlChannelSearch, $params);
        if (isset($resApiCH012['status']) && $resApiCH012['status'] == CommonConstant::STATUS_SUCCESS) {
            if ($resApiCH012['count'] > 0) {
                $channelId = $resApiCH012['list'][0];
            }
        }
        
        return $channelId;        
    }
}
