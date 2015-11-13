<?php

namespace Application\Login\Controller;

use Application\Application\Constant\CommonConstant;
use Application\Application\Controller\BackEndController;
use Application\Login\Form\LoginForm;
use Application\Login\Model\Login;
use Kdl\Api\Run as apiRun;
use Zend\Authentication\AuthenticationService;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use Zend\View\Model\ViewModel;

class LoginController extends BackEndController
{

    /**
     * @var AuthenticationService
     */
    protected $authService;
    
    /**
     * Content data of authentication result
     * @var array 
     */
    protected $authContent = array();

    /**
     * ログイン情報
     * @var LoginInfoService 
     */
    protected $loginInfoSv;
 
    /**
     * Create AuthenticationService if not exist yet
     * @return AuthenticationService
     */
    private function getAuthService()
    {
        if (!$this->authService) {
            $this->authService = $this->getServiceLocator()->get('AuthService');
        }
        return $this->authService;
    }

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
    

    protected $loginHelper;
    
    /**
     * Get login info service
     * @return \Application\Login\Service\LoginInfoService
     */
    public function getLoginHelper()
    {
        if (!isset($this->loginHelper)) {
            $this->loginHelper = $this->getServiceLocator()->get('LoginInfoHelper');
        }
        return $this->loginHelper;
    }
    
    
    /**
     * Login action
     * @return ViewModel
     */
    public function loginViewAction()
    {
        if ($this->getAuthService()->hasIdentity()) {
            return $this->redirect()->toRoute('user');
        }
       
        $loginForm = new LoginForm();
        $login = new Login();
        $errorMessage = null;
        $loginSession = new Container('login');

        $reguest = $this->getRequest();
        if ($reguest->isPost()) {
            $loginForm->setInputFilter($login->getInputFilter());
            $loginForm->setData($reguest->getPost());

            if ($loginForm->isValid()) {
                $data = $loginForm->getData();

                $params = array();
                $params['mail_address'] = $data['login_name'];
                $params['password'] = $data['login_password'];
                
                //ログインのURL取得
                $urlLogin = apiRun::getApiUrl(CommonConstant::URL_LOGIN);
                //API AU-014と連結し、実装する
                $resApiAU014 = apiRun::runURL($urlLogin, $params);
                
                if (isset($resApiAU014['status']) && $resApiAU014['status'] == CommonConstant::STATUS_SUCCESS) {                  
                    $paramsAU002 = array();
                    $paramsAU002['token'] = $resApiAU014['token'];
                    //echo 'token' . $resApiAU014['token']; die;
                    
                    //ユーザー情報のURL取得
                    $urlUserInfo = apiRun::getApiUrl(CommonConstant::URL_USER_INFO);
                    //API AU-002と連結し、実装する
                    $resApiAU002 = apiRun::runURL($urlUserInfo, $paramsAU002);
                
                    if (isset($resApiAU002['status']) && $resApiAU002['status'] == CommonConstant::STATUS_SUCCESS) {
                        $loginSession->loginInfo = $resApiAU002;
                        $resApiAU002['token'] = $paramsAU002['token'];
                        
                        //check login
                        if ($resApiAU002['permission'] == CommonConstant::ROLE_REGISTER_USER ||
                            $resApiAU002['permission'] == CommonConstant::ROLE_GENERAL_USER)
                        {
                            $errorMessage = 'APPLICATION_002';
                        }
                        else {
                            $chkLogin = $this->getLoginService()->CheckLogin($resApiAU002);
                            if ($chkLogin) {
                                return $this->redirect()->toRoute('content');                       
                            }
                        }
                    }
                    else if($resApiAU002['status'] == CommonConstant::STATUS_ERROR) {
                        $errorMessage = $resApiAU002['errmsg'];    
                    }
                }
                else if ($resApiAU014['status'] == CommonConstant::STATUS_ERROR) {
                    $errorMessage = $resApiAU014['errmsg'];
                }
            }
        }
        
        return new ViewModel(array('loginFrm' => $loginForm, 'errorMessage' => $errorMessage));
    }

    /**
     * Logout action
     * @return ViewModel
     */
    public function logoutAction()
    {
        $auth = $this->getAuthService();
        $token = $this->getLoginInfoSv()->getToken();

        //ログアウトのURL取得
        $urlLogout = apiRun::getApiUrl(CommonConstant::URL_LOGOUT);
        //API AU015と連結し、実装する
        $resApiAU015 = apiRun::runURL($urlLogout, array('token' => $token));
        if (isset($resApiAU015['status']) && $resApiAU015['status'] == CommonConstant::STATUS_SUCCESS) {
            if ($auth->hasIdentity()) {
                $auth->clearIdentity();
                $sessionManager = new SessionManager();
                $sessionManager->forgetMe();
                session_destroy();
            }
            return $this->redirect()->toRoute('home');
        }
        else if ($resApiAU015['status'] == CommonConstant::STATUS_ERROR) {
            //TODO: 後で、処理を追加する予定です。
        }

//        if ($auth->hasIdentity()) {
//            $auth->clearIdentity();
//            $sessionManager = new SessionManager();
//            $sessionManager->forgetMe();
//        }

        return $this->redirect()->toRoute('home');
    }
}
