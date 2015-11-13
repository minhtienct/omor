<?php

namespace Kdl\PhpConfig;

class PhpConfig
{
    
    /**
     * Maximum allowed size for uploaded files.
     * @return type
     */
    public function getUploadMaxFileSize()
    {
        return $this->convertToBytes(ini_get('upload_max_filesize')); 
    }
    
    /**
     * Maximum number of files that can be uploaded via a single request
     * @return type
     */
    public function getMaxFileUploads()
    {
        return ini_get('max_file_uploads');
    }
    
    /**
     * Maximum size of POST data that PHP will accept.
     * @return type
     */
    public function getPostMaxSize()
    {
        return $this->convertToBytes(ini_get('post_max_size'));
    }
    
    /**
     * Convert MB, GB,.. to bytes.
     * @param type $size
     * @return type
     */
    function convertToBytes($size)
    {
        //Example: 128M
        $number = substr($size, 0, -1);
        if (!is_numeric($number)) {
            throw new \Exception("Info config file upload not match.");
        }
        switch (strtoupper(substr($size, -1)))
        {
            case "K":
                return $number * 1024;
            case "M":
                return $number * pow(1024, 2);
            case "G":
                return $number * pow(1024, 3);
            case "T":
                return $number * pow(1024, 4);
            case "P":
                return $number * pow(1024, 5);
            default:
                return $size;
        }
    }
}
