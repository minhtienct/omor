<?php

namespace Kdl\Image;

class Converter
{
    /**
     * image Quality of jpg
     */
    const IMAGE_QUALITY_JPG  = 75;
    /**
     * image Quality of png
     */
    const IMAGE_QUALITY_PNG  = 9;
    
    /**
     * Algorithm from:
     * jQuery File Upload Plugin PHP Class 6.9.1
     * https://github.com/blueimp/jQuery-File-Upload
     * @param type $originalFile
     * @param type $maxWidthOrHeight
     * @return boolean
     */
    public static function createScaledImage($originalFilepath, $newFilepath,  $maxWidthOrHeight)
    {
        try {
            $rate = 10;
            if (!is_file($originalFilepath)) {
                throw new \Exception("File is not found.");
            }
            
            if (!function_exists('getimagesize')) {
                throw new \Exception("Function not found: getimagesize");
            }

            list($imgWidth, $imgHeight) = @getimagesize($originalFilepath);

            if (!$imgWidth || !$imgHeight) {
                return false;
            }


            if ($imgWidth > $imgHeight) {
                $maxWidth = $maxWidthOrHeight;
                $maxHeight = $maxWidthOrHeight * $rate;
            } else {
                $maxWidth = $maxWidthOrHeight * $rate;
                $maxHeight = $maxWidthOrHeight;
            }

            $scale = min(
                $maxWidth / $imgWidth, $maxHeight / $imgHeight
            );

            if ($scale >= 1) {
                if ($originalFilepath !== $newFilepath) {;
                    return copy($originalFilepath, $newFilepath);
                }
            }

            if (!function_exists('imagecreatetruecolor')) {
                throw new \Exception("Function not found: imagecreatetruecolor");
            }

            $newWidth = $imgWidth * $scale;
            $newHeight = $imgHeight * $scale;
            $dstX = 0;
            $dstY = 0;
            $newImg = imagecreatetruecolor($newWidth, $newHeight);
            $type = mime_content_type($originalFilepath);
            switch (strtolower(substr(strrchr($type, '/'), 1)))
            {
                case 'jpg':
                case 'jpeg':
                    $srcImg = @imagecreatefromjpeg($originalFilepath);
                    $writeImageFunction = 'imagejpeg';
                    $imageQuality = self::IMAGE_QUALITY_JPG;
                    break;
                case 'gif':
                    //imagecolortransparent($newImg, imagecolorallocate($newImg, 0, 0, 0));
                    $srcImg = @imagecreatefromgif($originalFilepath);
                    $writeImageFunction = 'imagegif';
                    $imageQuality = null;
                    break;
                case 'png':
                    imagecolortransparent($newImg, imagecolorallocate($newImg, 255, 255, 255));
                    imagealphablending($newImg, false);
                    imagesavealpha($newImg, true);
                    $srcImg = @imagecreatefrompng($originalFilepath);
                    $writeImageFunction = 'imagepng';
                    $imageQuality = self::IMAGE_QUALITY_PNG;
                    break;
                default:
                    @imagedestroy($newImg);
                    throw new \Exception("File type not match");
            }
            
            $success = false;

            if ($srcImg) {
                $success = imagecopyresampled(
                        $newImg, $srcImg, $dstX, $dstY, 0, 0, $newWidth, $newHeight, $imgWidth, $imgHeight
                    ) && $writeImageFunction($newImg, $newFilepath, $imageQuality); //Create file new
            }

            //Free up memory (imagedestroy does not delete files):
            @imagedestroy($srcImg);
            @imagedestroy($newImg);
            
            return $success;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * 
     * @param type $originalImage
     * @param type $fileName
     * @param type $outputImage
     * @param type $convertType
     * @return type
     * @throws \Exception
     */
    function convertImageByType($originalImage, $newFilepath, $convertType = 'jpeg')
    {
        try {
            
            if (!is_file($originalImage)) {
                throw new \Exception("File is not found.");
            }
            
            if (!function_exists('getimagesize')) {
                throw new \Exception("Function not found: getimagesize");
            }
            if (!function_exists('imagecreatetruecolor')) {
                throw new \Exception("Function not found: imagecreatetruecolor");
            }
            
            $type = mime_content_type($originalImage);
            $ext = strtolower(substr(strrchr($type, '/'), 1));
            if ($ext == $convertType) {
                if ($originalImage != $newFilepath) {
                    return copy($originalImage, $newFilepath);
                }
            }

            list($imgWidth, $imgHeight) = @getimagesize($originalImage);
            $newImg = imagecreatetruecolor($imgWidth, $imgHeight);

            switch ($ext)
            {
                case 'jpg':
                case 'jpeg':
                    $srcImg = @imagecreatefromjpeg($originalImage);
                    break;
                case 'gif':
                    $srcImg = @imagecreatefromgif($originalImage);
                    break;
                case 'png':
                    $white = imagecolorallocate($newImg, 255, 255, 255);
                    imagefilledrectangle($newImg, 0, 0, $imgWidth, $imgHeight, $white);
                    $srcImg = @imagecreatefrompng($originalImage);
                    break;
                default:
                    @imagedestroy($newImg);
                    throw new \Exception("File type not match");
            }

            $success = false;
            if ($srcImg) {
                #Output file
                switch ($convertType)
                {
                    case 'jpg':
                    case 'jpeg':
                        $writeImageFunction = 'imagejpeg';
                        $imageQuality = self::IMAGE_QUALITY_JPG;
                        break;
                    case 'gif':
                        $writeImageFunction = 'imagegif';
                        $imageQuality = null;
                        break;
                    case 'png':
                        $writeImageFunction = 'imagepng';
                        $imageQuality = self::IMAGE_QUALITY_PNG;
                        break;
                    default:
                        throw new \Exception("File convert type not match");
                }
                
              $success = imagecopyresampled($newImg, $srcImg, 0, 0, 0, 0, $imgWidth, $imgHeight, $imgWidth, $imgHeight)
                          && $writeImageFunction($newImg, $newFilepath, $imageQuality);
            }

            //Free up memory (imagedestroy does not delete files):
            @imagedestroy($srcImg);
            @imagedestroy($newImg);
            
            return $success;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

}
