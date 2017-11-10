<?php
namespace autoapi\controller;

use autoapi\exception\Abort;
use autoapi\util\FileUtil;
use autoapi\util\HumpUtil;

class Controller
{
     function render($view=null, $params = [],$ext='.php',$createDir=true){
        $trace=debug_backtrace();
        isset($trace[0],$trace[1])?0:new Abort('debug_backtrace');
        $defView=$trace[1]['function'];
        $viewDir=$this->getControllerViewDir($trace);
        $dirname=dirname(dirname($trace[0]['file']));
        $viewPath=$view===null?$defView:$view;
        $dirname=($dirname.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$viewDir.DIRECTORY_SEPARATOR);
        $filePath=$dirname.$viewPath.$ext;
        $this->createView($dirname,$filePath,$createDir);
        require_once $filePath;
    }


    function createView($dirname,$filePath,$createDir=true){
        if ($createDir){
            $f= new FileUtil();
            $f->createDir($dirname);
            $f->createFile($filePath);
        }
    }


    function getControllerViewDir($trace){
        $path= $trace[1]['class'];
        $file= basename($path);
        $h=new HumpUtil();
        $humpToLine=$h->humpToLine($file);
        $result=explode('-',$humpToLine);
        array_pop($result);
        array_shift($result);
        return  implode('-', $result);
    }



}