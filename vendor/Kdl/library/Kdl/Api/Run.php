<?php

namespace Kdl\Api;

use Application\Application\Constant\CommonConstant;
use CURLFile;

class Run
{   
    public static function runURL($url, $params) {
//        Kankeisei_Model_Logging::debug('start', __METHOD__);
//        // セッションに格納されているライブラリよりクライアントIDを取得する
//        $session = new Zend_Session_Namespace(self::$_Namespace);
//        $params['clientid'] = !is_null($this->userId) ? $this->userId : $session->clientId[$this->requestId];
//        $urlEngine = $this->kankeiseiapiModel->getUrlEngineByClientId($params['clientid']);
//        $url = $urlEngine . $url;

        $data_string = json_encode($params);

        $ch = curl_init($url);
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
                                            'Content-Type: application/json',
                                            'Content-Length: ' . strlen($data_string)
                                            ));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        $result = json_decode(self::objectToArray($response), true);

        $debugInfo = ' ' . $url . ', PARAMS: ' . $data_string;
        // Error handling
        if (curl_errno($ch)) {
            $result = array('result' => false, 'message' => curl_errno($ch));
//            Kankeisei_Model_Logging::error($result['message'] . $debugInfo, __METHOD__);
//            Kankeisei_Model_Logging::debug('end', __METHOD__);
            curl_close($ch);
            return null;
        }
        // check valid json or not
        if (json_last_error() != JSON_ERROR_NONE) {
            $result = array('result' => false, 'message' => $response);
//            Kankeisei_Model_Logging::error($result['message'] . $debugInfo, __METHOD__);
//            Kankeisei_Model_Logging::debug('end', __METHOD__);
            curl_close($ch);
            return null;
        }
        // Check json result
        if (isset($result['result']) && $result['result'] == false) {
            //$result = array('result' => false, 'message' => $result['message']);
//            Kankeisei_Model_Logging::error($response . $debugInfo, __METHOD__);
        }

        curl_close($ch);

//        Kankeisei_Model_Logging::debug(Zend_Registry::get('msgCustom')->API->request_ok . $debugInfo, __METHOD__);
//        Kankeisei_Model_Logging::debug('end', __METHOD__);

        return $result;
    }
    
    public static function runUploadURL($url, $params, $files) {
        foreach($files as $key => $v) {
            $cfile = new CURLFile($v['tmp_name'], $v['type'], $v['name']);
            $params[$key] = $cfile;
        }

        $ch = curl_init($url);
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array("Content-Type: multipart/form-data; charset=utf-8"));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        $result = json_decode(self::objectToArray($response), true);

        $debugInfo = ' ' . $url . ', PARAMS: ' . json_encode($params);
        // Error handling
        if (curl_errno($ch)) {
            $result = array('result' => false, 'message' => curl_errno($ch));
//            Kankeisei_Model_Logging::error($result['message'] . $debugInfo, __METHOD__);
//            Kankeisei_Model_Logging::debug('end', __METHOD__);
            curl_close($ch);
            return null;
        }
        // check valid json or not
        if (json_last_error() != JSON_ERROR_NONE) {
            $result = array('result' => false, 'message' => $response);
//            Kankeisei_Model_Logging::error($result['message'] . $debugInfo, __METHOD__);
//            Kankeisei_Model_Logging::debug('end', __METHOD__);
            curl_close($ch);
            return null;
        }
        // Check json result
        if (isset($result['result']) && $result['result'] == false) {
            $result = array('result' => false, 'message' => $result['message']);
//            Kankeisei_Model_Logging::error($response . $debugInfo, __METHOD__);
        }

        curl_close($ch);

//        Kankeisei_Model_Logging::debug(Zend_Registry::get('msgCustom')->API->request_ok . $debugInfo, __METHOD__);
//        Kankeisei_Model_Logging::debug('end', __METHOD__);

        return $result;
    }
       
    public static function getFileURL($url, $params) {
        $data_string = json_encode($params);

        $ch = curl_init($url);
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
                                            'Content-Type: application/json',
                                            'Content-Length: ' . strlen($data_string)
                                            ));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        curl_close($ch);
        
        return $response;
    }


    public static function objectToArray($d) {
        if (is_object($d)) {
            $d = get_object_vars($d);
        }

        if (is_array($d)) {
            return array_map(__FUNCTION__, $d);
        }
        return $d;
    }
    
    /*
     * APIのURLを取得する。
     * @param string $urlName
     */
    public static function getApiUrl($urlName) {
        $urlMain = CommonConstant::URL_MAIN . $urlName;        
        return $urlMain;       
    }
}
