<?php

namespace Omolink\User\Controller;

use Application\Application\Constant\CommonConstant;
use Application\Application\Service\SystemLog;
use Application\Login\Service\LoginInfoService;
use Kdl\Api\Run as apiRun;
use Omolink\User\Form\UserForm;
use Omolink\User\Model\User;
use Omolink\User\Model\UserTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Paginator;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController
{
    //チャネルオーナー
    const TAB_INDEX_1 = 1;
    
    //正規コンテンツオーナー
    const TAB_INDEX_2 = 2;

    //一般ユーザー
    const TAB_INDEX_3 = 3;

    //登録申請中ユーザー
    const TAB_INDEX_4 = 4;

    /**
     * @var LoginInfoService 
     */
    protected $loginInfoSv;
  
    /**
     * @var \Application\Application\Service\SystemLog;
     */
    protected $logSv;
  
    /**
     * Get login info service
     * @return LoginInfoService
     */
    public function getLoginInfoSv()
    {
        if (!isset($this->loginInfoSv)) {
            $this->loginInfoSv = $this->getServiceLocator()->get('LoginInfoService');
        }
        return $this->loginInfoSv;
    }
    
    /**
     * @return SystemLog
     */
    public function getLogService()
    {
        if (!$this->logSv) {
            $this->logSv = $this->getServiceLocator()->get('SystemLogService');
        }
        return $this->logSv;
    }

    /*
     * ユーザー一覧情報の取得アクション
     */
    public function listviewAction()
    {
        $errorMessage = null;
        $userTable = new UserTable();

        $config = $this->getServiceLocator()->get('config');
        $itemCountPerPage = $config['paging']['rowsPerPage'];
        $pageRange = $config['paging']['pageRange'];
        $page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
        $orderBy = $this->params()->fromRoute('order_by');
        $sort = $this->params()->fromRoute('sort');
        $offset = (($page ? $page : 1)- 1) * $itemCountPerPage;
        
        $objListChannelOwner = $this->getListDataUser(CommonConstant::ROLE_CHANNEL_OWNER, $offset);
        if (count($objListChannelOwner['list_user_info']) > 0) {
            $userTable->setUserData($objListChannelOwner['list_user_info'], $objListChannelOwner['total_count']);
        }
        
        if (!is_null($objListChannelOwner['errMsg'])) {
            $errorMessage = $objListChannelOwner['errMsg'];
        }
        
        $paginator = new Paginator($userTable);
        $paginator->setCurrentPageNumber($page)
                  ->setItemCountPerPage($itemCountPerPage)
                  ->setPageRange($pageRange);    
 
        return new ViewModel(array(
            'paginator' => $paginator,
            'order_by' => $orderBy ? $orderBy : 'name',
            'order' => $sort ? $sort : 'ASC',
            'data' => $userTable->getItems(($page - 1) * $itemCountPerPage, $itemCountPerPage),
            'errorMessage' => $errorMessage,
        ));           
    }
    
    /*
     * 新規ユーザーを登録するアクション
     */
    public function insertAction()
    {
        if (!$this->getServiceLocator()->get('AuthService')->hasIdentity()) {
            $layout = $this->layout();
            $layout->setTemplate('layout/layout_public');
        } else {
            return $this->redirect()->toRoute('user', array('action' => 'listview'));
        }

        $form = new UserForm();
        $user = new User();
        $errorMessage = null;
        $success = null;
                
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($user->getInputFilterInsert());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                
                $params = array();
                $params['organization_name'] = $data['companyName'];
                $params['organization_kana'] = $data['companyNameKana'];
                $params['zipcode'] = (int)$data['postalCode'];
                $params['address'] = $data['address'];
                $params['phone'] = $data['telNumber'];
                $params['contact_name'] = $data['staffName'];
                $params['mail_address'] = $data['staffMail'];
                $params['url'] = $data['webUrl'];
                $params['owner_name'] = $data['ownerName'];
                $params['activation_flag'] = 0;
                $params['permission'] = 0;
                $params['password'] = $data['password'];    
                
                //ユーザー登録のURL取得
                $urlOwnerRegister = apiRun::getApiUrl(CommonConstant::URL_OWNER_REGISTER);
                //API AU023と連結し、実装する
                $resApiAU023 = apiRun::runURL($urlOwnerRegister, $params);
                if (isset($resApiAU023['status']) && $resApiAU023['status'] == CommonConstant::STATUS_SUCCESS) {
                    //return $this->redirect()->toRoute('user', array('action' => 'insert-completed'));
                    $success = true;
                }else if ($resApiAU023['status'] == CommonConstant::STATUS_ERROR) {
                    $errorMessage = $resApiAU023['errmsg'];
                }
            }
        }
        
        return new ViewModel(array(
            'form' => $form,
            'errorMessage' => $errorMessage,
            'success' => $success,
        ));
    }
    
    /*
     * ユーザーを登録した後、ダイアログ通知を出すアクション
     */
    public function insertCompletedAction()
    { 
        $url = $this->url()->fromRoute('user');
        
        if (!$this->getServiceLocator()->get('AuthService')->hasIdentity()) {
            $layout = $this->layout();
            $layout->setTemplate('layout/layout_public');
            $url = $this->url()->fromRoute('home');
        }

        return new ViewModel(array('returnURL' => $url));
    }
    
    /*
     * ユーザー一覧画面で改ページのアクション
     */
    public function pagingAction()
    {
        $userSession = new Container('user');
        $config = $this->getServiceLocator()->get('config');
        $itemCountPerPage = $config['paging']['rowsPerPage'];
        $pageRange = $config['paging']['pageRange'];
        $userTable = new UserTable();

        $page = $this->params()->fromQuery('page');
        $orderBy = $this->params()->fromQuery('order_by');
        $order = $this->params()->fromQuery('order');
        $tabIndex = $this->params()->fromQuery('tab');
        $offset = (($page ? $page : 1) - 1) * $itemCountPerPage;    
        
        if (isset($userSession->goToLastPage) && $userSession->goToLastPage && $page > 1) {
            $page--;
            $userSession->goToLastPage = null;
        }
        
        //チャネルオーナータブ
        if ($tabIndex == self::TAB_INDEX_1) {
            $objListChannelOwner = $this->getListDataUser(CommonConstant::ROLE_CHANNEL_OWNER, $offset);
            if (count($objListChannelOwner['list_user_info']) > 0) {
                $userTable->setUserData($objListChannelOwner['list_user_info'], $objListChannelOwner['total_count']);
            }
        }
        
        // 正規コンテンツオーナータブ
        if ($tabIndex == self::TAB_INDEX_2) {
            $objListContentOwner = $this->getListDataUser(CommonConstant::ROLE_CONTENT_OWNER, $offset);           

            if (count($objListContentOwner['list_user_info']) > 0) {
                $userTable->setUserData($objListContentOwner['list_user_info'], $objListContentOwner['total_count']);
            }
        }    
        
        // 一般ユーザータブ
        if ($tabIndex == self::TAB_INDEX_3) {
            $objListGeneralUser = $this->getListDataUser(CommonConstant::ROLE_GENERAL_USER, $offset);
           
            if (count($objListGeneralUser['list_user_info']) > 0) {
                $userTable->setUserData($objListGeneralUser['list_user_info'], $objListGeneralUser['total_count']);
            }        
        }
        
        // 登録申請中ユーザータブ
        if ($tabIndex == self::TAB_INDEX_4) {
            $objListRegisterUser = $this->getListDataUser(CommonConstant::ROLE_REGISTER_USER, $offset);
            if (count($objListRegisterUser['list_user_info']) > 0) {
                $userTable->setUserData($objListRegisterUser['list_user_info'], $objListRegisterUser['total_count']);
            }
        }         
        
        //$rows = $userTable->getItems((($page ? $page : 1) - 1) * $itemCountPerPage, $itemCountPerPage);
        $rows = $userTable->getItems(0, $itemCountPerPage);
        if ($page > 1 && count($rows) == 0) {
            $userSession->goToLastPage = true;
            return $this->pagingAction();
        }
        
        $paginator = new Paginator($userTable);  
        $paginator->setCurrentPageNumber($page)
                    ->setItemCountPerPage($itemCountPerPage)
                    ->setPageRange($pageRange);
        
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('paginationControl');
        $paginationHTML = $paginationHelper(
            $paginator,
            'Sliding',
            'paginator-slide',
            array(
                'route' => 'user',
                'action' => 'paging',
                'tableId' => 'listId',
                'order_by' => $orderBy,
                'order' => $order,
                'addParamCallback' => 'paging',
            ));

        $rowHTML = '<tr>
                        <td class="text-center"><a href="%s">%s</a></td>
                        <td class="text-left">%s</td>
                        <td class="text-left">%s</td>
                        <td class="text-left">%s</td>
                        <td class="text-center">
                            <a class="btn btn-contens-delete" uid="%s" href="javascript:void(0)" onclick="onDeleteUserClick(this);">削除</a>
                        </td>                        
                    </tr>';
        if ($tabIndex == self::TAB_INDEX_3) {
            $rowHTML = '<tr>
                            <td class="text-center"><a href="%s">%s</a></td>
                            <td class="text-left">%s</td>
                            <td class="text-left">%s</td>
                            <td class="text-center">
                                <a class="btn btn-contens-delete" uid="%s" href="javascript:void(0)" onclick="onDeleteUserClick(this);">削除</a>
                            </td>                                                    
                        </tr>';
        }

        $data = array();
        foreach ($rows as $row) {
            $url = $this->url()->fromRoute('user', array('action' => 'update', 'id' => $row['user_id']));
            if ($tabIndex == self::TAB_INDEX_3) {
                $data[] = sprintf($rowHTML, $url, $row['user_id'], $row['user_name'], $row['mail_address'], $row['user_id']);
            } else {
                $data[] = sprintf($rowHTML,  $url, $row['user_id'], $row['organization'], $row['contact'], $row['user_name'], $row['user_id']);
            }
        }
        
        $sessionUser = new Container('user');
        $message = '';
        if (isset($sessionUser->successUserDelete) && $sessionUser->successUserDelete != '') {
            $message = $sessionUser->successUserDelete;
            $sessionUser->successUserDelete = null;
        }

        $result = new JsonModel(array(
	    'data' => $data,
            'paginator' => $paginationHTML,
            'message' => $message
        ));

        return $result;
    }
    
    /*
     * ユーザー更新のアクション
     */
    public function updateAction()
    {
        $userSession = new Container('user');
        $userData = array();
        $form = new UserForm();
        $user = new User();
        
        $successMessage = null;
        $errorMessage = null;
       
        $token = $this->getLoginInfoSv()->getToken();
        $userIdFromUrl = (int)$this->params()->fromRoute('id');
        $userPermission = (int)$this->getLoginInfoSv()->getPermission();
        $parPermission = null;

        $params = array();
        $params['token'] = $token;
        $params['user_id'] = $userIdFromUrl;

        //ユーザー情報のURL取得
        $urlUserInfo = apiRun::getApiUrl(CommonConstant::URL_OWNER);
        //API AU024と連結し、実装する
        $resApiAU024 = apiRun::runURL($urlUserInfo, $params);
        if (isset($resApiAU024['status']) && $resApiAU024['status'] == CommonConstant::STATUS_SUCCESS) {
            $user_id = isset($resApiAU024['user_id']) ? $resApiAU024['user_id'] : null;
            $organization_name = isset($resApiAU024['organization_name']) ? $resApiAU024['organization_name'] : null;
            $organization_kana = isset($resApiAU024['organization_kana']) ? $resApiAU024['organization_kana'] : null;
            $zipcode = isset($resApiAU024['zipcode']) ? $resApiAU024['zipcode'] : null;
            $address = isset($resApiAU024['address']) ? $resApiAU024['address'] : null;
            $phone = isset($resApiAU024['phone']) ? $resApiAU024['phone'] : null;
            $contact_name = isset($resApiAU024['contact_name']) ? $resApiAU024['contact_name'] : null;
            $mail_address = isset($resApiAU024['mail_address']) ? $resApiAU024['mail_address'] : null;
            $url = isset($resApiAU024['url']) ? $resApiAU024['url'] : null;
            $owner_name = isset($resApiAU024['owner_name']) ? $resApiAU024['owner_name'] : null;
            $activation_flag = isset($resApiAU024['activation_flag']) ? $resApiAU024['activation_flag'] : null;
            $facebook_id = isset($resApiAU024['facebook_id']) ? $resApiAU024['facebook_id'] : null;
            $permission = isset($resApiAU024['permission']) ? $resApiAU024['permission'] : null;
            $password = isset($resApiAU024['password']) ? $resApiAU024['password'] : null;

            $userData['userId'] = $user_id;
            $userData['companyName'] = $organization_name;
            $userData['companyNameKana'] = $organization_kana;
            $userData['postalCode'] = $zipcode;
            $userData['address'] = $address;
            $userData['telNumber'] = $phone;
            $userData['staffName'] = $mail_address;
            $userData['staffMail'] = $password;
            $userData['webUrl'] = $url;
            $userData['ownerName'] = $owner_name;
            $userData['facebook_id'] = $facebook_id;
            $userData['activation_flag'] = $activation_flag;
            $userData['permission'] = $permission;
            $userData['password'] = $password;
            
            $userSession->userData = $userData;
            
            $parPermission = (int)$permission;
        }
        else if ($resApiAU024['status'] == CommonConstant::STATUS_ERROR) {
            $errorMessage = $resApiAU024['errmsg'];
        }

        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($user->getInputFilter($parPermission, $userPermission));       
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                
                //一般ユーザー又は登録申請中ユーザーの場合
                if ($parPermission == CommonConstant::ROLE_REGISTER_USER ||
                    $parPermission == CommonConstant::ROLE_GENERAL_USER)
                {
                    $parGeneralUserUpdate = array();
                    $parGeneralUserUpdate['token'] = $token;
                    $parGeneralUserUpdate['user_id'] = $userIdFromUrl;
                    
                    if ($userPermission == CommonConstant::ROLE_SYSTEM) {
                        $parGeneralUserUpdate['permission'] = (int)$data['roles'];                    
                    }
                    
                    //ユーザー更新のURL取得
                    $urlGeneralUserUpdate = apiRun::getApiUrl(CommonConstant::URL_USER_SYSTEM_UPDATE);
                    //API AU-022と連結し、実装する
                    $resApiAU022 = apiRun::runURL($urlGeneralUserUpdate, $parGeneralUserUpdate);                                     
                    if (isset($resApiAU022['status']) && $resApiAU022['status'] == CommonConstant::STATUS_SUCCESS) {
                        $successMessage = 'UPDATE_DONE';
                    }else if ($resApiAU022['status'] == CommonConstant::STATUS_ERROR) {
                        $errorMessage = $resApiAU022['errmsg'];
                    }                    
                }

                //チャネルオーナー又は正規コンテンツオーナー又はシステム管理者の場合
                if ($parPermission == CommonConstant::ROLE_CHANNEL_OWNER ||
                    $parPermission == CommonConstant::ROLE_CONTENT_OWNER ||
                    $parPermission == CommonConstant::ROLE_SYSTEM)
                {
                    $pasSystemUpdate = array();
                    $pasSystemUpdate['token'] = $token;
                    $pasSystemUpdate['user_id'] = $userIdFromUrl;
                    $pasSystemUpdate['organization_name'] = $data['companyName'];
                    $pasSystemUpdate['organization_kana'] = $data['companyNameKana'];
                    $pasSystemUpdate['zipcode'] = (int)$data['postalCode'];
                    $pasSystemUpdate['address'] = $data['address'];
                    $pasSystemUpdate['phone'] = (int)$data['telNumber'];
                    $pasSystemUpdate['contact_name'] = $data['staffName'];
                    $pasSystemUpdate['mail_address'] = $data['staffMail'];
                    $pasSystemUpdate['url'] = $data['webUrl'];
                    $pasSystemUpdate['owner_name'] = $data['ownerName'];
                    //$pasSystemUpdate['password'] = $data['password'];
                    $pasSystemUpdate['activation_flag'] = 1;
                    
                    if ($userPermission == CommonConstant::ROLE_SYSTEM) {
                        $pasSystemUpdate['permission'] = (int)$data['roles'];                    
                    }                    
                    
                    //ユーザー更新のURL取得
                    $urlSystemUserUpdate = apiRun::getApiUrl(CommonConstant::URL_OWNER_UPDATE);
                    //API AU-025と連結し、実装する
                    $resApiAU025 = apiRun::runURL($urlSystemUserUpdate, $pasSystemUpdate);                                  
                    if (isset($resApiAU025['status']) && $resApiAU025['status'] == CommonConstant::STATUS_SUCCESS) {
                        $successMessage = 'UPDATE_DONE';
                    }else if ($resApiAU025['status'] == CommonConstant::STATUS_ERROR) {
                        $errorMessage = $resApiAU025['errmsg'];
                    }                 
                    
                }
                
            }
            else
            {
                //print_r($form->getMessages());
            }
        }
        else
        {
            $form->get('userId')->setValue($user_id);
            $form->get('roles')->setValue($parPermission);

            if ($parPermission == CommonConstant::ROLE_CHANNEL_OWNER ||
                $parPermission == CommonConstant::ROLE_CONTENT_OWNER ||
                $parPermission == CommonConstant::ROLE_SYSTEM)
            {                
                $form->get('companyName')->setValue($organization_name);
                $form->get('companyNameKana')->setValue($organization_kana);
                $form->get('postalCode')->setValue($zipcode);
                $form->get('address')->setValue($address);
                $form->get('telNumber')->setValue($phone);
                $form->get('staffName')->setValue($contact_name);
                $form->get('staffMail')->setValue($mail_address);
                $form->get('password')->setValue($password);
                $form->get('webUrl')->setValue($url);
                $form->get('ownerName')->setValue($owner_name);
            }
            else if ($parPermission == CommonConstant::ROLE_GENERAL_USER ||
                     $parPermission == CommonConstant::ROLE_REGISTER_USER)
            {
                $objRes = $this->getUserInfo($userIdFromUrl);
                
                $form->get('userName')->setValue(isset($objRes['user_info']['user_name']) ? $objRes['user_info']['user_name'] : null);
                $form->get('userID')->setValue(isset($objRes['user_info']['user_id']) ? $objRes['user_info']['user_id'] : null);
                $form->get('mailAddress')->setValue(isset($objRes['user_info']['mail_address']) ? $objRes['user_info']['mail_address'] : null);
            }
            
            if ($userPermission == CommonConstant::ROLE_SYSTEM) {
                    $form->get('roleName')->setValue($permission);                
            }                
              
        }
        
        return new ViewModel(array(
            'form' => $form,
            'userData' => $userSession->userData,
            'successMessage' => $successMessage,
            'errorMessage' => $errorMessage,
            'userId' => $userIdFromUrl,
        ));
    }
    
    
    public function deleteAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $token = $this->getLoginInfoSv()->getToken();
                      
       	$sessionUser = new Container('user');
        $errorMessage = null;
        
        $params = array();
        $params['token'] = $token;
        $params['user_id'] = (int)$this->params()->fromRoute('id');   

        //TODO
        //コンテンツ削除のURL取得
        $urlUserDelete = apiRun::getApiUrl(CommonConstant::URL_USER_SYSTEM_DELETE);
        //API A019と連結し、実装する      
        $resultA019 = apiRun::runURL($urlUserDelete, $params);
        if (isset($resultA019['status']) && $resultA019['status'] == CommonConstant::STATUS_SUCCESS) {
            $sessionUser->successUserDelete = $translator->translate('DELETE_DONE');
            return $this->pagingAction();
        } else {
            $errorMessage = $resultA019['errmsg'];
            $result = new JsonModel(array(
                'error' => $errorMessage
            ));

            return $result;
        }
    }
    
    /*
     * ユーザー情報を取得する。API AU024を使う。
     * @return array()|null
     */
    public function getUserInfoByAU024($userId)
    {
        $result = array();
        $result['user_info'] = array();
        $result['errMsg'] = '';

        $token = $this->getLoginInfoSv()->getToken();
        $params = array();
        $params['token'] = $token;
        $params['user_id'] = (int)$userId;

        //ユーザー情報のURL取得
        $urlUserInfo = apiRun::getApiUrl(CommonConstant::URL_OWNER);
        //API AU-024と連結し、実装する
        $resApiAU024 = apiRun::runURL($urlUserInfo, $params);        
        if ($resApiAU024['status'] == CommonConstant::STATUS_ERROR) {
            $resApiAU024['errMsg'] = $resApiAU024['errmsg'];   
            return $result;
        }

        if (count($resApiAU024) > 0) {           
            $result['user_info'] = $resApiAU024;
        }
        
        return $result;
    }
    
    /*
     * ユーザー情報を取得する。API AU021を使う。
     * @return array()|null
     */
    public function getUserInfo($userId)
    {
        $result = array();
        $result['user_info'] = array();
        $result['errMsg'] = '';

        $token = $this->getLoginInfoSv()->getToken();

        //ユーザー情報のURL取得
        $urlUserInfo = apiRun::getApiUrl(CommonConstant::URL_USER_LIST);
        //API AU-021と連結し、実装する
        $resApiAU021 = apiRun::runURL($urlUserInfo, array('token' => $token));        
        if ($resApiAU021['status'] == CommonConstant::STATUS_ERROR) {
            $result['errMsg'] = $resApiAU021['errMsg'];
            return $result;
        }
        
        if (count($resApiAU021['list'] > 0)) {
            foreach ($resApiAU021['list'] as $value) {
                if ($value['user_id'] == $userId) {
                    $result['user_info'] = $value;
                    return $result;
                }           
            }
        }                
    }
           
    /*
     * ユーザーーの一覧のデータを取得する。
     * @return array()|null
     */
    public function getListDataUser($permission, $offset)
    {
        $result = array();
        $result['list_user_info'] = array();
        $result['errMsg'] = null;
        $result['total_count'] = null;
        $result['count'] = null;
                
        $config = $this->getServiceLocator()->get('config');
        $token = $this->getLoginInfoSv()->getToken();
        $limit = $config['paging']['rowsPerPage'];

        $params = array();
        $params['token'] = $token;
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        
        switch ($permission) {
            case CommonConstant::ROLE_REGISTER_USER : //0
                $params['permission'] = CommonConstant::ROLE_REGISTER_USER;
                break;
            case CommonConstant::ROLE_GENERAL_USER : //1
                $params['permission'] = CommonConstant::ROLE_GENERAL_USER;
                break;
            case CommonConstant::ROLE_CONTENT_OWNER : //2
                $params['permission'] = CommonConstant::ROLE_CONTENT_OWNER;
                break;
            case CommonConstant::ROLE_CHANNEL_OWNER : //3
                $params['permission'] = CommonConstant::ROLE_CHANNEL_OWNER;
                break;
            case CommonConstant::ROLE_SYSTEM : //9
                $params['permission'] = CommonConstant::ROLE_SYSTEM;
                break;
            default:
                break;
        }

        //ログインのURL取得
        $urlUserList = apiRun::getApiUrl(CommonConstant::URL_USER_LIST);
        //API AU-014と連結し、実装する
        $resApiAU021 = apiRun::runURL($urlUserList, $params);

        if ($resApiAU021['status'] == CommonConstant::STATUS_ERROR) {
            $resApiAU021['errMsg'] = $resApiAU021['errmsg'];   
            return $result;
        }
        
        if (isset($resApiAU021['list']) && count($resApiAU021['list']) > 0) {           
            $result['total_count'] = $resApiAU021['totalcount'];
            $result['count'] = $resApiAU021['count'];
            $result['list_user_info'] = $resApiAU021['list'];
        }

        return $result;
    }

}
