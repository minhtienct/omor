<?php

namespace Omolink\Content\Controller;

use Application\Application\Constant\CommonConstant;
use Kdl\Api\Run as apiRun;
use Omolink\Content\Form\ContentForm;
use Omolink\Content\Model\Content;
use Omolink\Content\Model\ContentTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Paginator;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ContentController extends AbstractActionController
{
    const MAX_FILES_UPLOADED = 5;
    /**
     * ログイン情報
     * @var LoginInfoService
     */
    protected $loginInfoSv;

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

    /*
     * コンテンツ一覧情報の取得アクション
     */
    public function listviewAction()
    {
        $errorMessage = null;
        $successMessage = null;
	$sessionContent = new Container('Content');

        $config = $this->getServiceLocator()->get('config');
        $contentTable = new ContentTable();
        
        //if (!isset($sessionContent->lastTabIndex)) {
            $sessionContent->lastTabIndex = 1;
        //}

        $rows = $this->getData($contentTable, $sessionContent->lastTabIndex, $errorMessage);

        $paginator = new Paginator($contentTable);
        $itemCountPerPage = $config['paging']['rowsPerPage'];
        $pageRange = $config['paging']['pageRange'];

        $page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
        $orderBy = $this->params()->fromRoute('order_by');
        $sort = $this->params()->fromRoute('sort');

        $paginator->setCurrentPageNumber($page)
                    ->setItemCountPerPage($itemCountPerPage)
                    ->setPageRange($pageRange);

        if(is_null($errorMessage) && !is_null($sessionContent->errorUpdate)){
            $errorMessage = $sessionContent->errorUpdate;
            $sessionContent->errorUpdate = null;
        }

        if(is_null($errorMessage) && !is_null($sessionContent->errorDelete)){
            $errorMessage = $sessionContent->errorDelete;
            $sessionContent->errorDelete = null;
        }

        if (is_null($successMessage) && !is_null($sessionContent->successDelete)) {
            $successMessage = $sessionContent->successDelete;
            $sessionContent->successDelete = null;
        }

        if (is_null($successMessage) && !is_null($sessionContent->successUpdate)) {
            $successMessage = $sessionContent->successUpdate;
            $sessionContent->successUpdate = null;
        }

        return new ViewModel(array(
            'paginator' => $paginator,
            'order_by' => $orderBy ? $orderBy : 'name',
            'order' => $sort ? $sort : 'ASC',
            'data' => $rows,
            'errorMessage' => $errorMessage,
            'successMessage' => $successMessage,
            'isContentOwner' => $this->getLoginInfoSv()->getPermission() === CommonConstant::ROLE_CONTENT_OWNER,
            'tabIndex' => $sessionContent->lastTabIndex,
        ));
    }
    
    private function getData(&$contentTable, $tabIndex, &$errorMessage) {
        $userId = $this->getLoginInfoSv()->getUserId();
        $isContentOwner = $this->getLoginInfoSv()->getPermission() === CommonConstant::ROLE_CONTENT_OWNER;
        $token = $this->getLoginInfoSv()->getToken();
        $config = $this->getServiceLocator()->get('config');        
        $itemCountPerPage = $config['paging']['rowsPerPage'];        
        
        $page = $this->params()->fromQuery('page');
        if ($page == null) {
            $page = 1;
        }
        
        if ($tabIndex == 1) {
            //コンテンツ一覧のURL取得
            $urlContentList = apiRun::getApiUrl(CommonConstant::URL_CONTENT_LIST_ALL);
            //API C003と連結し、実装する
            $resApiC013 = apiRun::runURL($urlContentList, array('token' => $token, 'user_id' => $userId, 'offset' => ($page - 1) * $config['paging']['rowsPerPage'], 'limit' => $config['paging']['rowsPerPage']));

            if ($resApiC013['status'] == CommonConstant::STATUS_SUCCESS) {
                $total = $resApiC013['totalcount'];
                //コンテンツ詳細の取得
                $contentListId = array();
                if (isset($resApiC013['list']) && count($resApiC013['list']) > 0) {
                    $activeStatus = array();
                    foreach ($resApiC013['list'] as $value) {
                        $contentListId[] = $value['content_id'];
                        $activeStatus[$value['content_id']] = isset($value['activation_status']) ? $value['activation_status'] : '-';
                    }

                    //コンテンツ詳細のURL取得
                    $urlContentListDetail = apiRun::getApiUrl(CommonConstant::URL_CONTENT_ID);
                    $params = array();
                    $params['token'] = $token;
                    $params['content_id_list'] = $contentListId;

                    //API C005と連結し、実装する
                    $resApiC005 = apiRun::runURL($urlContentListDetail, $params);
                    if ($resApiC005['status'] == CommonConstant::STATUS_SUCCESS) {
                        if (isset($resApiC005['list']) && count($resApiC005['list']) > 0) {
                            foreach ($resApiC005['list'] as $k => $v) {
                                $resApiC005['list'][$k]['activate_status'] = $activeStatus[$v['content_id']];
                            }
                            $contentTable->setContentTable($resApiC005['list'], $total);
                        }
                    }
                    else if ($resApiC005['status'] == CommonConstant::STATUS_ERROR) {
                        $errorMessage = $resApiC005['errmsg'];
                    }
                }
            }
            else if ($resApiC013['status'] == CommonConstant::STATUS_ERROR) {
                $errorMessage = $resApiC013['errmsg'];
            }
        } else if (!$isContentOwner && ($tabIndex == 2 || $tabIndex == 3)) {
            //コンテンツ一覧のURL取得
            $urlChannelList = apiRun::getApiUrl(CommonConstant::URL_CHANNEL_SEARCH);
            //API CH012と連結し、実装する
            $resApiCH012 = apiRun::runURL($urlChannelList, array('token' => $token, 'channel_owner_id' => $this->getLoginInfoSv()->getUserId()));
            if ($resApiCH012['status'] == CommonConstant::STATUS_SUCCESS) {
                $contentListId = array();
                //loop channel list
                foreach ($resApiCH012['list'] as $channel) {
                    //call CH005
                    $resApiCH005 = apiRun::runURL(apiRun::getApiUrl(CommonConstant::URL_CHANNEL_CONTENTS),
                        array('token' => $token, 'channel_id' => (int)$channel['channel_id'],
                            'activation_status' => $tabIndex == 2 ? array(CommonConstant::ACTIVATION_FLG_9) : array(CommonConstant::ACTIVATION_FLG_0)));
                    if ($resApiCH005['status'] == CommonConstant::STATUS_SUCCESS) {
                        foreach ($resApiCH005['list'] as $value) {
                            $contentListId[] = $value['content_id'];
                        }
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
            } else {
                $errorMessage = $resApiCH012['errmsg'];
            }
        } else if ($tabIndex == 4) {
            //コンテンツ一覧のURL取得
            $urlContentList = apiRun::getApiUrl(CommonConstant::URL_CONTENT_LIST_ALL);
            //API C003と連結し、実装する
            $resApiC013 = apiRun::runURL($urlContentList, array('token' => $token));

            if ($resApiC013['status'] == CommonConstant::STATUS_SUCCESS) {
                //コンテンツ詳細の取得
                $contentListId = array();
                if (isset($resApiC013['list']) && count($resApiC013['list']) > 0) {
                    foreach ($resApiC013['list'] as $key => $value) {
                        $contentListId[] = $value['content_id'];
                    }

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
            }
            else if ($resApiC013['status'] == CommonConstant::STATUS_ERROR) {
                $errorMessage = $resApiC013['errmsg'];
            }
        }
        
        return $tabIndex == 1 ? $contentTable->getAllItems() : $contentTable->getItems(($page - 1) * $itemCountPerPage, $itemCountPerPage);
    }

    /*
     * 改ページのアクション
     */
    public function pagingAction()
    {
        $contentTable = new ContentTable();
        $config = $this->getServiceLocator()->get('config');        
        $translator = $this->getServiceLocator()->get('translator');
        $sessionContent = new Container('Content');
        $itemCountPerPage = $config['paging']['rowsPerPage'];
        $pageRange = $config['paging']['pageRange'];

        $page = $this->params()->fromQuery('page');
        $orderBy = $this->params()->fromQuery('order_by');
        $order = $this->params()->fromQuery('order');
        $tabIndex = $this->params()->fromQuery('tab');

        if (isset($sessionContent->goToLastPage) && $sessionContent->goToLastPage && $page > 1) {
            $page--;
            $sessionContent->goToLastPage = null;
        }
        $sessionContent->lastTabIndex = $tabIndex;

        $errorMessage = null;
        $rows = $this->getData($contentTable, $tabIndex, $errorMessage);
        

        if ($page > 1 && !is_null($rows) && count($rows) == 0) {
            $sessionContent->goToLastPage = true;
            return $this->pagingAction();
        }

        if (!is_null($errorMessage) || $contentTable->isEmpty()) {
            $message = '';
            if (isset($sessionContent->successContentStatus) && $sessionContent->successContentStatus != '') {
                $message = $sessionContent->successContentStatus;
                $sessionContent->successContentStatus = null;
            }

            $result = new JsonModel(array(
                'data' => array(),
                'paginator' => '',
                'message' => $translator->translate($message)
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
                'route' => 'content',
                'action' => 'paging',
                'tableId' => 'listId',
                'order_by' => $orderBy,
                'order' => $order,
                'addParamCallback' => 'paging',
            ));

        $rowHTML = '<tr>
                            <td><a href="%s">%s</a></td>
                            <td class="text-center">%s</td>
                            <td class="text-center">%s</td>
                            <td class="text-center">
                                <a class="btn btn-contens-delete" ctid="%s" href="%s" onclick="onDeleteContentClick(this);">削除</a>
                            </td>
                        </tr>';
        if ($tabIndex == 2) {
            $rowHTML = '<tr>
                            <td><a href="%s">%s</a></td>
                            <td class="text-center">%s</td>
                            <td class="text-center">%s</td>
                            <td class="text-center"><a href="/omolink/report/%s">レポート</a></td>
                            <td class="text-center">
                                <a class="btn btn-contens-delete" ctid="%s" chid="%s" href="%s" onclick="onApprovalCancelContentClick(this);">承認取消</a>
                            </td>
                        </tr>';
        }

        if ($tabIndex == 3) {
            $rowHTML = '<tr>
                            <td><a href="%s">%s</a></td>
                            <td class="text-center">%s</td>
                            <td class="text-center">%s</td>
                            <td class="text-center">
                                <a class="btn btn-contens-delete" ctid="%s" chid="%s" href="%s" onclick="onApprovalContentClick(this);">承認</a>
                            </td>
                            <td class="text-center">
                                <a class="btn btn-contens-delete" ctid="%s" chid="%s" href="%s" onclick="onDenialContentClick(this);">拒否</a>
                            </td>
                        </tr>';
        }
        if ($tabIndex == 4) {
            $rowHTML = '<tr>
                            <td><a href="%s">%s</a></td>
                            <td class="text-center">%s</td>
                        </tr>';
        }

        $data = array();
        foreach ($rows as $row) {
            $url = $this->url()->fromRoute('content', array('action' => 'update', 'id' => $row['content_id']));

            $startDate = isset($row['available_term_start']) && $row['available_term_start'] != '' ? date('Y/m/d', $row['available_term_start']) : '';
            $endDate = isset($row['available_term_end']) && $row['available_term_end'] != '' ? date('Y/m/d', $row['available_term_end']) : '';
            $expireString = '';
            if ($startDate != '' || $endDate != '') {
                $expireString = $startDate . ' ～ ' . $endDate;
            }

            if ($tabIndex == 1) {
                $data[] = sprintf($rowHTML, $url, $row['name'], $expireString,
                    \Application\Application\Constant\CommonValue::getNameActivationStattus($row['activate_status']), $row['content_id'], 'javascript:void(0)');
            }
            else if ($tabIndex == 2) {
                $data[] = sprintf($rowHTML, $url, $row['name'], $expireString,
                    $row['create_user_id'], $row['content_id'], $row['content_id'], $row['channel_id'], 'javascript:void(0)');
            }
            else if ($tabIndex == 3) {
                $data[] = sprintf($rowHTML, $url, $row['name'], $expireString,
                    $row['create_user_id'], $row['content_id'], $row['channel_id'], 'javascript:void(0)', $row['content_id'], $row['channel_id'], 'javascript:void(0)');
            }
            else if ($tabIndex == 4) {
                $data[] = sprintf($rowHTML, $url, $row['name'], $expireString);
            }
        }

        $message = '';
        if (isset($sessionContent->successContentStatus) && $sessionContent->successContentStatus != '') {
            $message = $sessionContent->successContentStatus;
            $sessionContent->successContentStatus = null;
        }

        $result = new JsonModel(array(
	    'data' => $data,
            'paginator' => $paginationHTML,
            'message' => $translator->translate($message)
        ));

        return $result;
    }

    public function genreChangedAction() {
        $id = $this->params()->fromQuery('id');
        switch ($id)
        {
            case '1':
                return new JsonModel(array(
                    'data' => array('居酒屋', '和食', '洋食', 'イタリアン', 'フレンチ', '中華', '焼肉', '韓国料理', ' アジアン', 'バー', 'ラーメン', 'お好み焼き・もんじゃ・鉄板焼き', 'その他')
                ));
            case '2':
                return new JsonModel(array(
                    'data' => array('スイーツ', 'パン', '紅茶', 'コーヒー', '抹茶・日本茶', 'その他')
                ));
            case '3':
                return new JsonModel(array(
                    'data' => array('展示会・ショー', 'スポーツ', '体験・遊覧', 'コンサート・ライブ', '舞台', 'その他')
                ));
            case '4':
                return new JsonModel(array(
                    'data' => array('自然', '神社仏閣', '建物・史跡', '動物園・公園', '美術館・博物館', '劇場・エンタメ', 'テーマパーク', 'アウトドア・スポーツ', '体験施設・教室 ショッピング', '温泉', 'その他')
                ));
            default:
                return new JsonModel(array(
                    'data' => array()
                ));
        }
    }

    /*
     * コンテンツ情報を登録するアクション
     */
    public function insertAction() {
        $form = new ContentForm();
        $config = $this->getServiceLocator()->get('config');
        $content = new Content();
        $error_message = null;
        $subGenreNameValue = '';

        $channels = $this->getChannelList();
        if (is_string($channels)) {
            $error_message = $channels;
            $channels = array();
        } else {
            $form->get('channel')->setAttributes(array('options' => $channels));
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($content->getInputFilter());
            $form->setData(array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()));
            if ($form->isValid()) {

                $data = $form->getData();

                $params = array();
                $params['token'] = $this->getLoginInfoSv()->getToken();
                if (trim($data['startExpirationDate']) != '') {
                    $params['available_term_start'] = (int)strtotime($data['startExpirationDate']);
                }
                if (trim($data['endExpirationDate']) != '') {
                    $params['available_term_end'] = (int)strtotime($data['endExpirationDate']);
                }
                $params['latitude'] = (float)$data['registerLocationLatitude'];
                $params['longitude'] = (float)$data['registerLocationLongitude'];
                $params['name'] = $data['contentName'];
                $params['genre_id'] = (int)$data['genreID'];
                $params['subgenre_name'] = $data['subGenreName'];
                $params['address'] = $data['address'];
                $params['phone'] = $data['phone'];
                $params['available_time'] = $data['dateEffective'];
                $params['budget_id'] = (int)$data['countId'];
                $params['capacity'] = (int)$data['numberSeat'];
                $params['url'] = $data['website'];
                $params['description'] = $data['overview'];
                $params['other'] = $data['remark'];
                $params['copyright'] = $data['copyright'];

                $formParams = $params;

                $url = apiRun::getApiUrl(CommonConstant::URL_CONTENT_NEW);

                $result = apiRun::runURL($url, $params);
                if (isset($result['status']) && $result['status'] == CommonConstant::STATUS_SUCCESS) {
                    $contentId = $result['content_id'];

                    //add to channel
                    $url = apiRun::getApiUrl(CommonConstant::URL_CHANNEL_CONTENTS_ADD);
                    $params = array();
                    $params['token'] = $this->getLoginInfoSv()->getToken();
                    $params['channel_id'] = $data['channel'];
                    $params['content_id_list'] = array($contentId);
                    $result = apiRun::runURL($url, $params);
                    if (isset($result['status']) && $result['status'] == CommonConstant::STATUS_SUCCESS) {
                        $err = false;
                        /*if ($data['rdFacebook'] > 0) {
                            //Facebook API
                            $url = apiRun::getApiUrl(CommonConstant::URL_CONTENT_FACEBOOK);
                            $params = array();
                            $params['token'] = $this->getLoginInfoSv()->getToken();
                            $params['content_id'] = $contentId;
                            $result = apiRun::runURL($url, $params);
                            if (!(isset($result['status']) && $result['status'] == CommonConstant::STATUS_SUCCESS)) {
                                //TODO
                                //$error_message = $result['errmsg'];
                                //$err = true;
                            }
                        }*/

                        if (!$err) {
                            //upload file
                            $url = apiRun::getApiUrl(CommonConstant::URL_CONTENT_FILE_NEW);

                            $params = array();
                            $params['token'] = $this->getLoginInfoSv()->getToken();
                            $params['content_id'] = $contentId;

                            $idList = array();
                            for ($order = 1; $order <= self::MAX_FILES_UPLOADED; $order++) {
                                for ($index = 1; $index <= self::MAX_FILES_UPLOADED; $index++) {
                                    if ($data['displayOrder' . $index] == $order && $data['imageContentFile' . $index]['name'] != '') {
                                        $imagePath = $config['thumb']['path'] . $data['imageContentFile' . $index]['name'];
                                        $thumbPath = $config['thumb']['path'] . '_' . $data['imageContentFile' . $index]['name'];
                                        move_uploaded_file($data['imageContentFile' . $index]['tmp_name'], $imagePath);

                                        //create thumb
                                        \Kdl\Image\Converter::createScaledImage($imagePath, $thumbPath, $config['thumb']['size']);

                                        $file['thumbnail'] = array('tmp_name' => $thumbPath,
                                            'type' => $data['imageContentFile' . $index]['type'],
                                            'name' => $data['imageContentFile' . $index]['name']);
                                        $file['image'] = array('tmp_name' => $imagePath,
                                            'type' => $data['imageContentFile' . $index]['type'],
                                            'name' => $data['imageContentFile' . $index]['name']);
                                        $result = apiRun::runUploadURL($url, $params, $file);

                                        //remove upload file
                                        unlink($imagePath);
                                        unlink($thumbPath);

                                        if (isset($result['status']) && $result['status'] == CommonConstant::STATUS_SUCCESS) {
                                            $idList[] = $result['file_id'];
                                        } else {
                                            $error_message = $result['errmsg'];
                                            break;
                                        }
                                    }
                                }
                            }

                            if (is_null($error_message)) {
                                //CC007
                                $url = apiRun::getApiUrl(CommonConstant::URL_CONTENT_UPDATE);
                                $formParams['content_id'] = $contentId;
                                $formParams['file_index'] = $idList;
                                $result = apiRun::runURL($url, $formParams);
                                if (isset($result['status']) && $result['status'] == CommonConstant::STATUS_SUCCESS) {
                                    $sessionContent = new Container('Content');
                                    $sessionContent->successUpdate = 'INSERT_DONE';
                                    return $this->redirect()->toRoute('content', array('action' => 'listview'));
                                } else {
                                    $error_message = $result['errmsg'];
                                }
                            }
                        }
                    } else {
                        $error_message = $result['errmsg'];
                    }
                } else {
                    $error_message = $result['errmsg'];
                }
            } else {
                $posts = $request->getPost()->toArray();
                $subGenreNameValue = $posts['subGenreName'];
            }
        } else {
            $form->get('startExpirationDate')->setValue(date('Y/m/d'));
            $form->get('endExpirationDate')->setValue(date('Y/m/d', strtotime('+10 years')));
        }

        return new ViewModel(array(
            'form' => $form,
            'error_message' => $error_message,
            'channels' => $channels,
            'subGenreNameValue' => $subGenreNameValue,
        ));
    }

    /*
     * 選択されたコンテンツ情報を更新するアクション
     */
    public function updateAction() {
        $contentId = $this->params()->fromRoute('id');
        $config = $this->getServiceLocator()->get('config');
        $contentListId = array();
        $contentListId[] = (int)$contentId;
        $url = apiRun::getApiUrl(CommonConstant::URL_CONTENT_ID);
        $subGenreNameValue = '';

        $form = new ContentForm();
        $content = new Content();
        $contentSession = new \Zend\Session\Container('content');
        $success = null;
        $error = null;
        $contentImage = array();
        $isSameUser = false;

        $channels = $this->getChannelList();
        if (is_string($channels)) {
            $error = $channels;
            $channels = array();
        } else {
            $form->get('channel')->setAttributes(array('options' => $channels));
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($content->getInputFilter());
            $posts = $request->getPost()->toArray();
            $form->setData(array_merge_recursive(
                $posts,
                $request->getFiles()->toArray()));
            $err = false;
            if ($posts['fileId1'] != '') {
                $form->getInputFilter()->get('imageContentFile1')->setRequired(false);
            }
            if ($form->isValid()) {
                $data = $form->getData();

                if ($data['channel'] !== $contentSession->apiData['channelId']) {
                    //remove content channel
                    $params = array();
                    $params['token'] = $this->getLoginInfoSv()->getToken();
                    $params['channel_id'] = (int)$contentSession->apiData['channelId'];
                    $params['content_id'] = (int)$data['contentId'];
                    $url = apiRun::getApiUrl(CommonConstant::URL_CHANNEL_CONTENTS_REMOVE);
                    $result = apiRun::runURL($url, $params);
                    if (isset($result['status']) && $result['status'] == CommonConstant::STATUS_SUCCESS) {
                        //add channel
                        $url = apiRun::getApiUrl(CommonConstant::URL_CHANNEL_CONTENTS_ADD);
                        $params = array();
                        $params['token'] = $this->getLoginInfoSv()->getToken();
                        $params['channel_id'] = $data['channel'];
                        $params['content_id_list'] = array($data['contentId']);
                        $result = apiRun::runURL($url, $params);
                        if (isset($result['status']) && $result['status'] == CommonConstant::STATUS_SUCCESS) {
                            // change channel done
                        } else {
                            $error = $result['errmsg'];
                            $err = true;
                        }
                    } else {
                        $error = $result['errmsg'];
                        $err = true;
                    }
                }

                /*if (!$err && $data['rdFacebook'] > 0) {
                    //Facebook API
                    $url = apiRun::getApiUrl(CommonConstant::URL_CONTENT_FACEBOOK);
                    $params = array();
                    $params['token'] = $this->getLoginInfoSv()->getToken();
                    $params['content_id'] = $data['contentId'];
                    $result = apiRun::runURL($url, $params);
                    if (!(isset($result['status']) && $result['status'] == CommonConstant::STATUS_SUCCESS)) {
                        //TODO
                        //$error = $result['errmsg'];
                        //$err = true;
                    }
                }*/

                if (!$err) {
                    if (!$err) {
                        $fileIds = array();
                        //upload file
                        $url = apiRun::getApiUrl(CommonConstant::URL_CONTENT_FILE_NEW);

                        $params = array();
                        $params['token'] = $this->getLoginInfoSv()->getToken();
                        $params['content_id'] = $data['contentId'];

                        for ($order = 1; $order <= self::MAX_FILES_UPLOADED; $order++) {
                            for ($index = 1; $index <= self::MAX_FILES_UPLOADED; $index++) {
                                if ($data['displayOrder' . $index] == $order && $data['imageContentFile' . $index]['name'] != '') {
                                    //delete old file
                                    if ($data['fileId' . $index] != '') {
                                        $tmpParams = array();
                                        $tmpParams['token'] = $this->getLoginInfoSv()->getToken();
                                        $tmpParams['content_id'] = $data['contentId'];
                                        $tmpParams['file_id'] = $data['fileId' . $index];
                                        $result = apiRun::runURL(apiRun::getApiUrl(CommonConstant::URL_CONTENT_FILE_DELETE), $tmpParams);
                                        if (!(isset($result['status']) && $result['status'] == CommonConstant::STATUS_SUCCESS)) {
                                            $error = $result['errmsg'];
                                            $err = true;
                                            break;
                                        }
                                    }
                                    //create thumb
                                    $imagePath = $config['thumb']['path'] . $data['imageContentFile' . $index]['name'];
                                    $thumbPath = $config['thumb']['path'] . '_' . $data['imageContentFile' . $index]['name'];
                                    move_uploaded_file($data['imageContentFile' . $index]['tmp_name'], $imagePath);

                                    \Kdl\Image\Converter::createScaledImage($imagePath, $thumbPath, $config['thumb']['size']);

                                    $file['thumbnail'] = array('tmp_name' => $thumbPath,
                                        'type' => $data['imageContentFile' . $index]['type'],
                                        'name' => $data['imageContentFile' . $index]['name']);
                                    $file['image'] = array('tmp_name' => $imagePath,
                                        'type' => $data['imageContentFile' . $index]['type'],
                                        'name' => $data['imageContentFile' . $index]['name']);
                                    $result = apiRun::runUploadURL($url, $params, $file);

                                    //remove upload file
                                    unlink($imagePath);
                                    unlink($thumbPath);

                                    if (isset($result['status']) && $result['status'] == CommonConstant::STATUS_SUCCESS) {
                                        $fileIds[] = $result['file_id'];
                                    } else {
                                        $error = $result['errmsg'];
                                        break;
                                    }
                                    break;
                                } else if ($data['displayOrder' . $index] == $order && $data['fileId' . $index] != '') {
                                    $fileIds[] = $data['fileId' . $index];
                                }
                            }
                            if ($err) {
                                break;
                            }
                        }
                        if (is_null($error)) {
                            //call CC007
                            $params = array();
                            $params['token'] = $this->getLoginInfoSv()->getToken();
                            $params['content_id'] = (int)$data['contentId'];
                            if (trim($data['startExpirationDate']) != '') {
                                $params['available_term_start'] = (int)strtotime($data['startExpirationDate']);
                            }
                            if (trim($data['endExpirationDate']) != '') {
                                $params['available_term_end'] = (int)strtotime($data['endExpirationDate']);
                            }
                            $params['latitude'] = (float)$data['registerLocationLatitude'];
                            $params['longitude'] = (float)$data['registerLocationLongitude'];
                            $params['name'] = $data['contentName'];
                            $params['genre_id'] = (int)$data['genreID'];
                            $params['subgenre_name'] = $data['subGenreName'];
                            $params['address'] = $data['address'];
                            $params['phone'] = $data['phone'];
                            $params['available_time'] = $data['dateEffective'];
                            $params['budget_id'] = (int)$data['countId'];
                            $params['capacity'] = (int)$data['numberSeat'];
                            $params['url'] = $data['website'];
                            $params['description'] = $data['overview'];
                            $params['other'] = $data['remark'];
                            $params['copyright'] = $data['copyright'];
                            $params['file_index'] = $fileIds;

                            $url = apiRun::getApiUrl(CommonConstant::URL_CONTENT_UPDATE);
                            $result = apiRun::runURL($url, $params);
                            if (isset($result['status']) && $result['status'] == CommonConstant::STATUS_SUCCESS) {
                                $sessionContent = new Container('Content');
                                $sessionContent->successUpdate = 'UPDATE_DONE';
                                return $this->redirect()->toRoute('content', array('action' => 'listview'));
                            } else {
                                $error = $result['errmsg'];
                                $success = false;
                            }
                        }
                    }
                }
            } else {
                $err = true;
                $posts = $request->getPost()->toArray();
                $subGenreNameValue = $posts['subGenreName'];
            }
            if ($err) {
                //reload images
                if (isset($contentSession->apiData['contentImage'])) {
                    $contentImage = $contentSession->apiData['contentImage'];
                }
                $isSameUser = $contentSession->apiData['isSameUser'];
            }
        } else {
            $resultC005 = apiRun::runURL($url, array('token' => $this->getLoginInfoSv()->getToken(),
                                                 'content_id_list' => $contentListId));
            if (isset($resultC005['status']) && $resultC005['status'] == CommonConstant::STATUS_SUCCESS) {
                $contentId = $resultC005['list'][0]['content_id'];
                $latitude = $resultC005['list'][0]['latitude'];
                $longitude = $resultC005['list'][0]['longitude'];
                $name = $resultC005['list'][0]['name'];
                $genreId = $resultC005['list'][0]['genre_id'];
                $available_term_start = isset($resultC005['list'][0]['available_term_start']) && $resultC005['list'][0]['available_term_start'] != '' ?
                    date('Y/m/d', $resultC005['list'][0]['available_term_start']) : '';
                $available_term_end = isset($resultC005['list'][0]['available_term_end']) && $resultC005['list'][0]['available_term_end'] != '' ?
                    date('Y/m/d', $resultC005['list'][0]['available_term_end']) : '';
                $subgenre_name = isset($resultC005['list'][0]['subgenre_name']) ? $resultC005['list'][0]['subgenre_name'] : '';
                $address = isset($resultC005['list'][0]['address']) ? $resultC005['list'][0]['address'] : '';
                $phone = isset($resultC005['list'][0]['phone']) ? $resultC005['list'][0]['phone'] : '';
                $available_time = isset($resultC005['list'][0]['available_time']) ? $resultC005['list'][0]['available_time'] : '';
                $budget_id = isset($resultC005['list'][0]['budget_id']) ? $resultC005['list'][0]['budget_id'] : '';
                $capacity = isset($resultC005['list'][0]['capacity']) ? $resultC005['list'][0]['capacity'] : '';
                $url =isset($resultC005['list'][0]['url']) ?  $resultC005['list'][0]['url'] : '';
                $description = isset($resultC005['list'][0]['description']) ? $resultC005['list'][0]['description'] : '';
                $other = isset($resultC005['list'][0]['other']) ? $resultC005['list'][0]['other'] : '';
                $copyright = isset($resultC005['list'][0]['copyright']) ? $resultC005['list'][0]['copyright'] : '';
                $channel_id = isset($resultC005['list'][0]['channel_id']) ?  $resultC005['list'][0]['channel_id'] : '';
                $create_user_id = isset($resultC005['list'][0]['create_user_id']) ?  $resultC005['list'][0]['create_user_id'] : '';
                if ($create_user_id != '' && $create_user_id == $this->getLoginInfoSv()->getUserId()) {
                    $isSameUser = true;
                }
                $subGenreNameValue = $subgenre_name;

                $fileIds = array();
                $ind = 1;
                foreach ($resultC005['list'][0]['file_list'] as $file) {
                    $fileIds[] = $file['file_id'];
                    $form->get('fileId' . $ind)->setValue($file['file_id']);
                    $ind++;
                }

                $data = array(
                    'registerLocationLatitude' => $latitude,
                    'registerLocationLongitude' => $longitude,
                    'contentName' => $name,
                    'genreID' => $genreId,
                    'genreName' => '',
                    'startExpirationDate' => $available_term_start,
                    'endExpirationDate' => $available_term_end,
                    'subGenreName' => $subgenre_name,
                    'address' => $address,
                    'phone' => $phone,
                    'dateEffective' => $available_time,
                    'countId' => $budget_id,
                    'numberSeat' => $capacity,
                    'website' => $url,
                    'overview' => $description,
                    'remark' => $other,
                    'copyright' => $copyright,
                    'channelName' => '',
                    'channelId' => $channel_id,
                    'isSameUser' => $isSameUser
                );
                $form->get('contentId')->setValue($contentId);
                $form->get('registerLocationLatitude')->setValue($latitude);
                $form->get('registerLocationLongitude')->setValue($longitude);
                $form->get('contentName')->setValue($name);
                $form->get('genreID')->setValue($genreId);
                $form->get('startExpirationDate')->setValue($available_term_start);
                $form->get('endExpirationDate')->setValue($available_term_end);
                $form->get('subGenreName')->setValue($subgenre_name);
                $form->get('address')->setValue($address);
                $form->get('phone')->setValue($phone);
                $form->get('dateEffective')->setValue($available_time);
                $form->get('countId')->setValue($budget_id);
                $form->get('numberSeat')->setValue($capacity);
                $form->get('website')->setValue($url);
                $form->get('overview')->setValue($description);
                $form->get('remark')->setValue($other);
                $form->get('copyright')->setValue($copyright);
                $form->get('channel')->setValue($channel_id);

                $index = 1;

                if (isset($resultC005['list'][0]['file_list'])) {
                    foreach ($resultC005['list'][0]['file_list'] as $image) {
                        //get image data
                        $resultC006 = apiRun::getFileURL($image['image_url'], array('token' => $this->getLoginInfoSv()->getToken()));

                        $imageData = base64_encode($resultC006);
                        $src = 'data: application/octet-stream;base64,' . $imageData;
                        $contentImage[] = $src;
                        $index++;
                    }
                    $data['contentImage'] = $contentImage;
                }

                $contentSession->apiData = $data;
            } else {
                $sessionContent = new Container('Content');
                $sessionContent->errorUpdate = $resultC005['errmsg'];
                return $this->redirect()->toRoute('content', array('action' => 'listview'));
            }
        }

        return new ViewModel(array(
            'form' => $form,
            'success' => $success,
            'error' => $error,
            'apiData' => $contentSession->apiData,
            'contentImage' => $contentImage,
            'channels' => $channels,
            'isSameUser' => $isSameUser,
            'subGenreNameValue' => $subGenreNameValue,
        ));
    }

    private function getChannelList() {
        $token = $this->getLoginInfoSv()->getToken();
        //チャネル一覧のURL取得
        $urlChannelList = apiRun::getApiUrl(CommonConstant::URL_CHANNEL_LIST);
        //API CH003と連結し、実装する
        $resApiCH003 = apiRun::runURL($urlChannelList, array('token' => $token));
        if (isset($resApiCH003['status']) && $resApiCH003['status'] == CommonConstant::STATUS_SUCCESS) {
            //チャネル詳細の取得
            $channelListId = array();
            if (isset($resApiCH003['list']) && count($resApiCH003['list']) > 0) {
                foreach ($resApiCH003['list'] as $key => $value) {
                    foreach ($value as $subKey => $subValue) {
                        $channelListId[] = $subValue;
                    }
                }

                $params = array();
                $params['token'] = $token;
                $params['channel_id_list'] = $channelListId;

                //チャネル詳細のURL取得
                $urlChannelListInfo = apiRun::getApiUrl(CommonConstant::URL_CHANNEL_INFO);
                //API CH004と連結し、実装する
                $resApiCH004 = apiRun::runURL($urlChannelListInfo, $params);//var_dump($resApiCH004);die;
                if (isset($resApiCH004['status']) && $resApiCH004['status'] == CommonConstant::STATUS_SUCCESS) {
                    $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
                    $escapeHtml = $viewHelperManager->get('escapeHtml');

                    $result = array();
                    $result[] = array('value' => '', 'label' => '選択してください');
                    foreach ($resApiCH004['list'] as $channel) {
                        $result[$channel['channel_id']] = array('value' => $escapeHtml($channel['channel_id']), 'label' => $escapeHtml($channel['name']));
                    }
                    return $result;
                }
                else if ($resApiCH004['status'] == CommonConstant::STATUS_ERROR) {
                    return $resApiCH004['errmsg'];
                }
            }
        }
        return array();
    }

    public function contentStatusAction() {
        $mode = $this->params()->fromQuery('mode');
        if ($mode === 'delete') {
            return $this->deleteContent($this->params()->fromQuery('ctid'));
        } else if ($mode === 'approvalCancel') {
            return $this->setContentStatus($this->params()->fromQuery('ctid'), $this->params()->fromQuery('chid'), CommonConstant::ACTIVATION_FLG_0);
        } else if ($mode === 'approval') {
            return $this->setContentStatus($this->params()->fromQuery('ctid'), $this->params()->fromQuery('chid'), CommonConstant::ACTIVATION_FLG_9);
        } else if ($mode === 'denial') {
            return $this->setContentStatus($this->params()->fromQuery('ctid'), $this->params()->fromQuery('chid'), CommonConstant::ACTIVATION_FLG_8);
        }
    }

    private function deleteContent($contentId) {
        $translator = $this->getServiceLocator()->get('translator');
        $token = $this->getLoginInfoSv()->getToken();

       	$sessionContent = new Container('Content');
        $errorMessage = null;

        $params = array();
        $params['token'] = $token;
        $params['content_id'] = (int)$contentId;

        //コンテンツ削除のURL取得
        $urlContentDelete = apiRun::getApiUrl(CommonConstant::URL_CONTENT_DELETE);
        //API C009と連結し、実装する
        $resultC009 = apiRun::runURL($urlContentDelete, $params);
        if (isset($resultC009['status']) && $resultC009['status'] == CommonConstant::STATUS_SUCCESS) {
            $sessionContent->successContentStatus = $translator->translate('DELETE_DONE');
            return $this->pagingAction();
        } else {
            $errorMessage = $resultC009['errmsg'];
            $result = new JsonModel(array(
                'error' => $errorMessage
            ));

            return $result;
        }
    }

    private function setContentStatus($contentId, $channelId, $status) {
        $token = $this->getLoginInfoSv()->getToken();

        $sessionContent = new Container('Content');
        $errorMessage = null;

        $params = array();
        $params['token'] = $token;
        $params['content_id'] = (int)$contentId;
        $params['channel_id'] = (int)$channelId;
        $params['activation_status'] = (int)$status;

        //コンテンツ削除のURL取得
        $urlContentUpdate = apiRun::getApiUrl(CommonConstant::URL_CHANNEL_CONTENTS_UPDATE);
        //API CH009と連結し、実装する
        $resultCH009 = apiRun::runURL($urlContentUpdate, $params);
        if (isset($resultCH009['status']) && $resultCH009['status'] == CommonConstant::STATUS_SUCCESS) {
            if ($status == CommonConstant::ACTIVATION_FLG_0) {
                $sessionContent->successContentStatus = '承認取消しました。';
            } else if ($status == CommonConstant::ACTIVATION_FLG_9) {
                $sessionContent->successContentStatus = '承認しました。';
            } else if ($status == CommonConstant::ACTIVATION_FLG_8) {
                $sessionContent->successContentStatus = '拒否しました。';
            }
            return $this->pagingAction();
        } else {
            $errorMessage = $resultCH009['errmsg'];
            $result = new JsonModel(array(
                'error' => $errorMessage
            ));

            return $result;
        }
    }
}
