<?php

namespace Omolink\Channel\Controller;

use Application\Application\Constant\CommonConstant;
use Application\Login\Service\LoginInfoService;
use Kdl\Api\Run as apiRun;
use Omolink\Channel\Form\ChannelForm;
use Omolink\Channel\Model\Channel;
use Omolink\Channel\Model\ChannelTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Paginator;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ChannelController extends AbstractActionController
{   
    /**
     * @var LoginInfoService 
     */
    protected $loginInfoSv;
    
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

    /*
     * チャネル一覧情報の取得アクション
     */
    public function listviewAction()
    {
        $errorMessage = null;
        $successMessage = null;
        $config = $this->getServiceLocator()->get('config');
        $channelTable = new ChannelTable();

        $sessionChannel = new Container('channel');

        $objListChannelData = $this->getListChannelData();
        if (count($objListChannelData['list_channel_data']) > 0) {
            $channelTable->setChannelData($objListChannelData['list_channel_data']);            
        }
        
        if (!is_null($objListChannelData['errorMessage'])) {
            $errorMessage = $objListChannelData['errorMessage'];
        }

        $paginator = new Paginator($channelTable);
        $itemCountPerPage = $config['paging']['rowsPerPage'];
        $pageRange = $config['paging']['pageRange'];
        $page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
        $orderBy = $this->params()->fromRoute('order_by');
        $sort = $this->params()->fromRoute('sort');
        $paginator->setCurrentPageNumber($page)
                  ->setItemCountPerPage($itemCountPerPage)
                  ->setPageRange($pageRange);    
 
        if (is_null($errorMessage) && !is_null($sessionChannel->errorDelete)){
            $errorMessage = $sessionChannel->errorDelete;
            $sessionChannel->errorDelete = null;
        }
        
        if (is_null($successMessage) && !is_null($sessionChannel->successDelete)) {
            $successMessage = $sessionChannel->successDelete;
            $sessionChannel->successDelete = null;
        }
        
        $this->getWillAndMemory($channelTable, ($page - 1) * $itemCountPerPage, $itemCountPerPage);
        
        return new ViewModel(array(
            'paginator' => $paginator,
            'order_by' => $orderBy ? $orderBy : 'name',
            'order' => $sort ? $sort : 'ASC',
            'data' => $channelTable->getItems(($page - 1) * $itemCountPerPage, $itemCountPerPage),
            'errorMessage' => $errorMessage,
            'successMessage' => $successMessage,
        ));                
    }    
    
    /*
     * 改ページアクション
     */
    public function pagingAction() 
    {
        $channelTable = new ChannelTable();
        $sessionChannel = new Container('channel');
        $objListChannelData = $this->getListChannelData();
        if (count($objListChannelData['list_channel_data']) > 0) {
            $channelTable->setChannelData($objListChannelData['list_channel_data']);
        }     

        $paginator = new Paginator($channelTable);
        $config = $this->getServiceLocator()->get('config');
        $itemCountPerPage = $config['paging']['rowsPerPage'];
        $pageRange = $config['paging']['pageRange'];

        $page = $this->params()->fromQuery('page');
        $orderBy = $this->params()->fromQuery('order_by');
        $order = $this->params()->fromQuery('order');
        
        $rows = $channelTable->getItems((($page ? $page : 1) - 1) * $itemCountPerPage, $itemCountPerPage);
        if ($page > 1 && count($rows) == 0) {
            $page--;
            $rows = $channelTable->getItems((($page ? $page : 1) - 1) * $itemCountPerPage, $itemCountPerPage);
        }
        
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
                'route' => 'channel',
                'action' => 'paging',
                'tableId' => 'listId',
                'order_by' => $orderBy,
                'order' => $order,
            ));
        
        $this->getWillAndMemory($channelTable, ($page - 1) * $itemCountPerPage, $itemCountPerPage);        
        
        $rowHTML = '<tr>
                        <td class="text-left"><a href="%s">%s</a></td>
                        <td class="text-left">%s</td>
                        <td class="text-center">%s</td>
                        <td class="text-center">%s</td>
                        <td class="text-center"><a href="javascript:void(0)" onclick="onShowContentList(%s)">%s</a></td>
                        <td class="text-center">
                            <a class="btn btn-contens-delete" href="%s" onclick="onDeleteChannelClick(%s);">削除</a>
                        </td>
                    </tr>';
        
        $data = array();
        foreach ($rows as $row) {
            $url = $this->url()->fromRoute('channel', array('action' => 'update', 'id' => $row['channel_id']));
            $channelName = isset($row['name']) ? $row['name'] : null;
            $description = isset($row['description']) ? $row['description'] : null;
            $will = isset($row['will_count']) ? $row['will_count'] : '';
            $memory = isset($row['memory_count']) ? $row['memory_count'] : '';
            $channelId = $row['channel_id'];
            $contentName = 'コンテンツ一覧';
            
            $data[] = sprintf($rowHTML, $url, $channelName, $description, $will, $memory, $channelId, $contentName, '#', $row['channel_id']);
        }

        $message = '';
        if (isset($sessionChannel->successDelete) && $sessionChannel->successDelete != '') {
            $message = $sessionChannel->successDelete;
            $sessionChannel->successDelete = null;
        }
        
        $resultData = array(
	    'data' => $data,
            'paginator' => $paginationHTML,
            'message' => $message
        );
        
        if (isset($sessionChannel->isUserChannel)) {
            $resultData['isUserChannel'] = $sessionChannel->isUserChannel ? 1 : 0;
            $sessionChannel->isUserChannel = null;
        }
            
        $result = new JsonModel($resultData);

        return $result;
    }
    
    /*
     * チャネル登録アクション
     */
    public function insertAction() {
        $form = new ChannelForm();
        $channel = new Channel();
        $config = $this->getServiceLocator()->get('config');
        $token = $this->getLoginInfoSv()->getToken();
        $userId = $this->getLoginInfoSv()->getUserId();
        $errorMessage = null;
        
        if ($this->getLoginInfoSv()->isRegisteChannelMenu()) {
            $this->redirect()->toRoute('content', array('action' => 'listview'));
        }
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($channel->getInputFilter());
            $form->setData(array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()));
            if ($form->isValid()) {
                $data = $form->getData();

                $params = array();
                $params['token'] = $token;
                $params['name'] = $data['channelName'];
                $params['description'] = $data['channelDescription'];
                $params['channel_owner_id'] = $userId;
                
                //チャネル登録のURL取得
                $urlChannelRegister = apiRun::getApiUrl(CommonConstant::URL_CHANNEL_NEW);
                //API CH001と連結し、実装する
                $resApiCH001 = apiRun::runURL($urlChannelRegister, $params);
                if (isset($resApiCH001['status']) && $resApiCH001['status'] == CommonConstant::STATUS_SUCCESS) {
                    $paramsCH008 = array();
                    $paramsCH008['token'] = $token;
                    $paramsCH008['channel_id'] = $resApiCH001['channel_id'];
                    //チャネルのイメージ更新のURL取得
                    $urlChannelImageUpdate = apiRun::getApiUrl(CommonConstant::URL_CHANNEL_FILE_UPDATE);

                    //API CH008と連結し、実装する
                    if ($data['fileBannerImgPhoto']['name'] != '') {
                        //バナー画像
                        $paramsCH008['type'] = 1;
                        $imagePath = $config['thumb']['path'] . $data['fileBannerImgPhoto']['name'];
                        move_uploaded_file($data['fileBannerImgPhoto']['tmp_name'], $imagePath);
                        $file['image'] = array('tmp_name' => $imagePath, 
                                                'type' => $data['fileBannerImgPhoto']['type'],
                                                'name' => $data['fileBannerImgPhoto']['name']);
                        $resApiCH008 = apiRun::runUploadURL($urlChannelImageUpdate, $paramsCH008, $file);
                        if ($resApiCH008['status'] == CommonConstant::STATUS_ERROR) {
                            $errorMessage = $resApiCH008['errmsg'];
                        }
                        unlink($imagePath);
                    }
                    
                    if (is_null($errorMessage) && $data['fileFrameOnImagePhoto']['name'] != '') {
                        //コンテンツフレーム上画像
                        $paramsCH008['type'] = 2;
                        $imagePath = $config['thumb']['path'] . $data['fileFrameOnImagePhoto']['name'];
                        move_uploaded_file($data['fileFrameOnImagePhoto']['tmp_name'], $imagePath);
                        $file['image'] = array('tmp_name' => $imagePath, 
                                                'type' => $data['fileFrameOnImagePhoto']['type'],
                                                'name' => $data['fileFrameOnImagePhoto']['name']);
                        $resApiCH008 = apiRun::runUploadURL($urlChannelImageUpdate, $paramsCH008, $file);
                        if ($resApiCH008['status'] == CommonConstant::STATUS_ERROR) {
                            $errorMessage = $resApiCH008['errmsg'];
                        }
                        unlink($imagePath);
                    }
                    
                    if (is_null($errorMessage) && $data['fileFrameUnderImagePhoto']['name'] != '') {
                        //コンテンツフレーム下画像
                        $paramsCH008['type'] = 3;
                        $imagePath = $config['thumb']['path'] . $data['fileFrameUnderImagePhoto']['name'];
                        move_uploaded_file($data['fileFrameUnderImagePhoto']['tmp_name'], $imagePath);
                        $file['image'] = array('tmp_name' => $imagePath, 
                                                'type' => $data['fileFrameUnderImagePhoto']['type'],
                                                'name' => $data['fileFrameUnderImagePhoto']['name']);
                        $resApiCH008 = apiRun::runUploadURL($urlChannelImageUpdate, $paramsCH008, $file);
                        if ($resApiCH008['status'] == CommonConstant::STATUS_ERROR) {
                            $errorMessage = $resApiCH008['errmsg'];
                        }
                        unlink($imagePath);
                    }
                    
                    if (is_null($errorMessage) && $data['fileFrameIconImagePhoto']['name'] != '') {
                        //コンテンツアイコン画像
                        $paramsCH008['type'] = 4;
                        $imagePath = $config['thumb']['path'] . $data['fileFrameIconImagePhoto']['name'];
                        move_uploaded_file($data['fileFrameIconImagePhoto']['tmp_name'], $imagePath);
                        $file['image'] = array('tmp_name' => $imagePath, 
                                                'type' => $data['fileFrameIconImagePhoto']['type'],
                                                'name' => $data['fileFrameIconImagePhoto']['name']);
                        $resApiCH008 = apiRun::runUploadURL($urlChannelImageUpdate, $paramsCH008, $file);
                        if ($resApiCH008['status'] == CommonConstant::STATUS_ERROR) {
                            $errorMessage = $resApiCH008['errmsg'];
                        }
                        unlink($imagePath);
                    }       

                    if (is_null($errorMessage)) {
                        $sessionContent = new Container('Content');
                        $sessionContent->successUpdate = 'INSERT_DONE';
                        return $this->redirect()->toRoute('content', array('action' => 'listview'));
                    }
                }
                else if ($resApiCH001['status'] == CommonConstant::STATUS_ERROR) {
                    $errorMessage = $resApiCH001['errmsg'];
                }
            }
        }

        return new ViewModel(array(
            'form' => $form,
            'errorMessage' => $errorMessage,
        ));
    }
    
    /*
     * チャネル更新アクション
     */
    public function updateAction() {
        $channelIdFromUrl = (int)$this->params()->fromRoute('id');
        $config = $this->getServiceLocator()->get('config');
        $token = $this->getLoginInfoSv()->getToken();
        $userId = $this->getLoginInfoSv()->getUserId();
        $errorMessage = null;
        $successMessage = null;
        
        if ($this->getLoginInfoSv()->getPermission() != CommonConstant::ROLE_SYSTEM && !$this->getLoginInfoSv()->isRegisteChannelMenu()) {
            $this->redirect()->toRoute('content', array('action' => 'listview'));
        }

        $form = new ChannelForm();
        $channel = new Channel();   
        $channelSession = new Container('channel');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($channel->getInputFilter());
            $form->setData(array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()));
            if ($form->isValid()) {
                $data = $form->getdata();
                
                $params = array();
                $params['token'] = $token;
                $params['channel_id'] = $channelIdFromUrl;
                $params['name'] = $data['channelName'];
                $params['description'] = $data['channelDescription'];
                $params['channel_owner_id'] = $userId;

                //チャネル更新のURL取得
                $urlChannelUpdate = apiRun::getApiUrl(CommonConstant::URL_CHANNEL_UPDATE);
                //API CH007と連結し、実装する
                $resApiCH007 = apiRun::runURL($urlChannelUpdate, $params);
                if (isset($resApiCH007['status']) && $resApiCH007['status'] == CommonConstant::STATUS_SUCCESS) {
                    $paramsCH008 = array();
                    $paramsCH008['token'] = $token;
                    $paramsCH008['channel_id'] = $channelIdFromUrl;
                    //チャネルのイメージ更新のURL取得
                    $urlChannelImageUpdate = apiRun::getApiUrl(CommonConstant::URL_CHANNEL_FILE_UPDATE);

                    //API CH008と連結し、実装する
                    if ($data['fileBannerImgPhoto']['name'] != '') {
                        //バナー画像
                        $paramsCH008['type'] = 1;
                        $imagePath = $config['thumb']['path'] . $data['fileBannerImgPhoto']['name'];
                        move_uploaded_file($data['fileBannerImgPhoto']['tmp_name'], $imagePath);
                        $file['image'] = array('tmp_name' => $imagePath, 
                                                'type' => $data['fileBannerImgPhoto']['type'],
                                                'name' => $data['fileBannerImgPhoto']['name']);
                        $resApiCH008 = apiRun::runUploadURL($urlChannelImageUpdate, $paramsCH008, $file);
                        if ($resApiCH008['status'] == CommonConstant::STATUS_ERROR) {
                            $errorMessage = $resApiCH008['errmsg'];
                        }
                        unlink($imagePath);
                    }
                    
                    if (is_null($errorMessage) && $data['fileFrameOnImagePhoto']['name'] != '') {
                        //コンテンツフレーム上画像
                        $paramsCH008['type'] = 2;
                        $imagePath = $config['thumb']['path'] . $data['fileFrameOnImagePhoto']['name'];
                        move_uploaded_file($data['fileFrameOnImagePhoto']['tmp_name'], $imagePath);
                        $file['image'] = array('tmp_name' => $imagePath, 
                                                'type' => $data['fileFrameOnImagePhoto']['type'],
                                                'name' => $data['fileFrameOnImagePhoto']['name']);
                        $resApiCH008 = apiRun::runUploadURL($urlChannelImageUpdate, $paramsCH008, $file);
                        if ($resApiCH008['status'] == CommonConstant::STATUS_ERROR) {
                            $errorMessage = $resApiCH008['errmsg'];
                        }
                        unlink($imagePath);
                    }
                    
                    if (is_null($errorMessage) && $data['fileFrameUnderImagePhoto']['name'] != '') {
                        //コンテンツフレーム下画像
                        $paramsCH008['type'] = 3;
                        $imagePath = $config['thumb']['path'] . $data['fileFrameUnderImagePhoto']['name'];
                        move_uploaded_file($data['fileFrameUnderImagePhoto']['tmp_name'], $imagePath);
                        $file['image'] = array('tmp_name' => $imagePath, 
                                                'type' => $data['fileFrameUnderImagePhoto']['type'],
                                                'name' => $data['fileFrameUnderImagePhoto']['name']);
                        $resApiCH008 = apiRun::runUploadURL($urlChannelImageUpdate, $paramsCH008, $file);
                        if ($resApiCH008['status'] == CommonConstant::STATUS_ERROR) {
                            $errorMessage = $resApiCH008['errmsg'];
                        }
                        unlink($imagePath);
                    }
                    
                    if (is_null($errorMessage) && $data['fileFrameIconImagePhoto']['name'] != '') {
                        //コンテンツアイコン画像
                        $paramsCH008['type'] = 4;
                        $imagePath = $config['thumb']['path'] . $data['fileFrameIconImagePhoto']['name'];
                        move_uploaded_file($data['fileFrameIconImagePhoto']['tmp_name'], $imagePath);
                        $file['image'] = array('tmp_name' => $imagePath, 
                                                'type' => $data['fileFrameIconImagePhoto']['type'],
                                                'name' => $data['fileFrameIconImagePhoto']['name']);
                        $resApiCH008 = apiRun::runUploadURL($urlChannelImageUpdate, $paramsCH008, $file);
                        if ($resApiCH008['status'] == CommonConstant::STATUS_ERROR) {
                            $errorMessage = $resApiCH008['errmsg'];
                        }
                        unlink($imagePath);
                    }
                    
                    if (is_null($errorMessage)) {
                        $channelSession->successUpdate = 'UPDATE_DONE';
                        return $this->redirect()->toRoute('channel', array('action' => 'update', 'id' => $channelIdFromUrl));
                    }
                }
                else if ($resApiCH007['status'] == CommonConstant::STATUS_ERROR) {
                    $errorMessage = $resApiCH007['errmsg'];
                }              
            }            
        }
        else 
        {
            $params = array();
            $params['token'] = $token;
            $params['channel_id_list'] = array((int)$channelIdFromUrl);

            //チャネル詳細のURL取得
            $urlChannelListInfo = apiRun::getApiUrl(CommonConstant::URL_CHANNEL_INFO);
            //API CH004と連結し、実装する
            $resApiCH004 = apiRun::runURL($urlChannelListInfo, $params);
            if (isset($resApiCH004['status']) && $resApiCH004['status'] == CommonConstant::STATUS_SUCCESS) {
                $channelId = $resApiCH004['list'][0]['channel_id'];
                $channelName = $resApiCH004['list'][0]['name'];
                $description = $resApiCH004['list'][0]['description'];
                $bannerUrl = null;
                if (isset($resApiCH004['list'][0]['banner_url'])) {
                    $bannerUrl = $this->getImageData($resApiCH004['list'][0]['banner_url']);
                }
                $frameTopUrl = null;
                if (isset($resApiCH004['list'][0]['frame_top_url'])) {
                    $frameTopUrl = $this->getImageData($resApiCH004['list'][0]['frame_top_url']);
                }
                $frameBottomUrl = null;
                if (isset($resApiCH004['list'][0]['frame_bottom_url'])) {
                    $frameBottomUrl = $this->getImageData($resApiCH004['list'][0]['frame_bottom_url']);
                }
                $iconUrl = null;
                if (isset($resApiCH004['list'][0]['icon_url'])) {
                    $iconUrl = $this->getImageData($resApiCH004['list'][0]['icon_url']);
                }

                $channelData = array(
                    'channelId' => $channelId,
                    'channelName' => $channelName,
                    'channelDescription' => $description,
                    'bannerImage' => $bannerUrl,
                    'frameOnImage' => $frameTopUrl,
                    'frameUnderImage' => $frameBottomUrl,
                    'frameIconImage' => $iconUrl,
                );
                
                $form->get('channelId')->setValue($channelId);
                $form->get('channelName')->setValue($channelName);
                $form->get('channelDescription')->setValue($description);
                $channelSession->channelData = $channelData;   
                
                if (is_null($successMessage) && !is_null($channelSession->successUpdate)) {
                    $successMessage = $channelSession->successUpdate;
                    $channelSession->successUpdate = null;
                }
            }            
        }

        return new ViewModel(array(
            'channelId' => $channelIdFromUrl,
            'form' => $form,
            'channelData' => $channelSession->channelData,
            'successMessage' => $successMessage,
            'errorMessage' => $errorMessage,
        ));
    }
    
    private function getImageData($url) {
        //get image data
        $result = apiRun::getFileURL($url, array('token' => $this->getLoginInfoSv()->getToken()));

        $imageData = base64_encode($result);
        return 'data: application/octet-stream;base64,' . $imageData;
    }
    
    /*
     * 選択されたチャネル情報を削除するアクション
     */
    public function deleteAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $channelIdFromUrl = (int)$this->params()->fromRoute('id');
        $token = $this->getLoginInfoSv()->getToken();
                      
       	$sessionChannel = new Container('channel');
	$sessionChannel->errorDelete = null;
	$sessionChannel->successDelete = null;
        $errorMessage = null;
        
        $userChannelId = $this->getLoginInfoSv()->isRegisteChannelMenu();
        if (isset($userChannelId['channel_id'])) {
            $userChannelId = $userChannelId['channel_id'];
        } else {
            $userChannelId = 0;
        }
        
        $params = array();
        $params['token'] = $token;
        $params['channel_id'] = $channelIdFromUrl;   
        

        //チャネル削除のURL取得
        $urlChannelDelete = apiRun::getApiUrl(CommonConstant::URL_CHANNEL_DELETE);
        //API CH010と連結し、実装する      
        $resultCH010 = apiRun::runURL($urlChannelDelete, $params);
        if (isset($resultCH010['status']) && $resultCH010['status'] == CommonConstant::STATUS_SUCCESS) {
            $sessionChannel->successDelete = $translator->translate('DELETE_DONE');
            if ($userChannelId > 0) {
                $sessionChannel->isUserChannel = $userChannelId == $channelIdFromUrl;
            }
            return $this->pagingAction();
        }
        else if ($resultCH010['status'] == CommonConstant::STATUS_ERROR) {
            $errorMessage = $resultCH010['errmsg'];
            $result = new JsonModel(array(
                'error' => $errorMessage
            ));

            return $result;
        }
    }
    
    public function contentsAction() {
        $contentTable = new \Omolink\Content\Model\ContentTable();
        $config = $this->getServiceLocator()->get('config');
        $itemCountPerPage = 10;//$config['paging']['rowsPerPage'];
        $pageRange = $config['paging']['pageRange'];

        $page = $this->params()->fromQuery('page');
        $orderBy = $this->params()->fromQuery('order_by');
        $order = $this->params()->fromQuery('order');
        $channelId = !is_null($this->params()->fromRoute('id')) ? 
            (int)$this->params()->fromRoute('id') : (int)$this->params()->fromQuery('chid');

        $token = $this->getLoginInfoSv()->getToken();
        
        $errorMessage = null;
        
        //call CH005
        $resApiCH005 = apiRun::runURL(apiRun::getApiUrl(CommonConstant::URL_CHANNEL_CONTENTS), 
            array('token' => $token, 'channel_id' => $channelId, 
                'activation_status' =>array(CommonConstant::ACTIVATION_FLG_9)));
        $contentListId = array();
        if ($resApiCH005['status'] == CommonConstant::STATUS_SUCCESS) {                        
            foreach ($resApiCH005['list'] as $value) {
                $contentListId[] = $value['content_id'];
            }
        }

        if (count($contentListId) > 0) {
            //コンテンツ詳細のURL取得
            $urlContentListDetail = apiRun::getApiUrl(CommonConstant::URL_CONTENT_ID);
            $params = array();
            $params['token'] = $token;
            $params['content_id_list'] = $contentListId;

            //API C005と連結し、実装する
            $resApiC005 = apiRun::runURL($urlContentListDetail, $params);
            if ($resApiC005['status'] == CommonConstant::STATUS_SUCCESS) {
                if (isset($resApiC005['list']) && count($resApiC005['list']) > 0) {
                    $contentTable->setContentTable($resApiC005['list'], count($resApiC005['list']));
                }
            }
            else if ($resApiC005['status'] == CommonConstant::STATUS_ERROR) {
                $errorMessage = $resApiC005['errmsg'];
            }
        }
        
        if (!is_null($errorMessage) || $contentTable->isEmpty()) {
            $result = new JsonModel(array(
                'data' => array(),
                'paginator' => ''
            ));
            return $result;
        }
        
        $paginator = new Paginator($contentTable);        
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
                'route' => 'channel',
                'action' => 'contents',
                'tableId' => 'contentList',
                'order_by' => $orderBy,
                'order' => $order,
                'addParamCallback' => 'addParamContentsCallback',
                'doneCallback' => 'pagingContentsDone',
            ));
       
        $rows = $contentTable->getItems(($page - 1) * $itemCountPerPage, $itemCountPerPage);
        $rowHTML = '<tr>
                        <td class="text-left"><a href="%s">%s</a></td>
                        <td class="text-center">%s</td>
                    </tr>';        
        
        $data = array();
        foreach ($rows as $row) {
            $url = $this->url()->fromRoute('content', array('action' => 'update', 'id' => $row['content_id']));
            $data[] = sprintf($rowHTML, $url, $row['name'], $row['create_user_id']);            
        }
        
        $sessionContent = new Container('Content');
        $message = '';
        if (isset($sessionContent->successContentStatus) && $sessionContent->successContentStatus != '') {
            $message = $sessionContent->successContentStatus;
            $sessionContent->successContentStatus = null;
        }

        $result = new JsonModel(array(
	    'data' => $data,
            'paginator' => $paginationHTML,
            'message' => $message
        ));

        return $result;
    }
    
    /*
     * チャネル一覧情報を取得する。
     * @return array()|null
     */
    public function getListChannelData()
    {
        $result = array();
        $result['list_channel_data'] = null;
        $result['errorMessage'] = null;
        
        $token = $this->getLoginInfoSv()->getToken();

        //チャネル一覧のURL取得
        $urlChannelList = apiRun::getApiUrl(CommonConstant::URL_CHANNEL_LIST);
        //API CH003と連結し、実装する
        $resApiCH003 = apiRun::runURL($urlChannelList, array('token' => $token));
        if (isset($resApiCH003['status']) && $resApiCH003['status'] == CommonConstant::STATUS_SUCCESS) {
            //チャネル詳細の取得
            $channelListId = array();
            if (isset($resApiCH003['list']) && count($resApiCH003['list']) > 0) {
                foreach ($resApiCH003['list'] as $value) {
                    $channelListId[] = $value['channel_id'];
                }

                $params = array();
                $params['token'] = $token;
                $params['channel_id_list'] = $channelListId;

                //チャネル詳細のURL取得
                $urlChannelListInfo = apiRun::getApiUrl(CommonConstant::URL_CHANNEL_INFO);
                //API CH004と連結し、実装する
                $resApiCH004 = apiRun::runURL($urlChannelListInfo, $params);
                if (isset($resApiCH004['status']) && $resApiCH004['status'] == CommonConstant::STATUS_SUCCESS) {
                    if (isset($resApiCH004['list']) && count($resApiCH004['list']) > 0) {
                        $result['list_channel_data'] = $resApiCH004['list'];                            
                    }
                }
                else if ($resApiCH004['status'] == CommonConstant::STATUS_ERROR) {
                    $result['errorMessage'] = $resApiCH004['errmsg'];
                }                  
            }
        }
        else if ($resApiCH003['status'] == CommonConstant::STATUS_ERROR) {
            $result['errorMessage'] = $resApiCH003['errmsg'];
        }        

        return $result;
    }
    
    private function getWillAndMemory($channelTable, $offset, $rows) {
        $channelIds = $channelTable->getChannelIds();
        $token = $this->getLoginInfoSv()->getToken();
        foreach ($channelIds as $index => $channelId) {
            if ($index < $offset || $index >= $offset + $rows) {
                continue;
            }
            $params = array();
            $params['token'] = $token;
            $params['channel_id'] = $channelId;
                
            //うぃる数
            $urlChannelWill = apiRun::getApiUrl(CommonConstant::URL_CHANNEL_WILL);
            //API CH013と連結し、実装する
            $resApiCH013 = apiRun::runURL($urlChannelWill, $params);
            if (isset($resApiCH013['status']) && $resApiCH013['status'] == CommonConstant::STATUS_SUCCESS) {
                $channelTable->setWill($index, $resApiCH013['will_count']);
            } else  {
                $channelTable->setWill($index, '');
            }
            
            //うぃる数
            $urlChannelMemory = apiRun::getApiUrl(CommonConstant::URL_CHANNEL_MEMORY);
            //API CH014と連結し、実装する
            $resApiCH014 = apiRun::runURL($urlChannelMemory, $params);
            if (isset($resApiCH014['status']) && $resApiCH014['status'] == CommonConstant::STATUS_SUCCESS) {
                $channelTable->setMemory($index, $resApiCH014['memory_count']);
            } else  {
                $channelTable->setMemory($index, '');
            }
        }
    }
}
