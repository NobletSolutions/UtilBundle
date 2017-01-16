<?php

namespace NS\UtilBundle\Service;

use \Symfony\Component\HttpFoundation\File\UploadedFile;
use \Symfony\Component\HttpFoundation\Request;

/**
 * Description of File
 *
 * @author gnat
 */
class FileNamer
{
    /**
     * @var Toolkit
     */
    private $toolkit;
    /**
     * @var
     */
    private $request;

    /**
     * FileNamer constructor.
     */
    public function __construct()
    {
        $this->toolkit = new Toolkit();
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param UploadedFile $file
     * @param array $previous_files
     * @param bool|false $split_language
     * @return string
     */
    public function cleanFilename(UploadedFile $file, $previous_files = array(), $split_language = false)
    {
        $ext = '.'.$file->getClientOriginalExtension();
        $f   = $this->toolkit->stripText($this->stripExtension($file));

        if($this->request && $split_language === true) {
            $f .= '-' . $this->request->getLocale();
        }

        $x   = 0;
        $t   = $f.$ext;

        while (in_array($t, $previous_files)) {
            $x++;
            $t = $f . '-' . $x . $ext;
        }

        if($x > 0) {
            $f = $f . '-' . $x;
        }

        $previous_files[] = $f.$ext;

        return $f.$ext;
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function stripExtension(UploadedFile $file)
    {
        $fname = $file->getClientOriginalName();
        $fext  = $file->getClientOriginalExtension();

        return substr($fname, 0, strpos($fname, '.'.$fext));
    }

    /**
     * @param $err
     * @return string
     */
    public function getUploadErrorString($err)
    {
        $str = 'No match';
        switch($err)
        {
            case UPLOAD_ERR_OK:
                $str = 'File upload success.';
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $str = 'The file is larger than the maximum allowed filesize.';
                break;
            case UPLOAD_ERR_PARTIAL:
                $str = 'The file was only partially transfered.';
                break;
            case UPLOAD_ERR_NO_FILE:
                $str = 'No file was uploaded.';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $str = 'The server has no temporary directory to store the file.';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $str = 'The server was unable to write the file to the temporary directory.';
                break;
            case UPLOAD_ERR_EXTENSION:
                $str = 'The file was denied upload based on file extension.';
                break;
        }

        return $str;
    }
}
