<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\File;

use Error;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Lib\Image\ImageTools;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FileUtil {

    const THUMB_EMPTY = 'empty';
    const THUMB_CORRUPT = 'corrupt';

    /**
     * @param string $extension
     * @return string
     */
    public static function getHTTPHeader($extension)
    {
        switch($extension)
        {
            case 'doc'    : { return "Content-Type: document/word"; break;}
            case 'xls'    : { return "Content-Type: document/xls"; break;}
            case 'pdf'    : { return "Content-Type: application/pdf"; break;}
            case 'mp3'    : { return "Content-Type: audio/mp3"; break;}
            case 'bmp'    : { return "Content-Type: image/bmp"; break;}
            case 'zip'    : { return "Content-Type: archive/zip"; break;}

            case 'docx'   : { return "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document"; break;}
            case 'dotx'   : { return "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.template"; break;}
            case 'pptx'   : { return "Content-Type: application/vnd.openxmlformats-officedocument.presentationml.presentation"; break;}
            case 'ppsx'   : { return "Content-Type: application/vnd.openxmlformats-officedocument.presentationml.slideshow"; break;}
            case 'potx'   : { return "Content-Type: application/vnd.openxmlformats-officedocument.presentationml.template"; break;}
            case 'xlsx'   : { return "Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"; break;}
            case 'xltx'   : { return "Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.template"; break;}

            case 'odt'    : { return "Content-Type: application/vnd.oasis.opendocument.text, application/x-vnd.oasis.opendocument.text"; break;}
            default       : { return "Content-Type: file"; break;}
        }
    }

    /**
     * @param string $extension
     * @return string|null
     */
    public static function getMIMEType($extension)
    {

        // our list of mime types
        $mime_types = array(
            "pdf"=>"application/pdf"
            ,"exe"=>"application/octet-stream"
            ,"zip"=>"application/zip"
            ,"docx"=>"application/msword"
            ,"doc"=>"application/msword"
            ,"xls"=>"application/vnd.ms-excel"
            ,"ppt"=>"application/vnd.ms-powerpoint"
            ,"gif"=>"image/gif"
            ,"png"=>"image/png"
            ,"jpeg"=>"image/jpg"
            ,"jpg"=>"image/jpg"
            ,"mp3"=>"audio/mpeg"
            ,"wav"=>"audio/x-wav"
            ,"mpeg"=>"video/mpeg"
            ,"mpg"=>"video/mpeg"
            ,"mpe"=>"video/mpeg"
            ,"mov"=>"video/quicktime"
            ,"avi"=>"video/x-msvideo"
            ,"3gp"=>"video/3gpp"
            ,"css"=>"text/css"
            ,"jsc"=>"application/javascript"
            ,"js"=>"application/javascript"
            ,"php"=>"text/html"
            ,"htm"=>"text/html"
            ,"html"=>"text/html"
        );

        return @$mime_types[strtolower($extension)];
    }

    /**
     * @param FileProxyInterface $file
     * @param int $imgWidth
     * @return bool|string
     */
    public static function getThumbnail(FileProxyInterface $file, $imgWidth = 80) {

        if($file->isEmpty())
            return self::createDefaultThumbnail(self::THUMB_EMPTY);

        $imgTool = new ImageTools();

        $ret = false;

        $tempFile = tempnam(Cool::getInstance()->getFactory()->getSettingsManager()->getTempFolder(),'TMP');
        $file->toFile($tempFile);

        $tempTarget = tempnam(Cool::getInstance()->getFactory()->getSettingsManager()->getTempFolder(),'PIC');

        if(in_array( strtolower($file->getExtension()), array('jpg','png','gif','bmp'))) {
            $jpgQuality = 80;

            if($imgTool->createThumb($tempFile, $tempTarget, $imgWidth, $jpgQuality))
               $ret = $tempTarget;
            else $ret = self::createDefaultThumbnail(self::THUMB_CORRUPT);

        }elseif($file->getExtension()=='pdf') {

            $file->toFile($tempFile);
            shell_exec($cm = "convert '{$tempFile}[0]' -resize \"{$imgWidth}x{$imgWidth}>\" -flatten -colorspace 'rgb' 'jpg:$tempTarget' 2>/dev/null");

            if(filesize($tempTarget)>0)
                $ret = $tempTarget;
            else $ret = self::createDefaultThumbnail(self::THUMB_CORRUPT);
        }

        @unlink($tempFile);
        if($ret!=$tempTarget)
            @unlink($tempTarget);

        return $ret;
    }

    /**
     * @param string $type
     * @return string
     */
    private static function createDefaultThumbnail($type) {
        $tempImage = tempnam(Cool::getInstance()->getFactory()->getSettingsManager()->getTempFolder(),'PIC');
        switch($type) {
            case self::THUMB_EMPTY      : copy( Cool::getInstance()->getFactory()->getFileLocator()->locate('@EulogixCoolCoreBundle/Resources/public/res/gfx/system/preview-empty.jpg'), $tempImage); break;
            case self::THUMB_CORRUPT    : copy( Cool::getInstance()->getFactory()->getFileLocator()->locate('@EulogixCoolCoreBundle/Resources/public/res/gfx/system/preview-corrupt.jpg'), $tempImage); break;
        }
        return $tempImage;
    }

}