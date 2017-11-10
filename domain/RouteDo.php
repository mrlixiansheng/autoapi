<?php

namespace autoapi\domain;
use autoapi\exception\Abort;
use autoapi\g\GFun;
use autoapi\util\HumpUtil;
use ReflectionClass;

class RouteDo
{

     function getControllerPath(&$c){
         $d=DIRECTORY_SEPARATOR;
         $c->path= $c->ns.$d;
         if ($c->second!=false) $c->path.= $c->second.$d;
         $c->path.= 'controllers'.$d;
         if ($c->mid!=false)  $c->path.= $c->mid.$d;
         $c->path.=ucwords($c->controller).'Controller';
    }



    function reflect(&$c){
      // isset($c->urls[2])?$uc2=$c->urls[2]:GFun::abort('df') ;
        $reflector=  new ReflectionClass($c->path);
        $instance= $reflector->newInstance();
        if (isset($uc2)&&$reflector->hasMethod($uc2)){
            $method= $reflector->getMethod($uc2);
            $method->invoke($instance);
        }else {
            $method= $reflector->getMethod('index');
            $method->invoke($instance);
        }
    }




    function defaultRoute(&$c){
        if ('default'!==$c->ns)return;
        $c->uri='admin/home/index/index';
    }

    function uriToArray($c){
        if ($c->params[0]!=false) $c->uri=strstr($c->uri,'&',true);
        $c->uri= trim($c->uri,'/.');
        $hump=new HumpUtil();
        $hump->lineToHump($c->uri);
        $c->uri=explode('/', $c->uri);
    }

    function getParams(&$c){
        $c->uri= parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $c->params= explode('&',trim(strstr($c->uri,'&'),'&'));
    }

    function routes(&$c){
        if ($c->uri[0]=='')return $c->uri='admin/controller/IndexController';
        $explodes=&$c->uri;
        $path='';
      $array_walk_recursive_route=function ($item, $key)use(&$path,$explodes) {
        if (sizeof($explodes)-2==$key)
            $path.= $item.DIRECTORY_SEPARATOR;

            if (0===strpos($path,GFun::adminNs())&&$key===0) $path.='controllers'.DIRECTORY_SEPARATOR;
            if (0===strpos($path,GFun::addonNs())&&$key===1) $path.='controllers'.DIRECTORY_SEPARATOR;
        };
        array_walk_recursive($explodes,$array_walk_recursive_route);
    }


}