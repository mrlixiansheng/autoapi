<?php

namespace autoapi\domain;
use autoapi\g\GFun;
use ReflectionClass;

class RouteDo
{

     function admin(&$c){
        $urls=&$c->urls;
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri=trim($uri,'/.');
        $urls=explode('/',$uri);
        if ($urls[0]=='') $urls=explode('/','admin/home/index');
        $c->ns= $urls[0].'\\controllers\\'.ucwords($urls[1]).'Controller';
    }

    function addon(&$c){

    }

    function reflect(&$c){
       isset($c->urls[2])?$uc2=$c->urls[2]:GFun::abort('df') ;
        $reflector=  new ReflectionClass($c->ns);
        $instance= $reflector->newInstance();
        if (isset($uc2)&&$reflector->hasMethod($uc2)){
            $method= $reflector->getMethod($uc2);
            $method->invoke($instance);
        }else {
            $method= $reflector->getMethod('index');
            $method->invoke($instance);
        }
    }
}