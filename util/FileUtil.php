<?php


namespace autoapi\util;


class FileUtil
{
    public function createDir($dirName){
       if (!is_dir($dirName)) mkdir($dirName,777,true);
    }
    public function createFile($filePath){
        if (is_dir(dirname($filePath))&&!file_exists($filePath)){
            $f= fopen($filePath, 'w+');
            fwrite($f,'<?php echo __FILE__;');
            fclose($f);
        }
    }
}