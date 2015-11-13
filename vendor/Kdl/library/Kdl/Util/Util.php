<?php

namespace Kdl\Util;

class Util
{
    /**
     * Convert string
     * @param type $str
     * @return type
     */
    public static function convUTF8ToCP932($str)
    {
        return iconv('UTF-8', 'CP932', $str);
    }
    
    /**
     * Convert string
     * @param type $str
     * @return type
     */
    public static function convCP932ToUTF8($str)
    {
        return iconv('CP932', 'UTF-8', $str);
    }
    
    /**
     * Check a file is valid uploaded
     * @param type $file
     * @return boolean
     */
    public static function isFileUploaded($file)
    {
        if (!isset($file) || $file['error'] == UPLOAD_ERR_NO_FILE) {
            return false;
        }
        return true;
    }
}
