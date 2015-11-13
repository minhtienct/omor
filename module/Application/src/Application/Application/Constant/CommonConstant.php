<?php

namespace Application\Application\Constant;

class CommonConstant
{
    //==================== ユーザー権限の定義 =====================
    const ROLE_REGISTER_USER = 0;
    const ROLE_GENERAL_USER = 1;
    const ROLE_CONTENT_OWNER = 2;
    const ROLE_CHANNEL_OWNER = 3;
    const ROLE_SYSTEM = 9;
    //============================================================
    

    //==================== APIを実装した後の状態定義 ===============
    const STATUS_SUCCESS = 0;
    const STATUS_ERROR = -1;
    //============================================================

    
    //==================== チャネルへの登録申請状態の定義 ==========   
    const ACTIVATION_STATUS_NOT_APPROVED = 0;
    const ACTIVATION_STATUS_DENIAL = 8;
    const ACTIVATION_STATUS_APPROVED = 9;
    //============================================================


    //==================== アクティベーションフラグの定義 ==========   
    const ACTIVATION_FLG_0 = 0;
    const ACTIVATION_FLG_1 = 1;
    const ACTIVATION_FLG_2 = 2;
    const ACTIVATION_FLG_3 = 3;
    const ACTIVATION_FLG_4 = 4;
    const ACTIVATION_FLG_8 = 8;
    const ACTIVATION_FLG_9 = 9;
    //============================================================

    
    //==================== APIのURLの定義 =====================   
    const URL_MAIN = 'https://stg-kujstsrv.kobedigitallabo.com/omolink/1/';
    const URL_LOGIN = 'login';
    const URL_LOGOUT = 'logout';
    const URL_ANONYMOUS_REGISTER = 'anonymous/register';
    const URL_USER_INFO = 'user';
    const URL_USER_LIST = 'user/list';
    const URL_USER_SYSTEM_DELETE = 'user/system/delete';
    const URL_USER_SYSTEM_UPDATE = 'user/system/update';
    const URL_OWNER = 'owner';
    const URL_OWNER_REGISTER = 'owner/register';
    const URL_OWNER_UPDATE = 'owner/update';
    const URL_CONTENT_LIST = 'contents/list';
    const URL_CONTENT_LIST_ALL = 'contents/list/all';
    const URL_CONTENT_ID = 'contents/id';
    const URL_CONTENT_NEW = 'contents/new';
    const URL_CONTENT_UPDATE = 'contents/update';
    const URL_CONTENT_FILE_NEW = 'contents/file/new';
    const URL_CONTENT_FILE_DELETE = 'contents/file/delete';
    const URL_CONTENT_DELETE = 'contents/delete';
    const URL_CONTENT_SUMMARY = 'contents/summary';
    const URL_CONTENT_FACEBOOK = 'contents/facebook';
    const URL_CHANNEL_LIST = 'channel/list';
    const URL_CHANNEL_INFO = 'channel/info';
    const URL_CHANNEL_SEARCH = 'channel/search';
    const URL_CHANNEL_NEW = 'channel/new';
    const URL_CHANNEL_UPDATE = 'channel/update';
    const URL_CHANNEL_DELETE = 'channel/delete';
    const URL_CHANNEL_FILE_UPDATE = 'channel/file/update';
    const URL_CHANNEL_CONTENTS = 'channel/contents';
    const URL_CHANNEL_CONTENTS_ADD = 'channel/contents/add';
    const URL_CHANNEL_CONTENTS_UPDATE = 'channel/contents/update';
    const URL_CHANNEL_CONTENTS_REMOVE = 'channel/contents/remove';
    const URL_CHANNEL_WILL = 'channel/will';
    const URL_CHANNEL_MEMORY = 'channel/memory';
    
    
    //============================================================    
}

class CommonValue
{
    /*
     * チャネルへの登録申請状態によって、表示名を変更する。
     */
    public static function getNameActivationStattus($status)
    {
        if ($status === CommonConstant::ACTIVATION_STATUS_NOT_APPROVED) {
            return '未承認';
        } else if ($status === CommonConstant::ACTIVATION_STATUS_DENIAL) {
            return '拒否';
        } else if ($status === CommonConstant::ACTIVATION_STATUS_APPROVED) {
            return '承認済み';
        }
        
        return '';
    }
}



