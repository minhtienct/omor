<?php

namespace Omolink\Report\Controller;

use Application\Application\Constant\CommonConstant;
use Omolink\Report\Form\ReportForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Kdl\Api\Run as apiRun;

class ReportController extends AbstractActionController
{   
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
     * レポート情報のロードアクション
     */
    public function listviewAction()
    {
        $errorMessage = null;
        $form = new ReportForm();

        $token = $this->getLoginInfoSv()->getToken();
        $contentIdFromUrl = (int)$this->params()->fromRoute('id');
        $reportData = array();
        
        $params = array();
        $params['token'] = $token;
        $params['content_id_list'] = array($contentIdFromUrl);
        
        //コンテンツ情報のURL取得
        $urlContentListId = apiRun::getApiUrl(CommonConstant::URL_CONTENT_ID);
        //API CC003と連結し、実装する
        $resApiCC005 = apiRun::runURL($urlContentListId, $params);
        if (isset($resApiCC005['status']) && $resApiCC005['status'] == CommonConstant::STATUS_SUCCESS) {
            //get owner name
            $ownerId = $resApiCC005['list'][0]['create_user_id'];
            $ownerName = '';
            //コンテンツ情報のURL取得
            $urlOwnerURL = apiRun::getApiUrl(CommonConstant::URL_OWNER);
            //API CC003と連結し、実装する
            $resApiAU024 = apiRun::runURL($urlOwnerURL, array('token' => $token, 'user_id' => $ownerId));
            if (isset($resApiAU024['status']) && $resApiAU024['status'] == CommonConstant::STATUS_SUCCESS) {
                $ownerName = isset($resApiAU024['owner_name']) ? $resApiAU024['owner_name'] : '';
            }
            
            $paramsCC011 = array();
            $paramsCC011['token'] = $token;
            $paramsCC011['content_id'] = $contentIdFromUrl;
            
            //コンテンツレポート情報のURL取得
            $urlReport = apiRun::getApiUrl(CommonConstant::URL_CONTENT_SUMMARY);
            //API CC011と連結し、実装する
            $resApiCC011 = apiRun::runURL($urlReport, $paramsCC011);
            if (isset($resApiCC011['status']) && $resApiCC011['status'] == CommonConstant::STATUS_SUCCESS) {
                //TODO: ket qua tra ve chua du neen con set them
                $contentId = $resApiCC005['list'][0]['content_id'];
                $contentName = $resApiCC005['list'][0]['name'];
                $willCount = $resApiCC011['will_count'];
                $omoCount = $resApiCC011['omo_count'];
                $rateAverage = isset($resApiCC011['rate_average']) ? $resApiCC011['rate_average'] : 0;
                $willSexList = $resApiCC011['will_sex_list'];
                $willAgeList = $resApiCC011['will_age_list'];
                $omoSexList = $resApiCC011['omo_sex_list'];
                $omoAgeList = $resApiCC011['omo_age_list'];
                
                $sexList = array('0' => '不明', '1' => '男性', '2' => '女性');
                $ageList = array('0' => '不明', '1' => '10歳未満', '10' => '10代', '20' => '20代', '30' => '30代', '40' => '40代', '50' => '50代', '60' => '60歳以上');

                if (count($willSexList) > 0) {
                    $tmp = array();
                    foreach ($sexList as $k => $v) {
                        $f = false;
                        foreach ($willSexList as $will) {
                            if ($will['sex'] == $k) {
                                $tmp[$k] = "['" . $v . "'," . $will['count'] . "]";
                                $f = true;
                                break;
                            }
                        }
                        if (!$f) {
                            $tmp[$k] = "['" . $v . "',0]";
                        }
                    }
                    $willSexList = '[' . implode(',', $tmp) . ']';
                } else {
                    $willSexList = '[]';
                }
                
                if (count($willAgeList) > 0) {
                    $tmp = array();
                    foreach ($ageList as $k => $v) {
                        $f = false;
                        foreach ($willAgeList as $will) {
                            if ($will['age'] == $k) {
                                $tmp[$k] = "['" . $v . "'," . $will['count'] . "]";
                                $f = true;
                                break;
                            }
                        }
                        if (!$f) {
                            $tmp[$k] = "['" . $v . "',0]";
                        }
                    }
                    $willAgeList = '[' . implode(',', $tmp) . ']';
                } else {
                    $willAgeList = '[]';
                }
                
                if (count($omoSexList) > 0) {
                    $tmp = array();
                    foreach ($sexList as $k => $v) {
                        $f = false;
                        foreach ($omoSexList as $omo) {
                            if ($omo['sex'] == $k) {
                                $tmp[] = "['" . $v . "'," . $omo['count'] . "]";
                                $f = true;
                                break;
                            }
                        }
                        if (!$f) {
                            $tmp[$k] = "['" . $v . "',0]";
                        }
                    }
                    $omoSexList = '[' . implode(',', $tmp) . ']';
                } else {
                    $omoSexList = '[]';
                }

                if (count($omoAgeList) > 0) {
                    $tmp = array();
                    foreach ($ageList as $k => $v) {
                        $f = false;
                        foreach ($omoAgeList as $omo) {
                            if ($omo['age'] == $k) {
                                $tmp[$k] = "['" . $v . "'," . $omo['count'] . "]";
                                $f = true;
                                break;
                            }
                        }
                        if (!$f) {
                            $tmp[$k] = "['" . $v . "',0]";
                        }
                    }
                    $omoAgeList = '[' . implode(',', $tmp) . ']';
                } else {
                    $omoAgeList = '[]';
                }
            
                $reportData = array(
                    'contentId' => $contentId,
                    'contentName' => $contentName,
                    'ownerName' => $ownerName,
                    'willCount' => $willCount,
                    'omoCount' => $omoCount,
                    'rateAverage' => $rateAverage,
                    'willSexList' => $willSexList,
                    'willAgeList' => $willAgeList,
                    'omoSexList' => $omoSexList,
                    'omoAgeList' => $omoAgeList,
                );

                $form->get('contentId')->setValue($contentId);
//                $form->get('contentName')->setValue($contentName);     
            }
            else if ($resApiCC011['status'] == CommonConstant::STATUS_ERROR) {
                $errorMessage = $resApiCC011['errmsg'];
            }            
        }
        else if ($resApiCC005['status'] == CommonConstant::STATUS_ERROR) {
            $errorMessage = $resApiCC005['errmsg'];
        }
        
        return new ViewModel(array(
            'contentlId' => $contentIdFromUrl,
            'form' => $form,
            'reportData' => $reportData,
            'errorMessage' => $errorMessage,
        ));        
    }
}
